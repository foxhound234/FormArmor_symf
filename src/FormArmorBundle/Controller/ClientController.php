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
    public function indexAction()
    {
        return $this->render('FormArmorBundle:Client:index.html.twig');
    }
}
