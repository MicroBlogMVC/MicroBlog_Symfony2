<?php 

namespace MVC\UserBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Mvc\UserBundle\Entity\User;
use Mvc\UserBundle\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller 
{

    /**
     * Returns a list of all users
     */
    public function indexAction ()
    {
        // Get all users in ascending alphabetical order
        // via the User Repository (cf same folder as User Entity).
        $users = $this->getDoctrine()
                      ->getRepository('MvcUserBundle:User')
                      ->findAllASC();

        
        return $this->render('MvcUserBundle:User:all-users.html.twig', array(
            'users' => $users
        ));
    }





    /**
     * Returns user profile's informations and all messages by this user
     */
    public function profileAction($username, User $user)
    {
        // the 'username' property of the User Entity being unique, we can ask
        // Symfony to fetch this user for us by adding a User argument in the method 
        // signature. If it's not found, Symfony triggers a 404.

        return $this->render('MvcUserBundle:User:see-profile.html.twig',
                             array(
                                'user' => $user,
                                'messages' => $user->getMessages()
                             )
        );
    }





    /**
     * @Secure(roles="ROLE_USER")
     */
    public function editProfileAction()
    {
        $request = $this->getRequest();

        $user = $this->getUser();
        $form = $this->createForm(new ProfileType, $user);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()
                            ->getManager();
                $em->persist($user);
                $em->flush();
            
                return $this->redirect( 
                    $this->generateUrl('see_profile',
                                        array('username' => $user->getUsername())
                    )
                );            
            }
        }

        return $this->render('MvcUserBundle:User:edit-profile.html.twig', array(
                                'form' => $form->createView(),
                                'user' => $user
                            )
        );
    }




}