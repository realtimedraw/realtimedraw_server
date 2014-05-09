<?php
namespace Realtimedraw\ServerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\App;

class WampServerCommand extends ContainerAwareCommand {
    protected function configure()
    {
        $this
            ->setName('wampserver:run')
            ->setDescription('Start the WAMP server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $wampsv = $this->getContainer()->get('wampsv');
        $server = new App('192.168.1.3', 8080, '0.0.0.0');
        $server->route('/pubsub', $wampsv, array('*'));
        $server->run();
    }
}
