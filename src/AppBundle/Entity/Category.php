<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="Movie", inversedBy="category")
     * @ORM\JoinTable()
     *
     */
    private $movie;


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
     * @return Category
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
     * Constructor
     */
    public function __construct()
    {
        $this->movie = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add movie
     *
     * @param \AppBundle\Entity\Movie $movie
     * @return Category
     */
    public function addMovie(\AppBundle\Entity\Movie $movie)
    {
        $this->movie[] = $movie;

        return $this;
    }

    /**
     * Remove movie
     *
     * @param \AppBundle\Entity\Movie $movie
     */
    public function removeMovie(\AppBundle\Entity\Movie $movie)
    {
        $this->movie->removeElement($movie);
    }

    /**
     * Get movie
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovie()
    {
        return $this->movie;
    }
}
