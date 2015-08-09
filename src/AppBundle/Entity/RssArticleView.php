<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RssArticleView
 *
 * @ORM\Table(name="rss_article_views")
 * @ORM\Entity(repositoryClass="RssArticleViewRepository")
 */
class RssArticleView
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="RssArticle", inversedBy="rssArticleViews")
     * @ORM\JoinColumn(name="rss_article_id", referencedColumnName="id")
     */
    private $rssArticle;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=20)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=255)
     */
    private $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="useragent", type="string", length=255)
     */
    private $useragent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * Set rssArticle
     *
     * @param integer $rssArticle
     * @return RssArticleView
     */
    public function setRssArticle($rssArticle)
    {
        $this->rssArticle = $rssArticle;

        return $this;
    }

    /**
     * Get rssArticle
     *
     * @return integer 
     */
    public function getRssArticle()
    {
        return $this->rssArticle;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return RssArticleView
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     * @return RssArticleView
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set useragent
     *
     * @param string $useragent
     * @return RssArticleView
     */
    public function setUseragent($useragent)
    {
        $this->useragent = $useragent;

        return $this;
    }

    /**
     * Get useragent
     *
     * @return string 
     */
    public function getUseragent()
    {
        return $this->useragent;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return RssArticleView
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
}
