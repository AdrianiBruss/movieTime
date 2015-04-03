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


    /**
     * @Route("/removeTorrent/{id}", name="removeTorrent")
     */
    public function removeTorrentAction($id)
    {

        $torRepo = $this->getDoctrine()->getRepository('AppBundle:Torrent');
        $torrent = $torRepo->find($id);

        if(!$torrent){
            $error = array('error'=>'torrent not found');
            return new JsonResponse($error);

        }else{

            $em = $this->getDoctrine()->getManager();
            $em->remove($torrent);
            $em->flush();

            $response = array('result'=>'torrent deleted');
            return new JsonResponse($response);

        }


    }




}
