<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
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
     * @Route("/search/{id}",name="app_search")
     */
    public function search(Request $request, SortieRepository $SortieRepository, CampusRepository $CampusRepository, $id): Response

    {
        $search = $request->request->get('search');
        $dateMin = $request->request->get('dateMin');
        $dateMax = $request->request->get('dateMax');
        $estOrganisateur = $request->request->get('est_organisateur');
        $estInscrit = $request->request->get('est_inscrit');
        $pasInscrit = $request->request->get('pas_inscrit');
        $SortiesPassees = $request->request->get('sortie_terminé');
        $idCurrentUser = $id;
        $idCampus = $request->request->get('campus');

        $sorties = [];

        if (
            $search != "" && $estOrganisateur == null && $estInscrit == null && $pasInscrit == null
            && $SortiesPassees == null && $dateMin == "" && $dateMax == ""
        ) {
            $sorties = array_merge($sorties, $SortieRepository->findBySearch($search));
        }

        if (
            $search != "" && $estOrganisateur == null && $estInscrit == null && $pasInscrit == null
            && $SortiesPassees == null && $dateMin == "" && $dateMax == ""
        ) {
            $sorties = array_merge($sorties, $SortieRepository->findByCampus($idCampus));
        }

        if ($dateMin != "" && $dateMax != "") {
            $sorties = array_merge($sorties, $SortieRepository->findByDates($dateMin, $dateMax, $idCampus, $search));
        }

        if ($estOrganisateur != null) {
            $sorties = array_merge($sorties, $SortieRepository->findByIdOrganisateur($idCurrentUser, $idCampus, $search));
        }

        if ($estInscrit != null) {
            $sorties = array_merge($sorties, $SortieRepository->findByIdParticipantInscrit($idCurrentUser, $idCampus, $search));
        }

        if ($pasInscrit != null) {
            $sorties = array_merge($sorties, $SortieRepository->findByIdParticipantNonInscrit($SortieRepository->findAll(), $idCurrentUser, $idCampus));
        }

        if ($SortiesPassees != null) {
            $sorties = array_merge($sorties, $SortieRepository->findByEtatPassees($idCampus, $search));
        }

        $sorties = array_unique($sorties, SORT_REGULAR);

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

        $sortie = $repo->find($idSortie);
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        $Participant = $repoParticipant->find($idParticipant);

        if ($sortie->getNbInscriptionsMax() > count($sortie->getParticipantsInscrits())) {
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
