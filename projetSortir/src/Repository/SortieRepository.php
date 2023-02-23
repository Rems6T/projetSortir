<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findAllWithSitesAndEtats() {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.siteOrganisateur', 'si')
            ->addSelect('si')
            ->addSelect('e')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findBySearchAndCampus($search, $idCampus)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :search')
            ->innerJoin('s.siteOrganisateur', 'si')
            ->andWhere('si.id =  :nom')
            ->andWhere('s.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('campus', $idCampus)
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }


    public function findByCampus($idCampus)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.siteOrganisateur', 'si')
            ->andWhere('si.nom =  :campus')
            ->setParameter('campus', $idCampus)
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
    }
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
