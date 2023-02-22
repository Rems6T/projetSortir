<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_main_index")
     * @return Response
     */
    public function index(SortieRepository $SortieRepository): Response
    {
    $sorties = $SortieRepository->findAll();


        return $this->render('main/index.html.twig',
        [ // les passe à Twig "
            "sorties" => $sorties, ]); }}


