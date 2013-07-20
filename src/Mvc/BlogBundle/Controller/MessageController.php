<?php 

namespace MVC\BlogBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Mvc\BlogBundle\Entity\Message;
use Mvc\BlogBundle\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MessageController extends Controller 
{



    /**
     * Returns a list of all messages
     */
    public function indexAction()
    {
        // select all messages from the databases
        $messages = $this->getMessageRepo()->findAllWithUsers();

        return $this->render('MvcBlogBundle:Message:timeline.html.twig',
                             array('messages' => $messages)
        );
    }





    /**
     * Returns a form for creating a new message 
     * or saves it if data was passed (and valid) before 
     * redirecting to the home page
     *    
     * @Secure(roles="ROLE_USER")
     */
    public function newAction()
    {
        $request = $this->getRequest();

        $message = new Message;
        $form = $this->createForm(new MessageType, $message);

        if ($request->getMethod() === 'POST'){
            $form->bind($request);

            if ($form->isValid()) {
                $message->setUser($this->getUser());

                $em = $this->getDoctrine()
                            ->getManager();

                $em->persist($message);
                $em->flush();

                $this->get('session')
                     ->getFlashBag()
                     ->add('success', 'Your message has been posted !');

                return $this->redirect($this->generateUrl('home'));
            }
        }

        return $this->render('MvcBlogBundle:Message:new-message.html.twig', array(
            'form' => $form->createView()
        ));
    }





    /**
     * Deletes a message and redirects to the referer page
     *
     * @Secure(roles="ROLE_USER")
     */
    public function deleteAction($id, Message $message)
    {
        $connectedUser = $this->getUser();
        $messageAuthor = $message->getUser();

       // we check that the user who asked for the delete is the author of this message
        if ($connectedUser === $messageAuthor) {
           
            $em = $this->getDoctrine()
                       ->getManager();

            $em->remove($message);
            $em->flush();

            // flash message for confirmation
            $this->get('session')
                 ->getFlashBag()
                 ->add('info', 'Your message has been deleted.');

            // rediection to the referer
            $referer = $this->get('request')->headers->get('referer');      
            return $this->redirect($referer);

        }
        else {
            throw new AccessDeniedHttpException('You are not the author of this message');
        }
    }







    private function getMessageRepo()
    {
        return $this->getDoctrine()
                    ->getRepository('MvcBlogBundle:Message');
    }


}