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
     * @var integer
     * @Assert\NotBlank()
     * @ORM\Column(name="movieId", type="integer")
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Torrent", mappedBy="movieId", cascade={"persist", "remove"})
     */
    private $movieId;

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
     * @var string
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="movie", cascade={"persist"})
     */
    private $category;


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
     * Set movieId
     *
     * @param integer $movieId
     * @return Movie
     */
    public function setMovieId($movieId)
    {
        $this->movieId = $movieId;

        return $this;
    }

    /**
     * Get movieId
     *
     * @return integer 
     */
    public function getMovieId()
    {
        return $this->movieId;
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
        $this->category = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Category $category
     * @return Movie
     */
    public function addCategory(\AppBundle\Entity\Category $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(\AppBundle\Entity\Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
