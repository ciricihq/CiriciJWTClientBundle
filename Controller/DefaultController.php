<?php

namespace Cirici\JWTClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CiriciJWTClientBundle:Default:index.html.twig');
    }
}
