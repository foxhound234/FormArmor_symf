<?php

namespace FormArmorBundle\Controller;

use FormArmorBundle\Form\ClientType;

use FormArmorBundle\Entity\Client;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;

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
