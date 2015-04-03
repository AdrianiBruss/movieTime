<?php

namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TorrentCommand extends ContainerAwareCommand{


    public function configure(){

        $this->setName('torrents:get')->setDescription('get torrents on KikAss');

    }

    public function execute(InputInterface $input, OutputInterface $output){

        $container = $this->getContainer();
        $service = $container->get('torrents');
        $torrent = $service->getTorrentInfos();
//        $output->writeln($torrent);

    }


}