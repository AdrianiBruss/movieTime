<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Movie
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MovieRepository")
 */
class Movie
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
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Torrent", mappedBy="movie", cascade={"persist", "remove"})
     */
    private $torrents;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="director", type="string", length=255)
     */
    private $director;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="imgUrl", type="text")
     */
    private $imgUrl;

    /**
     * @var float
     *
     * @Assert\Range(
     *      min = 6.0,
     *      max = 10.0
     * )
     * @ORM\Column(name="rating", type="float")
     */
    private $rating;

    /**
     * @var float
     * @Assert\GreaterThan(
     *     value = 10.000
     * )
     * @ORM\Column(name="nbRates", type="float")
     */
    private $nbRates;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", inversedBy="movies", cascade={"persist"})
     */
    private $categories;


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
     * Set title
     *
     * @param string $title
     * @return Movie
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
     * Set year
     *
     * @param integer $year
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Movie
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string 
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set imgUrl
     *
     * @param string $imgUrl
     * @return Movie
     */
    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    /**
     * Get imgUrl
     *
     * @return string 
     */
    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return Movie
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set nbRates
     *
     * @param float $nbRates
     * @return Movie
     */
    public function setNbRates($nbRates)
    {
        $this->nbRates = $nbRates;

        return $this;
    }

    /**
     * Get nbRates
     *
     * @return float 
     */
    public function getNbRates()
    {
        return $this->nbRates;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    

    /**
     * Set torrents
     *
     * @param integer $torrents
     * @return Movie
     */
    public function setTorrents($torrents)
    {
        $this->torrents = $torrents;

        return $this;
    }

    /**
     * Get torrents
     *
     * @return integer 
     */
    public function getTorrents()
    {
        return $this->torrents;
    }

    /**
     * Add torrents
     *
     * @param \AppBundle\Entity\Torrent $torrents
     * @return Movie
     */
    public function addTorrent(\AppBundle\Entity\Torrent $torrents)
    {
        $this->torrents[] = $torrents;

        return $this;
    }

    /**
     * Remove torrents
     *
     * @param \AppBundle\Entity\Torrent $torrents
     */
    public function removeTorrent(\AppBundle\Entity\Torrent $torrents)
    {
        $this->torrents->removeElement($torrents);
    }

    /**
     * Add categories
     *
     * @param \AppBundle\Entity\Category $categories
     * @return Movie
     */
    public function addCategory(\AppBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
        $categories->addMovie($this);

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \AppBundle\Entity\Category $categories
     */
    public function removeCategory(\AppBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
