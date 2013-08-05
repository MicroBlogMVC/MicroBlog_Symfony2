<?php

namespace Mvc\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MicroBlog\Images\ImageResizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Mvc\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="username", message="This username is already used")
 */
class User implements UserInterface
{
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
     * @ORM\Column(name="username", type="string", length=30, unique=true)
     * @Assert\NotBlank
     * @Assert\Regex(pattern= "/^[a-z0-9_-]+$/i", message= "The username may only contain letters, numbers, underscores and dashes")
     * @Assert\Length(min=3, max=30)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=40)
     * @Assert\NotBlank
     */
    private $password;


    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=32)
     */
    private $salt;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    private $roles;


    /**
     * @var string
     *
     * @ORM\Column(name="bio", type="string", length=160, nullable=true)
     * @Assert\Length(max=160)
     */
    private $bio;



    /**
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    private $avatar;



    /**
     * @ORM\OneToMany(targetEntity="Mvc\BlogBundle\Entity\Message", mappedBy="user")
     */
    private $messages;


    
    /**
     * @Assert\Valid()
     * @Assert\File(maxSize = "2M", mimeTypes = {
     *   "image/jpeg",
     *   "image/jgif",
     *   "image/png"
     * })
     */
    private $file;

    
    private $previousFileName;







    /*---------------------------
    -----------------------------
        CONSTRUCTOR
    -----------------------------    
    ---------------------------*/

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->salt = md5(uniqid(null, true));
        $this->roles = array('ROLE_USER');
    }







    /*---------------------------
    -----------------------------
        CALLBACKS
    -----------------------------    
    ---------------------------*/


    /**
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // set image name
            $this->avatar = $this->getFile()->guessExtension();
        }
    }



    /**
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // if no file selected, nothing to do
        if (null === $this->file) { 
            return;
        }

        // delete previous avatar if exists
        if (null !== $this->previousFileName) {
            $oldFile = $this->getUploadRootDir().'/' . $this->previousFileName;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        
        // load the new one
        $newAvatarName =  $this->id.'.'.$this->getFile()->guessExtension();
  
        $this->file->move(
            $this->getUploadRootDir(),
            $newAvatarName
        );


        // resize it
        $resizer = new ImageResizer;
        $resizer->crop($this->getUploadRootDir() . '/' . $newAvatarName, 50, 50);

        $this->file = null;
    }





    /*---------------------------
    -----------------------------
        INTERFACE METHOD
    -----------------------------    
    ---------------------------*/

    public function eraseCredentials() {}





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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }



    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }




    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }



    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    
        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }



    /**
     * Set bio
     *
     * @param string $bio
     * @return User
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    
        return $this;
    }

    /**
     * Get bio
     *
     * @return string 
     */
    public function getBio()
    {
        return $this->bio;
    }



    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }



    /**
     * Add message
     *
     * @param \Mvc\BlogBundle\Entity\Message $message
     * @return User
     */
    public function addMessage(\Mvc\BlogBundle\Entity\Message $message)
    {
        $this->messages[] = $message;
        $message->setUser($this);

    
        return $this;
    }

    /**
     * Remove message
     *
     * @param \Mvc\BlogBundle\Entity\Message $message
     */
    public function removeMessage(\Mvc\BlogBundle\Entity\Message $message)
    {
        $this->messages->removeElement($message);
        $message->setUser(null);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }



    /**
     * Set file
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return User
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        if (null !== $this->avatar) {
            $this->previousFileName = $this->id .'.' . $this->avatar;
            $this->avatar = null;
        }

        return $this;
    }

    /**
     * Get file
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile;
     */
    public function getFile()
    {
        return $this->file;
    }



    /**
     * Get upload dir
     *
     * @return string
     */
    public function getUploadDir()
    {
        return 'avatars';
    }



    /**
     * Get upload root dir
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/' . $this->getUploadDir();
    }

}