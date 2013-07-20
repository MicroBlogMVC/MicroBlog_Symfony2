<?php 

namespace Mvc\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Mvc\BlogBundle\Entity\Message;

class Messages extends AbstractFixture implements OrderedFixtureInterface 
{


    public function load(ObjectManager $manager)
    {        

        $messages = array(
            array(
                'message' => 'Just setting up my tiny Twitter',
                'user' => 'Steven'
            ),
            array(
                'message' => 'Nothing wrong with a man taking pleasure in his work',
                'user' => 'John'
            ),
            array(
                'message' => 'If you don\'t like what\'s being said, change the conversation',
                'user' => 'Jane'
            ),
            array(
                  'message' => 'I don\'t know what to say',
                  'user' => 'Nobody'
            )
        );

       
        foreach ($messages as $infos) {
            $message = new Message;

            $message->setMessage($infos['message']);
            $message->setUser(
                $manager->merge($this->getReference($infos['user']))
            );
           
            $manager->persist($message);
        }

        $manager->flush();
    }





    public function getOrder()
    {
        return 2;
    }



}