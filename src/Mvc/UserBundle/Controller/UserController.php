<?php 

namespace MVC\UserBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Mvc\UserBundle\Entity\User;
use Mvc\UserBundle\Form\UserType;
use Mvc\UserBundle\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

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

        
        return $this->render('MvcUserBundle:User:list.html.twig', array(
            'users' => $users
        ));
    }






    /**
     * Returns a registration form
     * or creates a new user if data was posted (and valid) and redirects to the
     * home page.
     */
    public function signUpAction()
    {

        $request = $this->getRequest();

        // Instantiate a new User and a new form for registration
        $user = new User;
        $form = $this->createForm(new UserType, $user);

        // Check if the form was submitted, according to the request method
        if ($request->getMethod() === 'POST') {
            $form->bind($request); // hydrate User object with posted data

            if ($form->isValid()) { // data valid ? cf User Entity @Assert annotations                
                

                // SET password via the security service.
                $encoderFactory = $this->container->get('security.encoder_factory');
                $encoder = $encoderFactory->getEncoder($user);
                $hashedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($hashedPassword);

                // Give this new User to Doctrine for saving it.
                $em = $this->getDoctrine()
                           ->getManager();      

                $em->persist($user);                
                $em->flush();


                // 'Manual' authentication
                $this->logUser($user);

                // Redirect to home page
                return $this->redirect($this->generateUrl('home'));
            }
        }

        // If no data, or invalid data, returns the SignIn form to the template
        return $this->render('MvcUserBundle:User:sign-up.html.twig',
                             array (
                                'form' => $form->createView()
                             )
        );
    }





    /**
     * Returns a user profile's informations and all messages by this user
     */
    public function profileAction($username, User $user)
    {
        // the 'username' property of the User Entity being unique, we can ask
        // Symfony to fetch this user for us by adding a User argument in the method 
        // signature. If it's not found, Symfony triggers a 404.

        return $this->render('MvcUserBundle:User:profile.html.twig',
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
                    $this->generateUrl('user_profile',
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











    /**
     *  Log the user in after a successful registration
     */
    private function logUser(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->container->get('security.context')->setToken($token);
    }




}