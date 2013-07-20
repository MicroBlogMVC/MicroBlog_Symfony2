<?php

namespace Mvc\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MvcBlogBundle:Default:index.html.twig', array('name' => $name));
    }
}
