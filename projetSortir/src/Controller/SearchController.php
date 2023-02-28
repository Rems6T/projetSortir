<?php

namespace App\Controller;

use App\Form\FiltreType;
use App\Model\Filtre;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function indexSortieRepository (SortieRepository $sortieRepo, CampusRepository $CampusRepository, Request $request): Response
    {
        $filtre = new Filtre();
        $filtreForm = $this->createForm(FiltreType::class, $filtre);
        //recupere les donnée et les injecte dans sortieForm
        $filtreForm->handleRequest($request);
        if ($filtreForm->isSubmitted() && $filtreForm->isValid()) {
            dd($filtre);
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
}
