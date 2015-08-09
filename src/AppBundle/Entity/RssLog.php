<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RssLog
 *
 * @ORM\Table(name="rss_logs")
 * @ORM\Entity(repositoryClass="RssLogRepository")
 */
class RssLog
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
     * @ORM\ManyToOne(targetEntity="RssFeed", inversedBy="rssLogs")
     * @ORM\JoinColumn(name="rss_feed_id", referencedColumnName="id")
     */
    private $rssFeed;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="string", length=45)
     */
    private $result;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=255)
     */
    private $info;

    /**
     * @var float
     *
     * @ORM\Column(name="time", type="float")
     */
    private $time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;


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
     * @return RssLog
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
     * Set result
     *
     * @param string $result
     * @return RssLog
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return string 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return RssLog
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set time
     *
     * @param float $time
     * @return RssLog
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return float 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return RssLog
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return RssLog
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }
}
