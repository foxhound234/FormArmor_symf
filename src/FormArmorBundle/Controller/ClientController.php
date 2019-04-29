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
        $user=$this->container->get('security.token_storage')->getToken()->getUser();
        $client_id = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'call session_autorisee(:client_id)';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('client_id', $client_id);
        $statement->execute();
        $RAW_QUERY = 'select s.id, f.libelle, niveau, description, date_debut as date from session_formation s, client c,
         formation f where s.id in (select id from id_session_autorisee) and c.id = :client_id and f.id = s.formation_id';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('client_id', $client_id);
        $statement->execute();
        $result = $statement->fetchAll();
        return $this->render('FormArmorBundle:Client:index.html.twig', array("sessions" => $result));
    }
    public function inscripAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'select s.id, f.libelle, date_debut as date from session_formation s,
        formation f where s.id = :id_session and f.id = s.formation_id';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('id_session', $id);
        $statement->execute();
        $result = $statement->fetchAll();
        return $this->render('FormArmorBundle:Client:inscription.html.twig', array("session" => $result));
    }
    public function confirmAction($id, Request $request)
    {
        $user=$this->container->get('security.token_storage')->getToken()->getUser();
        $client_id = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'insert into inscription (client_id, session_formation_id, date_inscription, etat) values (:id_client, :id_session, :date_inscription, \'\')';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('id_session', $id);
        $statement->bindValue('id_client', $id_client);
        $statement->bindValue('date_inscription', date("Y-m-d"));
        $statement->execute();
        return $this->redirectToRoute('form_armor_client');
    }
}
