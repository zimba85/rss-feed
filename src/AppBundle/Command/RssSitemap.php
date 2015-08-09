<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;

class RssSitemap extends ContainerAwareCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('rss:sitemap')
             ->setDescription('Update sitemap');
    }

    /**
     * Command execution
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $articles = $container
                         ->get('doctrine')
                         ->getRepository('AppBundle:RssArticle')
                         ->createQueryBuilder('articles')
                         ->select('articles.alias, articles.insertDate')
                         ->andWhere('articles.deleted = :deleted')
                         ->setParameter('deleted', 0)
                         ->getQuery()
                         ->getScalarResult();

        $sitemap = $container->get('templating')->render(
            'AppBundle:Default:sitemap.html.twig',
            array(
                'articles' => $articles
            )
        );

        $filePath = str_replace('ufcnews/app', 'ufcnews/web', $container->get('kernel')->getRootDir()).'/sitemap.xml';

        $file = fopen($filePath, 'w');
        fwrite($file, $sitemap);
        fclose($file);

        $now = new \DateTime();
        $container->get('logger')->log(Logger::INFO, $now->format('Y-m-d H:i:s').' sitemap created');
    }
}