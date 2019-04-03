<?php

namespace FormArmorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class AccueilController extends Controller
{
    public function __construct()
    {
        
    }

    public function indexAction()
    {
        return $this->render('FormArmorBundle:Accueil:index.html.twig');
    }
}
