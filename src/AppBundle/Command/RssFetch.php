<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;

class RssFetch extends ContainerAwareCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('rss:fetch')
             ->setDescription('Fetch latest RSS feeds')
             ->addArgument(
                 'id',
                 InputArgument::OPTIONAL,
                 'Which feed do you want to update?'
             );
    }

    /**
     * Command execution
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timeStart = microtime(true);

        $logger = $this->getContainer()->get('logger');
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $dbConnection = $em->getConnection();
        $feedRepository = $doctrine->getRepository('AppBundle:RssFeed');
        $articleRepository = $doctrine->getRepository('AppBundle:RssArticle');
        $categoryRepository = $doctrine->getRepository('AppBundle:RssCategory');

        $insertArticle = "INSERT INTO rss_articles (`rss_feed_id`, `title`, `alias`, `link`, `date`, `description`, `content`, `comments`, `insert_date`)
                          VALUES (:rss_feed_id, :title, :alias, :link, :date, :description, :content, :comments, :insert_date)";
        $insertCategory = "INSERT INTO rss_categories (`first_article_id`, `name`, `alias`, `count`, `insert_date`, `update_date`)
                            VALUES (:first_article_id, :name, :alias, :count, :insert_date, :update_date)";
        $insertArticleCategory = "INSERT INTO rss_articles_categories (`rss_category_id`, `rss_article_id`) VALUES (:rss_category_id, :rss_article_id)";
        $updateArticle = "UPDATE rss_articles SET category_name = :category_name, category_alias = :category_alias WHERE id = :id";

        $now = new \Datetime();

        $id = $input->getArgument('id');
        if ($id) {
            $feeds = $feedRepository->findBy(array('id' => $id));
        } else {
            $feeds = $feedRepository->findBy(array('deleted' => 0));
        }

        try {

            $feedsCount = count($feeds);
            for ($i = 0; $i < $feedsCount; $i++) {

                $feedDateStart = new \Datetime();
                $feedTimeStart = microtime(true);

                $url = $feeds[$i]->getUrl();
                try {
                    $feed = simplexml_load_file($url);
                    if ($feed === false) {
                        $logger->log(Logger::WARNING, 'Warning: RSS feed not valid: '.$url);
                        continue;
                    }
                } catch (Exception $e) {
                    $logger->log(Logger::ERROR, $e->getMessage());
                    continue;
                }

                $channel = $feed->channel;
                $lastUpdate = new \Datetime($channel->lastBuildDate);

                $feeds[$i]->setName($channel->title)
                          ->setDescription($channel->description)
                          ->setWebsite($channel->link)
                          ->setLastUpdate($lastUpdate)
                          ->setUpdateDate($now);
                $em->merge($feeds[$i]);
                $em->flush($feeds[$i]);

                $categoriesDb = $categoryRepository->findAll();
                $categories = array();
                $categoryCount = count($categoriesDb);

                for ($j = 0; $j < $categoryCount; $j++) {
                    $categories[$categoriesDb[$j]->getAlias()] = $categoriesDb[$j]->getId();
                }

                $itemsCount = count($channel->item);
                $insertCount = 0;
                for ($j = 0; $j < $itemsCount; $j++) {

                    $articleFeed = $channel->item[$j];

                    $pubDate = new \Datetime($articleFeed->pubDate);

                    $articleAlias = $this->generateAlias($articleFeed->title);

                    // Check if articles have been already imported
                    $articleDb = $articleRepository->createQueryBuilder('articles')
                                                   ->where('articles.rssFeed = :rss_feed')
                                                   ->andWhere('articles.alias = :alias')
                                                   ->andWhere('articles.link = :link')
                                                   ->setParameters(array(
                                                       'rss_feed' => $feeds[$i],
                                                       'alias'    => $articleAlias,
                                                       'link'     => $articleFeed->link
                                                   ))
                                                   ->getQuery()
                                                   ->getScalarResult();
                    if (count($articleDb)) {
                        continue;
                    }

                    $insertTime = new \Datetime();

                    $articleFeed->title = trim($articleFeed->title);

                    $params = array(
                        'rss_feed_id' => $feeds[$i]->getId(),
                        'title'       => trim($articleFeed->title),
                        'alias'       => $articleAlias,
                        'link'        => $articleFeed->link,
                        'date'        => $pubDate->format('Y-m-d H:i:s'),
                        'description' => $articleFeed->description,
                        'content'     => $articleFeed->children('content', true)->encoded,
                        'comments'    => $articleFeed->comments,
                        'insert_date' => $insertTime->format('Y-m-d H:i:s'),
                    );
                    $dbConnection->prepare($insertArticle)->execute($params);
                    $articleId = $dbConnection->lastInsertId();
                    $insertCount++;

                    // Process article categories
                    if (count($articleFeed->category)) {
                        $feedCategories = $articleFeed->category;
                    } else {
                        $feedCategories = array('UFC');
                    }
                    $feedCategoryCount = count($feedCategories);
                    $categoryNames = array();
                    $categoryAliases = array();
                    for ($k = 0; $k < $feedCategoryCount; $k++) {
                        $categoryName = (string) $feedCategories[$k];
                        $categoryName = trim($categoryName);
                        $categoryName = str_replace('ufc', 'UFC', $categoryName);

                        if (!$categoryName) { // Avoid adding empty categories
                            continue;
                        }

                        $categoryAlias = $this->generateAlias($categoryName);
                        if (!isset($categories[$categoryAlias])) {
                            // Create new category
                            $params = array(
                                'first_article_id' => $articleId,
                                'name'             => $categoryName,
                                'alias'            => $categoryAlias,
                                'count'            => 1,
                                'insert_date'      => $insertTime->format('Y-m-d H:i:s'),
                                'update_date'      => $insertTime->format('Y-m-d H:i:s'),
                            );
                            $dbConnection->prepare($insertCategory)->execute($params);
                            $categoryId = $dbConnection->lastInsertId();

                            $categories[$categoryAlias] = $categoryId;
                        } else {
                            $categoryId = $categories[$categoryAlias];
                        }

                        $categoryNames[] = $categoryName;
                        $categoryAliases[] = $categoryAlias;

                        // Add article category
                        $params = array(
                            'rss_category_id' => $categoryId,
                            'rss_article_id'  => $articleId
                        );
                        $dbConnection->prepare($insertArticleCategory)->execute($params);
                    }

                    $params = array(
                        'category_name'  => implode(',', $categoryNames),
                        'category_alias' => implode(',', $categoryAliases),
                        'id'             => $articleId
                    );
                    $dbConnection->prepare($updateArticle)->execute($params);
                }

                if ($insertCount) {
                    $this->track(
                        $em,
                        $feeds[$i],
                        'SUCCESS',
                        $insertCount.' articles inserted',
                        microtime(true) - $feedTimeStart,
                        $feedDateStart,
                        new \Datetime()
                    );
                }

                $em->flush();
            }

            $this->updateCategoryCounters($em);

            $em->flush();

        } catch (Exception $e) {
            $message = $e->getMessage();
            $logger->log(Logger::ERROR, $message);

            $this->track(
                $em,
                $feeds[$i],
                'FAILED',
                $message,
                microtime(true) - $feedTimeStart,
                $feedDateStart,
                new \Datetime()
            );
        }

        $message = $now->format('Y-m-d H:i:s').' RSS feed fetching completed in '.round(microtime(true) - $timeStart, 3).' sec';
        $logger->log(Logger::INFO, $message);
        $output->writeln($message);

        // Generate new sitemap
        $this->getApplication()->find('rss:sitemap')->run($input, $output);
    }

    /**
     * Track event in logs table
     *
     * @param $rssFeedId
     * @param $result
     * @param $message
     * @param $time
     * @param $start
     * @param $end
     */
    private function track($em, $rssFeed, $result, $message, $time, $start, $end)
    {
        $insertArticle = "INSERT INTO rss_logs (`rss_feed_id`, `result`, `info`, `time`, `start`, `end`)
                          VALUES (:rss_feed_id, :result, :info, :time, :start, :end)";
        $params = array(
            'rss_feed_id' => $rssFeed->getId(),
            'result'      => $result,
            'info'        => $message,
            'time'        => $time,
            'start'       => $start->format('Y-m-d H:i:s'),
            'end'         => $end->format('Y-m-d H:i:s'),
        );
        $em->getConnection()->prepare($insertArticle)->execute($params);
        $em->flush();
    }

    /**
     * Calculate categories sum from articles
     */
    private function updateCategoryCounters($em)
    {
        $sql = 'update `rss_categories` set `count` = (
            select count(*) from `rss_articles_categories`
            where `rss_categories`.`id` = `rss_articles_categories`.`rss_category_id`
            group by `rss_category_id`
        )';
        $em->getConnection()->prepare($sql)->execute();
        $em->flush();
    }

    /**
     * Generate string alias
     *
     * @param $string
     * @return string
     */
    private function generateAlias($string)
    {
        //remove any '-' from the string they will be used as concatonater
        $string = str_replace('-', ' ', $string);
        $string = str_replace('_', ' ', $string);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $string = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $string);

        // lowercase and trim
        $string = trim(strtolower($string));
        return $string;
    }
}