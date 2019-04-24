<?php

namespace FormArmorBundle\Controller;

use FormArmorBundle\Form\ClientType;
use FormArmorBundle\Form\ClientCompletType;
use FormArmorBundle\Form\StatutType;
use FormArmorBundle\Form\FormationType;
use FormArmorBundle\Form\SessionType;
use FormArmorBundle\Form\PlanFormationType;

use FormArmorBundle\Entity\Client;
use FormArmorBundle\Entity\Formation;
use FormArmorBundle\Entity\Session_formation;
use FormArmorBundle\Entity\Plan_formation;
use FormArmorBundle\Entity\Statut;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;

class AdminController extends Controller
{
	public function __construct()
	{
		//$session = new Session();
		//ici on teste si la personne est un admin
		//if($session->get('nvAcces')!=2)
		//{
		//	return $this->redirectToRoute('form_armor_homepage');
		//}
        
    }


	public function redirectionAcceuil() // redirige vers l'acceuil
	{
		return $this->render('FormArmorBundle:Accueil:index.html.twig');
	}	
    public function authentifAction(Request $request) // Affichage du formulaire d'authentification
    {
        
		// Création du formulaire
		$client = new Client();
		$form   = $this->get('form.factory')->create(ClientType::class, $client);
		
		
		
		// Contrôle du mdp si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// Récupération des données saisies (le nom des controles sont du style nomDuFormulaire[nomDuChamp] (ex. : client[nom] pour le nom) )
				$donneePost = $request->request->get('client');
				$nom = $donneePost['nom'];
				$mdp = $donneePost['password'];
				
				// Controle du nom et du mdp
				$manager = $this->getDoctrine()->getManager();
				$rep = $manager->getRepository('FormArmorBundle:Client');
				$nbClient = $rep->verifMDP($nom, $mdp);
				if ($nbClient > 0)
				{
					return $this->render('FormArmorBundle:Admin:accueil.html.twig');
				}
				$request->getSession()->getFlashBag()->add('connection', 'Login ou mot de passe incorrects');
			}
		}
		
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:connection.html.twig', array('form' => $form->createView()));
    }
	
	// Gestion des statuts
	public function listeStatutAction($page)
	{
		if ($page < 1)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// On peut fixer le nombre de lignes avec la ligne suivante :
		// $nbParPage = 4;
		// Mais bien sûr il est préférable de définir un paramètre dans "app\config\parameters.yml", et d'y accéder comme ceci :
		$nbParPage = $this->container->getParameter('nb_par_page');
		
		
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$rep = $manager->getRepository('FormArmorBundle:Statut');
		$lesStatuts = $rep->listeStatuts($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesStatuts) qui retourne le nombre total de statuts
		$nbPages = ceil(count($lesStatuts) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:statut.html.twig', array(
		  'lesStatuts' => $lesStatuts,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifStatutAction($id, Request $request) // Affichage du formulaire de modification d'un statut
    {
        // Récupération du statut d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Statut');
		$statut = $rep->find($id);
		
		// Création du formulaire à partir du statut "récupéré"
		$form   = $this->get('form.factory')->create(StatutType::class, $statut);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($statut);
				$em->flush();
				
				// Réaffichage de la liste des statuts
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesStatuts = $rep->listeStatuts(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesStatuts) qui retourne le nombre total de statuts
				$nbPages = ceil(count($lesStatuts) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:statut.html.twig', array(
				  'lesStatuts' => $lesStatuts,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formStatut.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppStatutAction($id, Request $request) // Affichage du formulaire de suppression d'un statut
    {
        // Récupération du statut d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Statut');
		$statut = $rep->find($id);
		
		// Création du formulaire à partir du statut "récupéré"
		$form   = $this->get('form.factory')->create(StatutType::class, $statut);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant du statut à supprimer
			$donneePost = $request->request->get('statut');
			//$identif = $donneePost['id'];
			
			// mise à jour de la bdd
			$res = $rep->suppStatut($id);
			$em->persist($statut);
			$em->flush();
				
			// Réaffichage de la liste des statuts
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesStatuts = $rep->listeStatuts(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
			$nbPages = ceil(count($lesStatuts) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:statut.html.twig', array(
				'lesStatuts' => $lesStatuts,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formStatut.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
    }
	
	// Gestion des clients
	public function listeClientAction($page)
	{
		if ($page < 1)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// On peut fixer le nombre de lignes avec la ligne suivante :
		// $nbParPage = 4;
		// Mais bien sûr il est préférable de définir un paramètre dans "app\config\parameters.yml", et d'y accéder comme ceci :
		$nbParPage = $this->container->getParameter('nb_par_page');
		
		
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$rep = $manager->getRepository('FormArmorBundle:Client');
		$lesClients = $rep->listeClients($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesClients) qui retourne le nombre total de clients
		$nbPages = ceil(count($lesClients) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:client.html.twig', array(
		  'lesClients' => $lesClients,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifClientAction($id, Request $request) // Affichage du formulaire de modification d'un statut
    {
        // Récupération du client d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Client');
		$client = $rep->find($id);
		
		// Création du formulaire à partir du client "récupéré"
		$form   = $this->get('form.factory')->create(ClientCompletType::class, $client);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($client);
				$em->flush();
				
				// Réaffichage de la liste des clients
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesClients = $rep->listeClients(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesClients) qui retourne le nombre total de clients
				$nbPages = ceil(count($lesClients) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:client.html.twig', array(
				  'lesClients' => $lesClients,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formClient.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppClientAction($id, Request $request) // Affichage du formulaire de suppression d'un client
    {
        // Récupération du client d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Client');
		$client = $rep->find($id);
		
		// Création du formulaire à partir du client "récupéré"
		$form   = $this->get('form.factory')->create(ClientCompletType::class, $client);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant du client à supprimer
			$donneePost = $request->request->get('client');
			
			// mise à jour de la bdd
			$res = $rep->suppClient($id);
			$em->persist($client);
			$em->flush();
				
			// Réaffichage de la liste des clients
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesClients = $rep->listeClients(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesClients) qui retourne le nombre total de clients
			$nbPages = ceil(count($lesClients) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:client.html.twig', array(
				'lesClients' => $lesClients,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formClient.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
    }
	
	// Gestion des formations
	public function listeFormationAction($page)
	{
		if ($page < 1)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// On peut fixer le nombre de lignes avec la ligne suivante :
		// $nbParPage = 4;
		// Mais bien sûr il est préférable de définir un paramètre dans "app\config\parameters.yml", et d'y accéder comme ceci :
		$nbParPage = $this->container->getParameter('nb_par_page');
		
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$rep = $manager->getRepository('FormArmorBundle:Formation');
		$lesFormations = $rep->listeFormations($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
		$nbPages = ceil(count($lesFormations) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:formation.html.twig', array(
		  'lesFormations' => $lesFormations,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifFormationAction($id, Request $request) // Affichage du formulaire de modification d'une formation
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Formation');
		$formation = $rep->find($id);
		
		// Création du formulaire à partir de la formation "récupérée"
		$form   = $this->get('form.factory')->create(FormationType::class, $formation);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($formation);
				$em->flush();
				
				// Réaffichage de la liste des clients
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesFormations = $rep->listeFormations(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
				$nbPages = ceil(count($lesFormations) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:formation.html.twig', array(
				  'lesFormations' => $lesFormations,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formFormation.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppFormationAction($id, Request $request) // Affichage du formulaire de suppression d'une formation
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Formation');
		$formation = $rep->find($id);
		
		// Création du formulaire à partir de la formation "récupérée"
		$form   = $this->get('form.factory')->create(FormationType::class, $formation);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant de la formation à supprimer
			$donneePost = $request->request->get('formation');
			
			// mise à jour de la bdd
			$res = $rep->suppFormation($id);
			$em->persist($formation);
			$em->flush();
				
			// Réaffichage de la liste des formations
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesFormations = $rep->listeFormations(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesFormations) qui retourne le nombre total de formations
			$nbPages = ceil(count($lesFormations) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:formation.html.twig', array(
				'lesFormations' => $lesFormations,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formFormation.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
    }
	
	// Gestion des sessions
	public function listeSessionAction($page)
	{
		if ($page < 1)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// On peut fixer le nombre de lignes avec la ligne suivante :
		// $nbParPage = 4;
		// Mais bien sûr il est préférable de définir un paramètre dans "app\config\parameters.yml", et d'y accéder comme ceci :
		$nbParPage = $this->container->getParameter('nb_par_page');
		
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$rep = $manager->getRepository('FormArmorBundle:Session_formation');
		$lesSessions = $rep->listeSessions($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
		$nbPages = ceil(count($lesSessions) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:session.html.twig', array(
		  'lesSessions' => $lesSessions,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifSessionAction($id, Request $request) // Affichage du formulaire de modification d'une session
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Session_formation');
		$session = $rep->find($id);
		
		// Création du formulaire à partir de la session "récupérée"
		$form   = $this->get('form.factory')->create(SessionType::class, $session);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($session);
				$em->flush();
				
				// Réaffichage de la liste des sessions
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesSessions = $rep->listeSessions(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
				$nbPages = ceil(count($lesSessions) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:session.html.twig', array(
				  'lesSessions' => $lesSessions,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formSession.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppSessionAction($id, Request $request) // Affichage du formulaire de suppression d'une session
    {
        // Récupération de la session d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Session_formation');
		$session = $rep->find($id);
		
		// Création du formulaire à partir de la session "récupérée"
		$form   = $this->get('form.factory')->create(SessionType::class, $session);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// Récupération de l'identifiant de la session à supprimer
			$donneePost = $request->request->get('session');
			
			// mise à jour de la bdd
			$res = $rep->suppSession($id);
			$em->persist($session);
			$em->flush();
				
			// Réaffichage de la liste des formations
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesSessions = $rep->listeSessions(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
			$nbPages = ceil(count($lesSessions) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:session.html.twig', array(
				'lesSessions' => $lesSessions,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formSession.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
    }
	
	// Gestion des plans de formation
	public function listePlanFormationAction($page)
	{
		if ($page < 1)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// On peut fixer le nombre de lignes avec la ligne suivante :
		// $nbParPage = 4;
		// Mais bien sûr il est préférable de définir un paramètre dans "app\config\parameters.yml", et d'y accéder comme ceci :
		$nbParPage = $this->container->getParameter('nb_par_page');
		
		// On récupère l'objet Paginator
		$manager = $this->getDoctrine()->getManager();
		$rep = $manager->getRepository('FormArmorBundle:Plan_formation');
		$lesPlans = $rep->listePlans($page, $nbParPage);
		
		// On calcule le nombre total de pages grâce au count($lesPlans) qui retourne le nombre total de plans de formation
		$nbPages = ceil(count($lesPlans) / $nbParPage);
		
		// Si la page n'existe pas, on retourne une erreur 404
		if ($page > $nbPages)
		{
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		// On donne toutes les informations nécessaires à la vue
		return $this->render('FormArmorBundle:Admin:plan.html.twig', array(
		  'lesPlans' => $lesPlans,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}
	public function modifPlanFormationAction($id, Request $request) // Affichage du formulaire de modification d'un plan de formation
    {
        // Récupération de la formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Plan_formation');
		$plan = $rep->find($id);
		
		// Création du formulaire à partir du plan "récupéré"
		$form   = $this->get('form.factory')->create(PlanFormationType::class, $plan);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			if ($form->isValid())
			{
				// mise à jour de la bdd
				$em->persist($plan);
				$em->flush();
				
				// Réaffichage de la liste des sessions
				$nbParPage = $this->container->getParameter('nb_par_page');
				// On récupère l'objet Paginator
				$lesPlans = $rep->listePlans(1, $nbParPage);
				
				// On calcule le nombre total de pages grâce au count($lesSessions) qui retourne le nombre total de sessions
				$nbPages = ceil(count($lesPlans) / $nbParPage);
				
				// On donne toutes les informations nécessaires à la vue
				return $this->render('FormArmorBundle:Admin:plan.html.twig', array(
				  'lesPlans' => $lesPlans,
				  'nbPages'     => $nbPages,
				  'page'        => 1,
				));
			}
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formPlan.html.twig', array('form' => $form->createView(), 'action' => 'modification'));
    }
	public function suppPlanFormationAction($id, Request $request) // Affichage du formulaire de suppression d'un plan de formation
    {
        // Récupération du plan de formation d'identifiant $id
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Plan_formation');
		$plan = $rep->find($id);
		
		// Création du formulaire à partir du plan de formation "récupéré"
		$form   = $this->get('form.factory')->create(PlanFormationType::class, $plan);
		
		// Mise à jour de la bdd si method POST ou affichage du formulaire dans le cas contraire
		if ($request->getMethod() == 'POST')
		{
			$form->handleRequest($request); // permet de récupérer les valeurs des champs dans les inputs du formulaire.
			
			// mise à jour de la bdd
			$res = $rep->suppPlanFormation($id);
			$em->persist($plan);
			$em->flush();
				
			// Réaffichage de la liste des plans de formation
			$nbParPage = $this->container->getParameter('nb_par_page');
			// On récupère l'objet Paginator
			$lesPlans = $rep->listePlans(1, $nbParPage);
				
			// On calcule le nombre total de pages grâce au count($lesPlans) qui retourne le nombre total de plans de formation
			$nbPages = ceil(count($lesPlans) / $nbParPage);
				
			// On donne toutes les informations nécessaires à la vue
			return $this->render('FormArmorBundle:Admin:plan.html.twig', array(
				'lesPlans' => $lesPlans,
				'nbPages'     => $nbPages,
				'page'        => 1,
				));
		}
		// Si formulaire pas encore soumis ou pas valide (affichage du formulaire)
		return $this->render('FormArmorBundle:Admin:formPlan.html.twig', array('form' => $form->createView(), 'action' => 'SUPPRESSION'));
	}
	public function ListeValidationIncription()
	{
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Plan_formation');
		$plan = $rep->findAll();
	}
	public function ListeValidationSessionAction()
	{
		$em = $this->getDoctrine()->getManager();
		$rep = $em->getRepository('FormArmorBundle:Session_formation');
		$nbParPage = $this->container->getParameter('nb_par_page');
		// On récupère l'objet Paginator
		$lesSessions= $rep->listeSessions(1, $nbParPage);
		$nbPages = ceil(count($lesSessions) / $nbParPage);
	

		return $this->render('FormArmorBundle:Admin:ListSessionValid.html.twig', array(
			'lesSessions' => $lesSessions,
			'nbPages'     => $nbPages,
			'page'        => 1,
			));
	}

	public function AffichesessionAction($id, Request $request)
		{
			$em = $this->getDoctrine()->getManager();
			$er=$this->getDoctrine()->getManager();
			$repsession = $em->getRepository('FormArmorBundle:Session_formation');
			$session= $repsession->find($id);
		
			$repincrit= $er->getRepository('FormArmorBundle:Inscription');

		  $inscrit=$repincrit->findBy(array('session_formation'=>$session));

			if ($request->getMethod() == 'POST') {
				$Modif = $request->request->get('ModifAnnul');
				foreach($inscrit as $client)
				{
					$message = (new \Swift_Message('Annulation Session'))
					->setFrom('morganlb347@gmail.com')
					->setTo('morganlb@hotmail.fr')
					->setBody(
						$this->render('FormArmorBundle:Emails:AnnulationSession.html.twig', array(
							'item' => $client,
						  'modif'=>$Modif
						)),
							'text/html'
						);
		
						$this->get('mailer')->send($message);

						if($session->getFormation()->getTypeForm()=="Bureautique")
						{
						 $Duree=$session->getFormation()->getDuree();
             $NvDureeBureau=$client->getClient()->getNbhbur()-$Duree ;
						 $client->getClient()->setNbhbur($NvDureeBureau);
						 $er->flush();
	
						}else
						{
							$Duree=$session->getFormation()->getDuree();
							$NvDureeCompta=$client->getClient()->getNbhcpta() - $Duree ;
							$client->getClient()->setNbhcpta($NvDureeCompta);
							$er->flush();
						}
						
						$er->remove($client);	
				}
				$er->flush();

				$res=$repsession->suppSession($id);
				$em->persist($session);
				$em->flush();

			 return $this->redirectToRoute('form_armor_admin_ListeSession');
				}
			



			return $this->render('FormArmorBundle:Admin:SessionValidation.html.twig', array(
				'laSession' => $session,
				'inscriptions'=>$inscrit
			));
      
		}

		public function ValidationSessionAction($id)
		{
			$em = $this->getDoctrine()->getManager();
			$er=$this->getDoctrine()->getManager();
			$repsession = $em->getRepository('FormArmorBundle:Session_formation');
			$session= $repsession->find($id);
		
		

			$repincrit= $er->getRepository('FormArmorBundle:Inscription');
			$inscrit=$repincrit->findBy(array('session_formation'=>$session));


			foreach ($inscrit as  $client) 
			{
				$message = (new \Swift_Message('Validation inscription'))
				->setFrom('morganlb347@gmail.com')
				->setTo('morganlb@hotmail.fr')
				->setBody(
						$this->renderView(
								// app/Resources/views/Emails/registration.html.twig
								'FormArmorBundle:Emails:ValidationSession.html.twig',
								['item' => $client]
						),
						'text/html'
					);
	
					$this->get('mailer')->send($message);
			}
			$session->setClose(true);
			$em->flush();

		
				return $this->redirectToRoute('form_armor_admin_ListeSession');
		}

		public function DeconnectionAction()
		{
			return $this->redirectionAcceuil();
		}

}

		