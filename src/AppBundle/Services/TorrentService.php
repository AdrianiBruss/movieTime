<?php

namespace AppBundle\Services;

use AppBundle\Entity\Movie;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

class TorrentService {


    protected $doctrine;
    protected $container;
    public function __construct($doctrine,Container $container){
        $this->doctrine = $doctrine;
        $this->container = $container;
    }

//Infos à parser et à sauvegarder à propos du torrent :
//- nom du torrent
//- lien "magnet"
//- info hash
//- nombre de seeders
//- nombre de leetchers
//- qualité (ts, brrip, dvdrip, cam, etc...)
//
//Infos à parser et à sauvegarder à propos du film :
//- titre
//- id imdb
//- année
//- réalisateur du film
//- url du poster principal (au moins 200px de large)
//- rating
//- nombre de votes

    public function getTorrentInfos(){

        $client = new Client();
        $crawler = $client->request('GET', 'http://kickass.to/movies/?field=seeders&sorder=desc');
        $crawler = $crawler
            ->filter('.torrentname .markeredBlock.torType.filmType>a:first-child')
            ->reduce(function($nodeCrawler, $i){
                if($i<2){
                    $this->getTorrentFromKickAss($nodeCrawler);
                }
            });

    }

    public function getTorrentFromKickAss($nodeCrawler){



        $client = new Client();

        $link = $nodeCrawler->first()->link()->getUri();

        $linkCrawler = $client->request('GET', $link);

        $imdbIdCheck = $linkCrawler->filter('ul.block.overauto>li:nth-child(3)>a')

            ->each(function($node) use (&$linkCrawler){

                if(!$node){

                    return false;

                }
                $imdbId = $node->text();

                $title = $linkCrawler->filter('h1')->text();

                $magnet = "";
                $magnetCheck = $linkCrawler
                    ->filter('a.magnetlinkButton')
                    ->each(function($node) use (&$magnet){
                        $magnet = $node->link()->getUri();

                    });

                $infoHash = preg_match('/btih:(?<path>\w*)&/', $magnet, $hash);
                $infoHash = $hash['path'];

                $seeders = $linkCrawler->filter('strong[itemprop="seeders"]')->text();
                $leechers = $linkCrawler->filter('strong[itemprop="leechers"]')->text();

                $quality = '';
                $qualityCheck = $linkCrawler
                    ->filter('ul.block.overauto>li:nth-child(2)>span')
                    ->each(function($node) use (&$quality){

                        if ($node){
                            $quality = $node->text();
                        }else{
                            $quality = 'Not Found';
                        }

                    });

                $torrentArray = array();

                array_push($torrentArray, $title, $magnet, $infoHash, $seeders, $leechers, $quality);

                dump($torrentArray);

                $this->getTorrentFromImdb($imdbId, $title);

            });

    }


    public function getTorrentFromImdb($imdbId, $title){

        $movieArray = array();

        $client = new Client();
        $linkImbd = $client->request('GET', 'http://www.imdb.com/title/tt'.$imdbId);

        $dateRelease = "";
        $dateReleaseCheck = $linkImbd->filter('h1 span a')

            ->each(function($node) use (&$dateRelease){

                if ($node){
                    $dateRelease = $node->text();

                }else{
                    $dateRelease = 'Not found';
                }

            });

        $director = $linkImbd->filter('div[itemprop="director"] span')->text();

        $img = "";
        $imgCheck = $linkImbd
            ->filter('div.image [itemprop="image"]')
            ->each(function($node) use (&$img){

                if ($node){
                    $img = $node->attr('src');
                }

            });

        $rate = $linkImbd->filter('div.star-box-giga-star')->text();

        $ratingCount = $linkImbd->filter('span[itemprop="ratingCount"]')->text();

        array_push($movieArray, $imdbId, $title, $dateRelease, $director, $img, $rate, $ratingCount);

        dump($movieArray);

        $this->setImdbMovie($movieArray);


    }


//    (film/torrent déjà présent ?
//    torrent de qualité suffisante ?
//    Rating imdb assez intéressant ?
//    etc. ) et envoyer un email contenant les infos des nouveaux films (peut-être fait plus tard).

    public function setImdbMovie($movieArray){

        $movieRepo = $this->doctrine->getManager()->getRepository('AppBundle:Movie');

        $movie = $movieRepo->findByMovieId($movieArray[0]);

        if (!$movie){

            $newMovie = new Movie();
            $newMovie->setMovieId($movieArray[0]);
            $newMovie->setTitle($movieArray[1]);
            $newMovie->setYear(intval($movieArray[2]));
            $newMovie->setDirector($movieArray[3]);
            $newMovie->setImgUrl($movieArray[4]);
            $newMovie->setRating(floatval($movieArray[5]));
            $newMovie->setNbRates(floatval($movieArray[6]));

            $validator = $this->container->get('validator');
            $errorList = $validator->validate($newMovie);

            if (count($errorList) > 0) {
                return new Response(print_r($errorList, true));
            } else {

                $em = $this->doctrine->getEntityManager();
                $em->persist($newMovie);
                $em->flush();

            }

        }else{



        }

    }
    public function setTorrentMovie(){



    }

}