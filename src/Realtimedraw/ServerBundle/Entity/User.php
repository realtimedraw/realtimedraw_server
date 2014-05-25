<?php
namespace Realtimedraw\ServerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users",uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_fb_id", columns={"facebook_id"})
 * })
 */
class User extends  BaseUser{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="facebook_id")
     */
    private $facebookId;

    /**
     * @ORM\OneToMany(targetEntity="Drawing", mappedBy="owner")
     */
    private $drawings;

    /**
     * @ORM\ManyToMany(targetEntity="Drawing", inversedBy="allowedUsers")
     * @ORM\JoinTable(name="shared_drawings")
     */
    private $sharedDrawings;

    public function __construct($facebookId)
    {
        parent::__construct();
        // your own logic
        $this->drawings = new ArrayCollection();
        $this->sharedDrawings = new ArrayCollection();
        $this->facebookId = $facebookId;
    }
}
