<?php

namespace Ez\ZipCodeCheckerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EzZipCodeCheckerBundle:Default:index.html.twig');
    }
}
