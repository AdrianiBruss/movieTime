<?php

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Torrent;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;


class TorrentService {


    protected $doctrine;
    protected $validator;
    protected $root_dir;

    public function __construct($doctrine, $validator, $root_dir){
        $this->doctrine = $doctrine;
        $this->validator = $validator;
        $this->root_dir;
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
//                if($i < 3){

                    $this->getTorrentFromKickAss($nodeCrawler);
//                }

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
                $imdbId = intval($node->text());

                $title = $linkCrawler->filter('h1')->text();

                $magnet = "";
                $magnetCheck = $linkCrawler
                    ->filter('a.magnetlinkButton')
                    ->each(function($node) use (&$magnet){
                        $magnet = $node->link()->getUri();

                    });

                $infoHash = preg_match('/btih:(?<path>\w*)&/', $magnet, $hash);
                $infoHash = $hash['path'];

                $seeders = intval($linkCrawler->filter('strong[itemprop="seeders"]')->text());
                $leechers = intval($linkCrawler->filter('strong[itemprop="leechers"]')->text());

                $quality = $this->foundQuality($title);


                $trailer = $linkCrawler->filter('#tab-trailer>div.center>iframe')->attr('src');

                $torrentArray = array();

                array_push($torrentArray, $title, $magnet, $infoHash, $seeders, $leechers, $quality, $trailer);

//                dump($torrentArray);

                $this->getTorrentFromImdb($imdbId, $torrentArray);

            });

    }


    public function getTorrentFromImdb($imdbId, $torrentArray){

        $movieArray = array();

        $client = new Client();
        $linkImbd = $client->request('GET', 'http://www.imdb.com/title/tt'.$imdbId);

        $title = $linkImbd->filter('h1.header span[itemprop="name"]')->text();

        $titleExtra = $linkImbd
            ->filter('.title-extra')
            ->each(function($node) use (&$title){
                if($node){
                    $title = $this->extraTitle(trim($node->text()));
                }
            });



        $category = [];
        $categoryCheck = $linkImbd->filter('span[itemprop="genre"]')
            ->each(function($node) use (&$category){
               if($node){
                   $category[] = $node->text();
               }
            });


        $dateRelease = "";
        $dateReleaseCheck = $linkImbd->filter('h1 span a')

            ->each(function($node) use (&$dateRelease){

                if ($node){
                    $dateRelease = intval($node->text());

                }else{
                    $dateRelease = 'Not found';
                }

            });

        $director = $linkImbd->filter('div[itemprop="director"] span')->text();

        $img = "";
        $imgCheck = $linkImbd
            ->filter('div.image [itemprop="image"]')
            ->first()
            ->each(function($node) use (&$img){

                if ($node){
                    $img = $node->attr('src');

                    $file = file_get_contents($img);
                    $img = explode('/M/', $img)[1];

                    $fp = fopen(__DIR__."/../../../web/uploads/".$img, "w");
                    fwrite($fp, $file);
                    fclose($fp);

                }

            });

        $rate = '';
        $rateCheck = $linkImbd
            ->filter('div.star-box-giga-star')
            ->each(function($node) use (&$rate){

                if(!$node){
                    $rate = null;
                }else{
                    $rate = floatval($node->text());
                }

            });


        $ratingCount = '';
        $ratingCountCheck = $linkImbd
            ->filter('span[itemprop="ratingCount"]')
            ->each(function($node) use (&$ratingCount){
                if (!$node){
                    $ratingCount = null;
                }else{
                    $ratingCount = $node->text();
                }
            });
        $ratingCount = $this->commaToDot($ratingCount);

        $backdrops = $this->getBackdrops($imdbId);
        $backdrops = "http://image.tmdb.org/t/p/w1000".$backdrops;

        array_push($movieArray, $title, $dateRelease, $director, $img, $rate, $ratingCount, $category, $backdrops);

        dump($movieArray);

        $this->setImdbMovie($movieArray, $torrentArray);


    }


//    (film/torrent déjà présent ? --- OK
//    torrent de qualité suffisante ? CAM / TS/ HDRIP / BRRIP -- OK
//    Rating imdb assez intéressant ? --- OK
//    etc. ) et envoyer un email contenant les infos des nouveaux films (peut-être fait plus tard).
//    Aller chercher les catégories

    public function setImdbMovie($movieArray, $torrentArray){

        $movieRepo = $this->doctrine->getManager()->getRepository('AppBundle:Movie');
        $movie = $movieRepo->findOneByTitle($movieArray[0]);

        if (!$movie){

            $newMovie = new Movie();
            $newMovie->setTitle($movieArray[0]);
            $newMovie->setYear($movieArray[1]);
            $newMovie->setDirector($movieArray[2]);
            $newMovie->setImgUrl($movieArray[3]);
            $newMovie->setRating($movieArray[4]);
            $newMovie->setNbRates($movieArray[5]);
            $newMovie->setBackdrops($movieArray[7]);
            $newMovie->setTrailer($torrentArray[6]);

            $catRepo = $this->doctrine->getManager()->getRepository('AppBundle:Category');

            foreach($movieArray[6] as $category){

                $cat = $catRepo->findOneByName($category);

                if (!$cat){

                    $newCat = new Category();
                    $newCat->setName($category);

                    $errorList = $this->validator->validate($newCat);


                    if (count($errorList) > 0) {
                        dump('error cat');

                    } else {

                        dump('Category added ! ');

                        $em = $this->doctrine->getManager();
                        $em->persist($newCat);
                        $em->flush();
                        $newMovie->addCategory($newCat);

                    }

                }else{

                    $newMovie->addCategory($cat);

                }

            }

//            dump($newMovie);


            $errorList = $this->validator->validate($newMovie);

            if (count($errorList) > 0) {
                dump('error movie');

            } else {

                $em = $this->doctrine->getManager();
                $em->persist($newMovie);
                $em->flush();

                dump('Movie added ! ');

                $this->setTorrentMovie($torrentArray, $newMovie);
            }

        }else{

            dump('Movie already exists');

            $this->setTorrentMovie($torrentArray, $movie);

        }




    }
    public function setTorrentMovie($torrentArray, $movie){

        // checker si le hash du torrent existe dejà en base de données

        $torrentRepo = $this->doctrine->getManager()->getRepository('AppBundle:Torrent');
        $torrent = $torrentRepo->findByHash($torrentArray[2]);

        if (!$torrent){

            $newTorrent = new Torrent();
            $newTorrent->setName($torrentArray[0]);
            $newTorrent->setMagnet($torrentArray[1]);
            $newTorrent->setHash($torrentArray[2]);
            $newTorrent->setSeeders($torrentArray[3]);
            $newTorrent->setLeechers($torrentArray[4]);
            $newTorrent->setQuality($torrentArray[5]);
            $newTorrent->setMovie($movie);

            $errorList = $this->validator->validate($newTorrent);

            if (count($errorList) > 0) {
                dump('error torrent');

            } else {

                $em = $this->doctrine->getManager();
                $em->persist($newTorrent);
                $em->flush();

                dump('torrent added ! ');

            }

        }else{

            dump('Torrent already exists');

        }

    }

    public function commaToDot($input){

        return floatval(str_replace(',', '.', $input));

    }

    public function extraTitle($input){

        return str_replace('"', '', trim(substr($input, 0, -16)));

    }

    public function foundQuality($input){

        $q_array = array('/\bcam\b/', '/\bts\b/', '/hdrip/', '/bdrip/', '/brrip/', '/xvid/', '/dvdrip/','/bluray/', '/webrip/');

        foreach($q_array as $q){

            if (preg_match( $q, strtolower($input), $matches )){
                return $matches[0];
            }

        }

    }

    public function getBackdrops($id){

        dump('getBackdrops');


        if( strlen($id) == 6 ){
            $id_back = "tt0".$id;
        }else{
            $id_back = "tt".$id;
        }

        $url = "https://api.themoviedb.org/3/movie/".$id_back."/images?api_key=a597734de4d095ef5b8860c4fd7050a6";

        dump('get http response');

        if($this->get_http_response_code($url) != "200"){
            dump('got http response !=200');
            return $backdrops = null;
        }else{
            dump('got http response 200');
            $backdrops = file_get_contents($url, 0, null, null);
            $jsondecode = json_decode($backdrops);
            return $jsondecode->backdrops[0]->file_path;
        }

    }

    public function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

}