<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="app_campus")
     */
    public function index(CampusRepository $campusRepository): Response
    {
        $campusS = $campusRepository->findAll();

        return $this->render('campus/index.html.twig',
            [ // les passe à Twig "
                "campusS" => $campusS, ]); }
}
