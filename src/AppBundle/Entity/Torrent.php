<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Torrent
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TorrentRepository")
 */
class Torrent
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="magnet", type="text")
     */
    private $magnet;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="hash", type="text")
     */
    private $hash;

    /**
     * @var integer
     * @ORM\Column(name="seeders", type="integer")
     */
    private $seeders;

    /**
     * @var integer
     *
     * @ORM\Column(name="leechers", type="integer")
     */
    private $leechers;

    /**
     * @var string
     *
     * @ORM\Column(name="quality", type="string", length=255)
     */
    private $quality;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @ORM\Column(name="imdbId", type="integer")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Movie", inversedBy="imdbId")
     */
    private $imdbId;


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
     * Set name
     *
     * @param string $name
     * @return Torrent
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
     * Set magnet
     *
     * @param string $magnet
     * @return Torrent
     */
    public function setMagnet($magnet)
    {
        $this->magnet = $magnet;

        return $this;
    }

    /**
     * Get magnet
     *
     * @return string 
     */
    public function getMagnet()
    {
        return $this->magnet;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Torrent
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set seeders
     *
     * @param integer $seeders
     * @return Torrent
     */
    public function setSeeders($seeders)
    {
        $this->seeders = $seeders;

        return $this;
    }

    /**
     * Get seeders
     *
     * @return integer 
     */
    public function getSeeders()
    {
        return $this->seeders;
    }

    /**
     * Set leechers
     *
     * @param integer $leechers
     * @return Torrent
     */
    public function setLeechers($leechers)
    {
        $this->leechers = $leechers;

        return $this;
    }

    /**
     * Get leechers
     *
     * @return integer 
     */
    public function getLeechers()
    {
        return $this->leechers;
    }

    /**
     * Set quality
     *
     * @param string $quality
     * @return Torrent
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return string 
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set imdbId
     *
     * @param integer $imdbId
     * @return Torrent
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return integer 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }
}
