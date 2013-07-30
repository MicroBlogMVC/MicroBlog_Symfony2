<?php
 
namespace Mvc\UserBundle\Controller;
 
use Mvc\UserBundle\Entity\User;
use Mvc\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
 
class SecurityController extends Controller
{

    
    public function loginAction()
    {
        // if the user is already logged in -> redirect to home page
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } 
        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('MvcUserBundle:Security:sign-in.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }






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
              
                // Set password via the security service.
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

        // If no data, or invalid data, returns the SignIn form 
        return $this->render('MvcUserBundle:Security:sign-up.html.twig',
                             array ('form' => $form->createView() 
        ));
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