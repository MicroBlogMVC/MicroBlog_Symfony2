<?php

namespace Mvc\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mvc\BlogBundle\Entity\MessageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Message
{

    /*---------------------------
    -----------------------------
        PROPERTIES
    -----------------------------    
    ---------------------------*/

    /**
     * @ORM\ManyToOne(targetEntity="Mvc\UserBundle\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=140)
     * @Assert\NotBlank()
     * @Assert\Length(max=140)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;











    /*---------------------------
    -----------------------------
        GETTERS & SETTERS
    -----------------------------    
    ---------------------------*/



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Message
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \Mvc\UserBundle\Entity\User $user
     * @return Message
     */
    public function setUser(\Mvc\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Mvc\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }





    /*---------------------------
    -----------------------------
        CALLBACKS
    -----------------------------    
    ---------------------------*/
    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        // set default date
        $this->createdAt = new \Datetime();
    }


}