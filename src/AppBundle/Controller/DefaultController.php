<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    /**
     * @Route("/home", name="homepage")
     * @Security("is_granted('ROLE_USER')")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('Api/index.html.twig');
    }
}
