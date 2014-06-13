<?php
namespace Realtimedraw\ServerBundle;

use Facebook\FacebookSession;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface as Conn;
use Doctrine\ORM\EntityManager;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Realtimedraw\ServerBundle\Entity\Drawing;
use Realtimedraw\ServerBundle\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Ratchet\Wamp\WampConnection;

class Server implements WampServerInterface
{
    protected $em;
    private $facebook_appID = '566601083457511';
    private $facebook_appSecret = '0c0f6192726dca65686aa75aea292d32';
    private $directory = 'uploads/drawings';

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        FacebookSession::setDefaultApplication($this->facebook_appID, $this->facebook_appSecret);
        echo "WampServer started\n";
    }

    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {
        $topic->broadcast($event);
    }

    public function onCall(Conn $conn, $id, $topic, array $params)
    {
        /** @var WampConnection $conn */
        $command_method = 'command_' . $topic;
        if (!method_exists($this, $command_method)) {
            $conn->callError($id, $topic, 'unknown command');
        } else {
            try {
                $result = $this->$command_method($params);
                $conn->callResult($id, array($result));
            } catch (Exception $ex) {
                $conn->callError($id, $topic, 'error occurred', $ex->getMessage());
            }
        }
    }

    // No need to anything, since WampServer adds and removes subscribers to Topics automatically
    public function onSubscribe(Conn $conn, $topic)
    {
        var_dump($topic);
    }

    public function onUnSubscribe(Conn $conn, $topic)
    {
    }

    public function onOpen(Conn $conn)
    {
    }

    public function onClose(Conn $conn)
    {
    }

    public function onError(Conn $conn, \Exception $e)
    {
    }

    protected function command_auth(array $params)
    {
        $facebookAccessToken = $params[0];
        try {
            $facebook_user = $this->getFacebookUser($facebookAccessToken);
            $user = $this->getUser($facebook_user);
            return $user->getId();
        } catch (FacebookRequestException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            if ($ex->getMessage() != 'user not found')
                throw $ex;
            $user = new User($facebook_user->getId());
            $this->em->persist($user);
            $this->em->flush();
            return $user->getId();
        }
    }

    protected function command_uploadDrawing($facebookAccessToken, $uploadedDrawing)
    {
        $facebook_user = $this->getFacebookUser($facebookAccessToken);
        $user = $this->getUser($facebook_user);
        $drawing = new Drawing($user->getId());
        $this->em->persist($drawing);
        $this->em->flush();
        file_put_contents($this->directory . '/id' + $drawing->getId(), $uploadedDrawing);
        return $drawing->getId();
    }

    protected function command_downloadDrawing($facebookAccessToken, $drawingId)
    {
        $facebook_user = $this->getFacebookUser($facebookAccessToken);
        $user = $this->getUser($facebook_user);
        /** @var Drawing $drawing */
        $drawing = $this->em->getRepository('RealtimedrawServerBundle:Drawing')->find($drawingId);
        if (!$drawing->getAllowedUsers()->exists($user))
            throw new \Exception('not allowed');
        return file_get_contents($this->directory . '/id' + $drawing->getId());
    }

    /**
     * @param $facebookAccessToken
     * @return GraphUser
     */
    protected function getFacebookUser($facebookAccessToken)
    {
        $session = new FacebookSession($facebookAccessToken);
        $session->validate();
        $user_profile = (new FacebookRequest(
            $session, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className());
        return $user_profile;
    }

    /**
     * @param GraphUser $facebook_user
     * @throws \Exception
     * @return User
     */
    protected function getUser(GraphUser $facebook_user)
    {
        $user = $this->em->getRepository('RealtimedrawServerBundle:User')->findOneByFacebookId($facebook_user->getId());
        if ($user)
            return $user;
        throw new \Exception('user not found');
    }
}
