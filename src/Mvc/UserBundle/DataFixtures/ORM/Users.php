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
     * Allow us to create some users into the database
     */
    public function load(ObjectManager $manager)
    {

        $encodeFactory = $this->container->get('security.encoder_factory');

        // Describe all users
        $usersInfos = array(
            array(
                'username' => 'Abraham_Lincoln',
                'bio' => '16th President of the United States',
                'avatar' => 'jpg'
            ),            
            array(
                'username' => 'Albert_Einstein',
                'bio' => 'Theoretical physicist who developed the general theory of relativity',
                'avatar' => 'jpg'
            ),
            array(
                'username' => 'Edgar_Allan_Poe',
                'bio' => 'Author, poet, editor, and literary critic, considered part of the American Romantic Movement',
                'avatar' => 'jpg'
            ),
            array(
                'username' => 'Emily_Dickinson',
                'bio' => 'American poet',
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