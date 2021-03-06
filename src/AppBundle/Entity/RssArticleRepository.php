<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * RssArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RssArticleRepository extends EntityRepository
{
    /**
     * @param $alias
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getArticle($alias)
    {
        $select = "articles.id, articles.title, articles.alias, articles.description, articles.content, articles.views,
                    articles.categoryName, articles.categoryAlias, articles.date, articles.link, feeds.name as feed";

        try {
            $article = $this->getEntityManager()
                            ->createQueryBuilder()
                            ->select($select)
                            ->from('AppBundle:RssArticle', 'articles')
                            ->join('articles.rssFeed', 'feeds')
                            ->where('articles.alias = :alias')
                            ->andWhere('articles.deleted = :deleted')
                            ->setParameters(array(
                                'alias'   => $alias,
                                'deleted' => 0
                            ))
                            ->getQuery()
                            ->getSingleResult();

            return ($article['id']) ? $article : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param \Knp\Component\Pager\Paginator $paginator
     * @param int $page
     * @param string $order
     * @param int $limit
     * @param string $category
     * @param string $keyword
     * @return mixed
     */
    public function getArticles($paginator, $page = 1, $order = 'articles.date desc', $limit = 20, $category = '', $keyword = '')
    {
        $select = "articles.id, articles.title, articles.alias, articles.description, articles.content, articles.views, articles.date,
                     articles.categoryName, articles.categoryAlias, feeds.name as feed";
        $params = array(
            'deleted'     => 0,
            'description' => '',
            'content'     => ''
        );

        $query = $this->getEntityManager()
                      ->createQueryBuilder()
                      ->select($select)
                      ->from('AppBundle:RssArticle', 'articles')
                      ->join('articles.rssFeed', 'feeds')
                      ->where('articles.deleted = :deleted')
                      ->andWhere('articles.description != :description or articles.content != :content');

        if ($keyword) {
            $query->join('articles.rssCategories', 'categories')
                  ->andWhere('categories.name like :keyword or articles.title like :keyword or articles.description like :keyword');
            $params['keyword'] = '%'.$keyword.'%';
        }
        if ($category) {
            $query->andWhere('articles.categoryAlias like :category or articles.categoryAlias like :category_start or
                articles.categoryAlias like :category_in or articles.categoryAlias like :category_end');
            $params = array_merge($params, array(
                'category'       => $category,
                'category_start' => $category.',%',
                'category_in'    => '%,'.$category.',%',
                'category_end'   => '%,'.$category
            ));
        }
        $orders = explode(' ', $order);
        $query->setParameters($params)
              ->groupBy('articles.id')
              ->orderBy($orders[0], $orders[1]);

        return $paginator->paginate($query->getQuery(), $page, $limit);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function incrementViews($id)
    {
        return $this->getEntityManager()
                    ->createQueryBuilder()
                    ->update('AppBundle:RssArticle', 'articles')
                    ->set('articles.views', 'articles.views + 1')
                    ->where('articles.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->execute();
    }
}
