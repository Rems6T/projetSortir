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
     * @return Response
     */
    public function index(SortieRepository $SortieRepository, CampusRepository $campusRepository,Request $request): Response
    {
       $campusS = $campusRepository->findAll();
         $sorties = $SortieRepository->findAll();
        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['search'])){
            $data = $request;

        }

        return $this->render('main/index.html.twig',
        [ // les passe à Twig "
            "sorties" => $sorties,
            'campusS' => $campusS
            ]);
    }}


