<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findNonInscrits($session_id){
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();


        $qb = $sub;
        //select tous les stagiaires d'une session dont l'id est passé en paramètre 
        $qb->select('s')
            ->from('App\Entity\stagiaire','s')
            ->leftJoin('s.sessions','se')
            ->where('se.id = :id');

        // Crée un nouveau QueryBuilder pour construire une autre requête.
        $sub = $em->createQueryBuilder();
        $sub->select('st')
            ->from('App\Entity\stagiaire','st')
            ->where($sub->expr()->notIn('st.id',$qb->getDQL()))
            //requete parametrer 
            ->setParameter('id',$session_id)
            //trier la liste des stagiaires sur le nom de famille 
            ->orderBy('st.nom');

        //renvoyer le resultat 
        $query = $sub->getQuery();
        return $query->getResult();

    }

    public function findNonProgrammes($programme_id) {
        $em = $this->getEntityManager();
        
        // Création du QueryBuilder pour la sous-requête
        $sub = $em->createQueryBuilder();
        
        
        $qb = $sub;
        // Construction de la sous-requête pour obtenir les modules associés au programme de la session
        $qb->select('m') 
            ->from('App\Entity\ModuleSession', 'm')  
            ->leftJoin('m.programmes', 'mod')  
            ->where('mod.session = :id')  ;
    
        
        // Création du QueryBuilder pour la requête principale
        $sub = $em->createQueryBuilder();
        $sub->select('mt')
            ->from('App\Entity\ModuleSession', 'mt')  // Entité des modules
            ->where($sub->expr()->notIn('mt.id', $qb->getDQL())) 
            // ->orderBy('mt.nom');  // Tri par le nom des modules
            ->setParameter('id',$programme_id);

        // Exécution de la requête et retour des résultats
        $query = $sub->getQuery();
        return $query->getResult();
    }
    
    
    
}
