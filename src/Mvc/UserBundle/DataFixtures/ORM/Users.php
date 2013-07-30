<?php 

namespace Mvc\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Mvc\UserBundle\Entity\Avatar;
use Mvc\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Users extends AbstractFixture implements OrderedFixtureInterface, 
                                               ContainerAwareInterface
{

    /**
     * @var ContainerInterface
    */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }




    /**
     * Allow us to create some fake users into the database
     */
    public function load(ObjectManager $manager)
    {

        $encodeFactory = $this->container->get('security.encoder_factory');

        // Describe all users
        $usersInfos = array(
            array(
                'username' => 'Steven',
                'bio' => 'Steven\'s bio',
                'avatar' => 'jpeg' 
            ),
            array(
                'username' => 'John',
                'bio' => 'John\'s bio',
                'avatar' => 'jpg' 
            ),
            array(
                'username' => 'Jane',
                'bio' => 'Jane\'s bio',
                'avatar' => 'jpg' 
            ),
            array(
                'username' => 'Nobody',
                'bio' => 'Nobody\'s bio',
                'avatar' => 'jpg' 
            )
        );


        $encoderFactory = $this->container->get('security.encoder_factory');



        // Create each users
        foreach ($usersInfos as $user => $infos) {
            
            $user = new User;

            // SET username, bio, avatar
            $user->setUsername($infos['username']);
            $user->setBio($infos['bio']);
            $user->setAvatar($infos['avatar']);

            // SET password via the security service.
            $encoder = $encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword('pass', $user->getSalt()));

            
            // We add a reference to this object so we can use it later when 
            // creating message(s) for this user, via the DataFixtures
            // --> cf Mvc\BlogBundle\DataFixtures\ORM\Messages
            $this->addReference($infos['username'], $user);

            $manager->persist($user);
        }

        // Save all users in the DB
        $manager->flush();
    }




    /**
     * This method tells doctrine to create the Users before the Messages 
     * (because of the constraints between these two entities)
     */
    public function getOrder()
    {
        return 1;
    }



}