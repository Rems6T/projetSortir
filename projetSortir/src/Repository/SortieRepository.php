<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Model\Filtre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Filtre $filtre
     * @param UserInterface $user
     * @return Paginator
     */
    public function findByRecherche(Filtre $filtre, UserInterface $user, $sorties)
    {

        $queryBuilder = $this->createQueryBuilder('s')
               //jonction avec participant
         ->join('s.participantsInscrits', 'si')
        ->join('s.etat', 'e');

        if ($sorties!= null) {
            $queryBuilder
                ->select('e.libelle')
                ->where('e.libelle' != 'Archivée');
           //     ->select('e.libelle')
            //    $queryBuilder->andWhere('s.etat != :etat')
              //      ->setParameter('etat', 'Archivée');
          //  ->where('e.libelle' != 'Archivée');
        }

            if ($filtre->getSearch() != null) {
                //where pour la recherche
                $queryBuilder->andWhere('s.nom LIKE :nom')
                    ->setParameter('nom', '%' . $filtre->getSearch() . '%');
            }
            //where pour le campus si non nulle
            if ($filtre->getCampus() != null) {
                $queryBuilder->andWhere('s.siteOrganisateur = :campus')
                    ->setParameter('campus', $filtre->getCampus());
            }
            //where date Heure debut < date min si remplie
            if (!empty($filtre->getDateDebut())) {
                $queryBuilder->andWhere('s.dateHeureDebut > :dateDebut')
                    ->setParameter('dateDebut', $filtre->getDateDebut());
            }
            //where date limite inscription > date max si rempli
            if (!empty($filtre->getDateLimite())) {
                $queryBuilder->andWhere('s.dateLimiteInscription < :dateLimite')
                    ->setParameter('dateLimite', $filtre->getDateLimite());
            }
            //where organisateur egale au user si est_organisateur non null
            if ($filtre->getEstOrganisateur() != null) {
                $queryBuilder->andWhere('s.organisateur = :user')
                    ->setParameter('user', $user);
            }
            //where user egale aux participants de la sortie si est_inscrit non null
            if ($filtre->getEstInscrit() != null) {
                $queryBuilder->andWhere('si.pseudo = :user')
                    ->setParameter('user', $user->getUserIdentifier());
            }
            //where user diferent des participants de la sortie si pas_inscrit non null
            if ($filtre->getPasInscrit() != null) {
                $queryBuilder->andWhere('si.pseudo != :user')
                    ->setParameter('user', $user->getUserIdentifier());
            }
            //where etat de la sortie = fermée si sortie_termine non null
            if ($filtre->getEstPassees() != null) {
                $queryBuilder->andWhere('s.etat != :etat')
                    ->setParameter('etat', 'fermée');
            }
            //On execute la requete
            $query = $queryBuilder->getQuery();
            $query->setMaxResults(50);
            $paginator = new Paginator($query);

            return $paginator;
        }


    public function archive() {{
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s.etat', 's')
        ->where('s.libelle', 'Créée')
        ->orWhere('s.libelle', 'Ouverte')
        ->orWhere('s.libelle', 'Activité en cours')
            ->orWhere('s.libelle', 'Passée')
            ->orWhere('s.libelle', 'Annulee')
        ->orWhere('s.libelle', 'Clôturée');

        $query = $queryBuilder->getQuery();
        return $query;

    }}




/*
    public function findAllWithSitesAndEtats()
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.siteOrganisateur', 'si')
            ->addSelect('si')
            ->addSelect('e')
            ->getQuery()
            ->getResult();
    }

    public function findBySearch($search)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.nom LIKE :mot')
            ->setParameter('mot', $search)
            ->getQuery();
        return $query->getResult();
    }


    public function findByCampus($Campus)
    {
        return $this->createQueryBuilder('s')
            //  ->innerJoin('s.siteOrganisateur', 'si')
            ->andWhere('s.siteOrganisateur =  :campus')
            ->setParameter('campus', $Campus)
            ->getQuery()
            ->getResult();
    }

    public function findByDates($dateMin, $dateMax, $idCampus, $search)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.siteOrganisateur', 'si')
            ->andWhere('s.dateHeureDebut >= :dateDebut')
            ->andWhere('s.dateLimiteInscription <= :dateFin')
            ->andWhere('si.id =  :site')
            ->andWhere('s.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('site', $idCampus)
            ->setParameter('dateDebut', $dateMin)
            ->setParameter('dateFin', $dateMax)
            ->getQuery()
            ->getResult();
    }

    public function findByIdOrganisateur($idParticipant, $idCampus, $search)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.siteOrganisateur', 'si')
            ->andWhere('s.organisateur  =  :id')
            ->andWhere('si.id =  :campus')
            ->andWhere('s.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('campus', $idCampus)
            ->setParameter('id', $idParticipant)
            ->getQuery()
            ->getResult();
    }

    public function findByIdParticipantInscrit($idParticipant, $idCampus, $search)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.participantsInscrits', 'p')
            ->innerJoin('s.siteOrganisateur', 'si')
            ->andWhere('p.id =  :id')
            ->andWhere('si.id =  :site')
            ->andWhere('s.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('site', $idCampus)
            ->setParameter('id', $idParticipant)
            ->getQuery()
            ->getResult();
    }

    public function findByIdParticipantNonInscrit($sorties, $idParticipant, $idCampus)
    {

        foreach ($sorties as $sortie) {
            foreach ($sortie->getParticipantsInscrits() as $participant) {
                if ($participant->getId() == $idParticipant || $idCampus != $sortie->getSiteOrganisateur()->getId()) {
                    $id = array_search($sortie, $sorties);
                    unset($sorties[$id]);
                }
            }
        }
        return $sorties;
    }

    public function findByEtatPassees($idCampus, $search)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->innerJoin('s.siteOrganisateur', 'si')
            ->andWhere('e.id =  5')
            ->andWhere('si.id =  :campus')
            ->andWhere('s.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('campus', $idCampus)
            ->getQuery()
            ->getResult();
    }*/
//    /**
//     * @return Sortie[] Returns an array of Sortie objects
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

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
