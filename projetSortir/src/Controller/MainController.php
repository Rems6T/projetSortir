<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\FiltreType;
use App\Model\Filtre;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

       /**
        * @Route("/", name="app_main_index")
        */
    public function index(SortieRepository $sortieRepo, CampusRepository $CampusRepository, EtatRepository $etatRepository, Request $request): Response
    {
        $sorties = $sortieRepo->findAll();
        $campusS = $CampusRepository->findAll();

        $aujourdhui = new \DateTime('now');

        foreach ($sorties as $sortie) {
            $date = $sortie->getDateHeureDebut();

            // $dateCopie = new \DateTime();
            $date2 = &$date;
            $duree = $sortie->getDuree();

            if ($duree == null) {
                $duree = 1;
            }

            $interval = new \DateInterval('PT' . $duree . 'M');
            $finInscription = $sortie->getDateLimiteInscription();
            $one_month = new DateInterval('P1M');

            $dateArchive = $date->add($one_month);

            if ($finInscription < $aujourdhui) { //Inscription finie

                if ($date < $aujourdhui) {
                    $dateFin = $date2->add($interval);
                    if ($dateFin > $aujourdhui) {
                        $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Activité en cours"])); // ENCOURS
                        $date2->sub($interval);
                    } else {
                        $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Passée"])); //PASSEE
                        $date2->sub($interval);
                    }
                    if ($dateArchive<$aujourdhui){
                        $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Archivée"])); //ARCHIVEE
                    }
                } else {
                    $sortie->setEtat($etatRepository->findOneBy(['libelle' => "Clôturée"])); //CLOTUREE
                }
            }
        }

        $filtre = new Filtre();
        $filtreForm = $this->createForm(FiltreType::class, $filtre);
        if ($filtreForm->isSubmitted() && $filtreForm->isValid()) {
            $sorties=$sortieRepo->findByRecherche($filtre);
            return $this->render('main/index.html.twig', [
                'sorties' => $sorties,
                'campusS' => $campusS,
                'filtreForm' => $filtreForm->createView(),
            ]);
        }

        $sorties = $sortieRepo->findAll();
        $campusS = $CampusRepository->findAll();
        return $this->render('main/index.html.twig', [
         'sorties' => $sorties,
         'campusS' => $campusS,
         'filtreForm' => $filtreForm->createView(),
        ]);

    }



//    /**
//     *@Route("/search/{id}",name="app_search")
//     */
//    public function search(Request $request, SortieRepository $SortieRepository, CampusRepository $CampusRepository, ParticipantRepository $participantRepository): Response
//
//    {
//
//        $data = [];
//        $data['search'] = $request->get('search');
//        $data['dateMin'] = $request->get('dateMin');
//        $data['dateMax'] = $request->get('dateMax');
//        $data['est_organisateur'] = $request->get('est_organisateur');
//        $data['est_inscrit'] = $request->get('est_inscrit');
//        $data['pas_inscrit'] = $request->get('pas_inscrit');
//        $data['sortie_termine'] = $request->get('sortie_termine');
//        $data['user'] = $participantRepository->find($request->request->get('user'));
//        $data['campus'] = $CampusRepository->find($request->request->get('campus'));
//        $sorties = $SortieRepository->findByRecherche($data);
//        $filtre = new Filtre();
//        $filtreForm = $this->createForm(FiltreType::class, $filtre);
//
//        $campusS = $CampusRepository->findAll();
//        return $this->render('main/index.html.twig', [
//            'sorties' => $sorties,
//           'campusS' => $campusS,
//
//        ]);
//        }

    /**
     * @Route("/sortie/inscription/{idParticipant}/{idSortie}",name="app_sortie_inscription")
     */
    public function inscriptionSortie(SortieRepository $repo, EntityManagerInterface $em, $idParticipant, $idSortie): Response
    {
        $today = new \DateTime('now');
        $sortie = $repo->find($idSortie);
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        $Participant = $repoParticipant->find($idParticipant);

        if($today >= $sortie->getDateLimiteInscription()) {
            $this->addFlash('error', "Impossible de s'inscrire, la date d'inscription est dépassée");
            return $this->redirectToRoute("app_main_index");
        } elseif ( $sortie->getNbInscriptionsMax() > count($sortie->getParticipantsInscrits())) {
            $sortie->addParticipantsInscrits($Participant);
            $em->flush();
            $this->addFlash('success', 'Vous avez était inscrit à la sortie !');
        } else {
            $this->addFlash('danger', 'La sortie est complète');
        }


        return $this->redirectToRoute("app_main_index");

    }

    /**
     * @Route("/sortie/desinscription/{idParticipant}/{idSortie}",name="app_sortie_desinscription")
     */

    public function desinscriptionSortie(SortieRepository $repo, EntityManagerInterface $em, $idParticipant, $idSortie): Response
    {

        $sortie = $repo->find($idSortie);
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        $Participant = $repoParticipant->find($idParticipant);
        $sortie->removeParticipantsInscrits($Participant);
        $em->flush();
        $this->addFlash('success', 'Vous êtes désinscrit de la sortie !');
        return $this->redirectToRoute("app_main_index");
    }




}
