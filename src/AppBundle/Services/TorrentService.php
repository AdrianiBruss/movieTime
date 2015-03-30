<?php

namespace AppBundle\Services;


use Goutte\Client;

class TorrentService {

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
                if($i<5){
                    $this->getTorrentFromKickAss($nodeCrawler);
                }
            });

    }

    public function getTorrentFromKickAss($nodeCrawler){

        $torrentArray = [];
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
                $torrentArray[] = $title;

                $magnet = "";
                $magnetCheck = $linkCrawler
                    ->filter('a.magnetlinkButton')
                    ->each(function($node) use (&$magnet){
                        $magnet = $node->link()->getUri();

                    });
                $torrentArray[] = $magnet;

                $infoHash = preg_match('/btih:(?<path>\w*)&/', $magnet, $hash);
                $infoHash = $hash['path'];
                $torrentArray[] = $infoHash;

                $seeders = $linkCrawler->filter('strong[itemprop="seeders"]')->text();
                $leechers = $linkCrawler->filter('strong[itemprop="leechers"]')->text();
                $torrentArray[] = $seeders;
                $torrentArray[] = $leechers;

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

                $torrentArray[] = $quality;

                $this->getTorrentFromImdb($imdbId, $title);

            });

    }


    public function getTorrentFromImdb($imdbId, $title){


        $movieArray = [];

        $movieArray[] = $imdbId;
        $movieArray[] = $title;

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

        $movieArray[] = $dateRelease;

        $director = $linkImbd->filter('div[itemprop="director"] span')->text();
        $movieArray[] = $director;

        $img = "";
        $imgCheck = $linkImbd
            ->filter('div.image [itemprop="image"]')
            ->each(function($node) use (&$img){

                if ($node){
                    $img = $node->attr('src');
                }

            });

        $movieArray[] = $img;

        $rate = $linkImbd->filter('div.star-box-giga-star')->text();
        $movieArray[] = $rate;

        $ratingCount = $linkImbd->filter('span[itemprop="ratingCount"]')->text();
        $movieArray[] = $ratingCount;

        

    }

    public function setImdbMovie(){

    }
    public function setTorrentMovie(){

    }

}