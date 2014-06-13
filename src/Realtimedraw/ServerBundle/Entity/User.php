<?php
namespace Realtimedraw\ServerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users",uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_fb_id", columns={"facebook_id"})
 * })
 */
class User{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint", name="facebook_id")
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
        $this->drawings = new ArrayCollection();
        $this->sharedDrawings = new ArrayCollection();
        $this->facebookId = $facebookId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param mixed $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return mixed
     */
    public function getDrawings()
    {
        return $this->drawings;
    }

    /**
     * @return mixed
     */
    public function getSharedDrawings()
    {
        return $this->sharedDrawings;
    }

    /**
     * Add drawings
     *
     * @param \Realtimedraw\ServerBundle\Entity\Drawing $drawings
     * @return User
     */
    public function addDrawing(\Realtimedraw\ServerBundle\Entity\Drawing $drawings)
    {
        $this->drawings[] = $drawings;

        return $this;
    }

    /**
     * Remove drawings
     *
     * @param \Realtimedraw\ServerBundle\Entity\Drawing $drawings
     */
    public function removeDrawing(\Realtimedraw\ServerBundle\Entity\Drawing $drawings)
    {
        $this->drawings->removeElement($drawings);
    }

    /**
     * Add sharedDrawings
     *
     * @param \Realtimedraw\ServerBundle\Entity\Drawing $sharedDrawings
     * @return User
     */
    public function addSharedDrawing(\Realtimedraw\ServerBundle\Entity\Drawing $sharedDrawings)
    {
        $this->sharedDrawings[] = $sharedDrawings;

        return $this;
    }

    /**
     * Remove sharedDrawings
     *
     * @param \Realtimedraw\ServerBundle\Entity\Drawing $sharedDrawings
     */
    public function removeSharedDrawing(\Realtimedraw\ServerBundle\Entity\Drawing $sharedDrawings)
    {
        $this->sharedDrawings->removeElement($sharedDrawings);
    }
}
