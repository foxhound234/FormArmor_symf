<?php

namespace FormArmorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class AccueilController extends Controller
{
    public function __construct()
    {
        $session = new Session();
        //$session->start(); hihi

        // ébauche de contrôle utilisateur
        $session->set('nvAcces', 0);
        echo $session->get('nvAcces');

        // set flash messages
        //$session->getFlashBag()->add('notice', 'Profile updated');

        // retrieve messages
        /*foreach ($session->getFlashBag()->get('notice', []) as $message) {
            echo '<div class="flash-notice">'.$message.'</div>';  
        }*/
    }

    public function indexAction()
    {
        return $this->render('FormArmorBundle:Accueil:index.html.twig');
    }
}
