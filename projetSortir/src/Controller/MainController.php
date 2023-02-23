<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
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
    public function search(Request $request, SortieRepository $SortieRepository, CampusRepository $CampusRepository, $id): Response

    {
        $search = $request->request->get('search');
        $dateDebut = $request->request->get('dateMin');
        $dateLimiteInscription = $request->request->get('dateMax');
        $estOrganisateur = $request->request->get('est_organisateur');
        $estInscrit = $request->request->get('est_inscrit');
        $pasInscrit = $request->request->get('pas_inscrit');
        $SortiesPassees =  $request->request->get('sortie_terminé');
        $idCurrentUser = $id;
        $idCampus = $request->request->get('campus');

        $sorties = [];

        if (
            $search != "" && $estOrganisateur == null && $estInscrit == null && $pasInscrit == null
            && $SortiesPassees == null && $dateDebut == "" && $dateLimiteInscription == ""
        ) {
            $sorties = array_merge($sorties, $SortieRepository->findBySearchAndCampus($search, $idCampus));
        }

        if (
            $search != "" && $estOrganisateur == null && $estInscrit == null && $pasInscrit == null
            && $SortiesPassees == null && $dateDebut == "" && $dateLimiteInscription == ""
         ) {
            $sorties = array_merge($sorties, $SortieRepository->findByCampus($idCampus));
         }

        if ($dateDebut != "" && $dateLimiteInscription != "") {
        $sorties = array_merge($sorties, $SortieRepository->findByDates($dateDebut, $dateLimiteInscription, $idCampus, $search));
        }

        if ($estOrganisateur != null) {
        $sorties = array_merge($sorties, $SortieRepository->findByIdOrganisateur($idCurrentUser, $idCampus, $search));
        }

        if ($estInscrit != null) {
         $sorties =  array_merge($sorties, $SortieRepository->findByIdParticipantInscrit($idCurrentUser, $idCampus, $search));
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
        } }
