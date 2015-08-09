<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/{page}", name="homepage", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template()
     */
    public function indexAction($page)
    {
        $articles = $this->getDoctrine()
                         ->getRepository('AppBundle:RssArticle')
                         ->getArticles($this->get('knp_paginator'), $page);

        return array('articles' => $articles);
    }
    /**
     * @Route("/popular/{page}", name="popular", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template("AppBundle:Default:index.html.twig")
     */
    public function popularAction($page)
    {
        $articles = $this->getDoctrine()
                         ->getRepository('AppBundle:RssArticle')
                         ->getArticles($this->get('knp_paginator'), $page, 'articles.views desc');

        return array('articles' => $articles);
    }

    /**
     * @Route("/category/{alias}/{page}", name="category", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template("AppBundle:Default:index.html.twig")
     */
    public function categoryAction($alias, $page)
    {
        $articles = $this->getDoctrine()
                         ->getRepository('AppBundle:RssArticle')
                         ->getArticles($this->get('knp_paginator'), $page, 'articles.views desc', 20, $alias);

        return array('articles' => $articles);
    }

    /**
     * @Route("/categories/{page}", name="categories", requirements={"page": "\d+"}, defaults={"page": 1})
     * @Template()
     */
    public function categoriesAction($page)
    {
        $categories = $this->getDoctrine()
                           ->getRepository('AppBundle:RssCategory')
                           ->getCategories($this->get('knp_paginator'), false, $page, 50);

        return array('categories' => $categories);
    }

    /**
     * @Route("/news/{alias}", name="news")
     * @Template()
     */
    public function showAction(Request $request, $alias)
    {
        $repository = $this->getDoctrine()
                           ->getRepository('AppBundle:RssArticle');
        $article = $repository->getArticle($alias);

        if (!$article) {
            throw $this->createNotFoundException($this->get('translator')->trans('error.not_found'));
        }

        $articleViews = $this->getDoctrine()
                             ->getRepository('AppBundle:RssArticleView')
                             ->count(
                                 $article['id'],
                                 $request->getClientIp(),
                                 $this->get('session')->getId()
                             );

        if (!$articleViews) {
            $em = $this->getDoctrine()->getManager();

            $singleArticle = $repository->find($article['id']);

            $articleView = $this->get('app.rss_article_view');
            $articleView->setRssArticle($singleArticle)
                        ->setIp($request->getClientIp())
                        ->setSessionId($this->get('session')->getId())
                        ->setUseragent($request->headers->get('User-Agent'))
                        ->setDate(new \DateTime());
            $em->persist($articleView);

            $repository->incrementViews($article['id']);

            try {
                $em->flush();
            } catch (\Exception $e) {
                $this->get('logger')->err($e->getMessage());
            }
        }

        return array('article' => $article);
    }

    /**
     * @Template()
     */
    public function sidebarAction()
    {
        $categories = $this->getDoctrine()
                           ->getRepository('AppBundle:RssCategory')
                           ->getCategories($this->get('knp_paginator'), false, 1, 10);

        $popular = $this->getDoctrine()
                        ->getRepository('AppBundle:RssArticle')
                        ->getArticles($this->get('knp_paginator'), 1, 'articles.views desc', 10);

        return array(
            'categories' => $categories,
            'popular'    => $popular,
        );
    }
}
