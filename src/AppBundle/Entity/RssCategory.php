<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * RssCategory
 *
 * @ORM\Table(name="rss_categories")
 * @ORM\Entity(repositoryClass="RssCategoryRepository")
 */
class RssCategory
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
     * @var RssArticle
     *
     * @ORM\ManyToOne(targetEntity="RssArticle")
     * @ORM\JoinColumn(name="first_article_id")
     */
    private $firstArticle;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=100)
     */
    private $alias;

    /**
     * @var integer
     *
     * @ORM\Column(name="main", type="smallint")
     */
    private $main;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert_date", type="datetime")
     */
    private $insertDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime")
     */
    private $updateDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="smallint")
     */
    private $deleted;

    /**
     * @ORM\ManyToMany(targetEntity="RssArticle", mappedBy="rssCategories")
     **/
    private $rssArticles;


    public function __construct()
    {
        $this->rssArticles = new ArrayCollection();
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
     * Set firstArticle
     *
     * @param integer $firstArticle
     * @return RssCategory
     */
    public function setFirstArticle($firstArticle)
    {
        $this->firstArticle = $firstArticle;

        return $this;
    }

    /**
     * Get firstArticle
     *
     * @return integer
     */
    public function getFirstArticle()
    {
        return $this->firstArticle;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return RssCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return RssCategory
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
     * Set main
     *
     * @param integer $main
     * @return RssCategory
     */
    public function setMain($main)
    {
        $this->main = $main;

        return $this;
    }

    /**
     * Get main
     *
     * @return integer 
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return RssCategory
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer 
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set insertDate
     *
     * @param \DateTime $insertDate
     * @return RssCategory
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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return RssCategory
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     * @return RssCategory
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
     * Add article to category
     *
     * @param RssArticle $article
     * @return RssCategory
     */
    /*public function addArticle(RssArticle $article)
    {
        $this->rssArticles[] = $article;

        return $this;
    }*/
}
