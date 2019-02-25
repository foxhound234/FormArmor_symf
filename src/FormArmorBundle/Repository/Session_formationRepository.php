<?php

namespace FormArmorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Session_formationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Session_formationRepository extends \Doctrine\ORM\EntityRepository
{
	public function listeSessions($page, $nbParPage) // Liste toutes les sessions avec pagination
	{
		$queryBuilder = $this->createQueryBuilder('s');

		// On n'ajoute pas de critère ou tri particulier ici car on veut tous les statuts, la construction
		// de notre requête est donc finie

		// On récupère la Query à partir du QueryBuilder
		$query = $queryBuilder->getQuery();

		// On gère ensuite la pagination grace au service Paginator qui nous fournit
		// entre autres les méthodes setFirstResult et setMaxResults
		$query
		  // On définit la formation à partir de laquelle commencer la liste
		  ->setFirstResult(($page-1) * $nbParPage)
		  // Ainsi que le nombre de formations à afficher sur une page
		  ->setMaxResults($nbParPage)
		;

		// Enfin, on retourne l'objet Paginator correspondant à la requête construite
		// (=>Ne pas oublier le "use Doctrine\ORM\Tools\Pagination\Paginator;" correspondant en début de fichier)
		return new Paginator($query, true);
	}
	public function suppSession($id) // Suppression de la session d'identifiant $id
	{
		$qb = $this->createQueryBuilder('s');
		$query = $qb->delete('FormArmorBundle\Entity\Session_formation', 's')
		  ->where('s.id = :id')
		  ->setParameter('id', $id);
		
		return $qb->getQuery()->getResult();
	}

}
