<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
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
    public function index(SortieRepository $sortieRepo, CampusRepository $CampusRepository): Response
    {
        $sorties = $sortieRepo->findAllWithSitesAndEtats();
        $campusS = $CampusRepository->findAll();
        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
            'campusS' => $campusS,
        ]);
    }


    /**
     *@Route("/search/{id}",name="app_search")
     */
    public function search(Request $request, SortieRepository $SortieRepository, CampusRepository $CampusRepository, ParticipantRepository $participantRepository): Response

    {
//        $search = $request->request->get('search');
//        $dateMin = $request->request->get('dateMin');
//        $dateMax = $request->request->get('dateMax');
//        $estOrganisateur = $request->request->get('est_organisateur');
//        $estInscrit = $request->request->get('est_inscrit');
//        $pasInscrit = $request->request->get('pas_inscrit');
//        $SortiesPassees =  $request->request->get('sortie_terminé');
//        $idCurrentUser = $id;
//        $Campus = $CampusRepository->find($request->request->get('campus')) ;
        $data = [];
        $data['search'] = $request->get('search');
        $data['dateMin'] = $request->get('dateMin');
        $data['dateMax'] = $request->get('dateMax');
        $data['est_organisateur'] = $request->get('est_organisateur');
        $data['est_inscrit'] = $request->get('est_inscrit');
        $data['pas_inscrit'] = $request->get('pas_inscrit');
        $data['sortie_termine'] = $request->get('sortie_termine');
        $data['user'] = $participantRepository->find($request->request->get('user'));
        $data['campus'] = $CampusRepository->find($request->request->get('campus'));
        $sorties = $SortieRepository->findByRecherche($data);

//        if (
//            $search != "" && $estOrganisateur == null && $estInscrit == null && $pasInscrit == null
//            && $SortiesPassees == null && $dateMin == "" && $dateMax == ""
//        ) {
//            $sorties = array_merge($sorties, $SortieRepository->findBySearch($search));
//        }
//
//        if (
//            $Campus != null
//         ) {
//            $sorties = array_merge($sorties, $SortieRepository->findByCampus($Campus));
//         }
//
//        if ($dateMin != "" && $dateMax != "") {
//        $sorties = array_merge($sorties, $SortieRepository->findByDates($dateMin, $dateMax, $Campus, $search));
//        }
//
//        if ($estOrganisateur != null) {
//        $sorties = array_merge($sorties, $SortieRepository->findByIdOrganisateur($idCurrentUser, $Campus, $search));
//        }
//
//        if ($estInscrit != null) {
//         $sorties =  array_merge($sorties, $SortieRepository->findByIdParticipantInscrit($idCurrentUser, $Campus, $search));
//        }
//
//        if ($pasInscrit != null) {
//        $sorties = array_merge($sorties, $SortieRepository->findByIdParticipantNonInscrit($SortieRepository->findAll(), $idCurrentUser, $Campus));
//        }
//
//        if ($SortiesPassees != null) {
//        $sorties = array_merge($sorties, $SortieRepository->findByEtatPassees($Campus, $search));
//        }
//
//        $sorties = array_unique($sorties, SORT_REGULAR);

        $campusS = $CampusRepository->findAll();
        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
           'campusS' => $campusS,
        ]);
        }

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
