<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VillesController extends AbstractController
{
    /**
     * @Route("/villes", name="app_villes")
     */
    public function index(VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();
        return $this->render('villes/index.html.twig', [
            "villes" => $villes,
        ]);
    }
}
