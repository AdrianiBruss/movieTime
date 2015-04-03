<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class MovieController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

        $moviesRepo = $this->getDoctrine()->getRepository('AppBundle:Movie');
        $movies = $moviesRepo->findAll();

        return new JsonResponse($movies);

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
