<?php
namespace Realtimedraw\ServerBundle;

use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface as Conn;
use Doctrine\ORM\EntityManager;

class Server implements WampServerInterface {
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct($em){
        $this->em = $em;
        echo "WampServer started\n";
    }

    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible) {
        $topic->broadcast($event);
    }

    public function onCall(Conn $conn, $id, $topic, array $params) {
        $v = strpos($topic, '/', 1);
        $category = substr($topic, 1, $v-1);
        $element = substr($topic, $v+1);
        switch($category){
            case 'command':
                if(!method_exists($this, 'command_'.$element)){
                    $conn->callError($id, $topic, 'unknown');
                }
                break;
        }
        $conn->callError($id, $topic, 'RPC not supported on this demo');
    }

    // No need to anything, since WampServer adds and removes subscribers to Topics automatically
    public function onSubscribe(Conn $conn, $topic) {
        var_dump($topic);
    }
    public function onUnSubscribe(Conn $conn, $topic) {}

    public function onOpen(Conn $conn) {}
    public function onClose(Conn $conn) {}
    public function onError(Conn $conn, \Exception $e) {}

    protected function command_addx(){

    }
}
