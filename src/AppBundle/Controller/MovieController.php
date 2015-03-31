<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MovieController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

        $moviesRepo = $this->getDoctrine()->getRepository('AppBundle:Movie');
        $movies = $moviesRepo->findAll();

        $params = array(
            'movies'=>$movies
        );

        return $this->render('movie/index.html.twig', $params);
    }

    /**
     * @Route("/movie/{id}", name="showMovie")
     */
    public function showMovieAction($id)
    {

        $moviesRepo = $this->getDoctrine()->getRepository('AppBundle:Movie');
        $movie = $moviesRepo->find($id);

        $params = array(
            'movie'=>$movie,

        );

        return $this->render('movie/single.html.twig', $params);
    }


}
