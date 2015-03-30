<?php

namespace AppBundle\Services;


use Goutte\Client;

class TorrentService {

//- nom du torrent
//- lien "magnet"
//- info hash
//- nombre de seeders
//- nombre de leetchers
//- qualité (ts, brrip, dvdrip, cam, etc...)

    public function getTorrentInfos(){

        $client = new Client();
        $crawler = $client->request('GET', 'http://kickass.to/movies/?field=seeders&sorder=desc');
        $crawler = $crawler
            ->filter('.torrentname .markeredBlock.torType.filmType>a:first-child')
            ->each(function($node){

                $client = new Client();

                $link = $node->first()->link()->getUri();

                $linkCrawler = $client->request('GET', $link);

                $title = $linkCrawler->filter('h1');

                $magnet = $linkCrawler
                    ->filter('a.magnetlinkButton')
                    ->link()->getUri();

                $infoHash = preg_match('/btih:(?<path>\w*)&/', $magnet, $hash);

                $infoHash = $hash['path'];

                $seeders = $linkCrawler->filter('strong[itemprop="seeders"]')->text();
                $leechers = $linkCrawler->filter('strong[itemprop="leechers"]')->text();

                $qualityCheck = $linkCrawler
                    ->filter('ul.block.overauto>li:nth-child(2)>span')
                    ->each(function($node){

                        if ($node){
                            $quality = $node->text();
                        }else{
                            $quality = 'Not Found';
                        }

                    });

            });


    }

}