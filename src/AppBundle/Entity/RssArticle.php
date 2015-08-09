<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * RssArticle
 *
 * @ORM\Table(name="rss_articles")
 * @ORM\Entity(repositoryClass="RssArticleRepository")
 */
class RssArticle
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var RssFeed
     *
     * @ORM\ManyToOne(targetEntity="RssFeed", inversedBy="rssArticles")
     * @ORM\JoinColumn(name="rss_feed_id", referencedColumnName="id")
     */
    private $rssFeed;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=255)
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(name="category_name", type="string", length=255)
     */
    private $categoryName;

    /**
     * @var string
     *
     * @ORM\Column(name="category_alias", type="string", length=255)
     */
    private $categoryAlias;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert_date", type="datetime")
     */
    private $insertDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="smallint")
     */
    private $deleted;

    /**
     * @ORM\ManyToMany(targetEntity="RssCategory", inversedBy="rssArticles")
     * @ORM\JoinTable(name="rss_articles_categories")
     **/
    private $rssCategories;

    /**
     * @ORM\OneToMany(targetEntity="RssArticleView", mappedBy="rssArticle")
     **/
    private $rssArticleViews;


    public function __construct()
    {
        $this->categoryName = '';
        $this->categoryAlias = '';
        $this->rssCategories = new ArrayCollection();
        $this->rssArticleViews = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rssFeed
     *
     * @param integer $rssFeed
     * @return RssArticle
     */
    public function setRssFeed($rssFeed)
    {
        $this->rssFeed = $rssFeed;

        return $this;
    }

    /**
     * Get rssFeed
     *
     * @return integer 
     */
    public function getRssFeed()
    {
        return $this->rssFeed;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return RssArticle
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return RssArticle
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return RssArticle
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return RssArticle
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return RssArticle
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return RssArticle
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return RssArticle
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return RssArticle
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set category name
     *
     * @param string $categoryName
     * @return RssArticle
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * Get category name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * Set category alias
     *
     * @param string $categoryAlias
     * @return RssArticle
     */
    public function setCategoryAlias($categoryAlias)
    {
        $this->categoryAlias = $categoryAlias;

        return $this;
    }

    /**
     * Get category alias
     *
     * @return string
     */
    public function getCategoryAlias()
    {
        return $this->categoryAlias;
    }

    /**
     * Set insertDate
     *
     * @param \DateTime $insertDate
     * @return RssArticle
     */
    public function setInsertDate($insertDate)
    {
        $this->insertDate = $insertDate;

        return $this;
    }

    /**
     * Get insertDate
     *
     * @return \DateTime 
     */
    public function getInsertDate()
    {
        return $this->insertDate;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     * @return RssArticle
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return integer 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Add category to article
     *
     * @param RssCategory $category
     * @return RssArticle
     */
    public function addCategory(RssCategory $category)
    {
        //$category->addArticle($this);
        $this->rssCategories[] = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }
}
