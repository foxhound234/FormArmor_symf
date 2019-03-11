<?php

namespace FormArmorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{
    public function __construct()
	{
		//ici on teste si la personne est un admin
		//if($session->get('nvAcces')!=1)
		//{
			//return $this->redirectToRoute('form_armor_homepage');
		//}
        
    }

    public function indexAction()
    {
        return $this->render('FormArmorBundle:Client:index.html.twig');
    }
}
