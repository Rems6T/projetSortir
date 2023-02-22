<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="app_campus")
     */
    public function index(CampusRepository $campusRepository, Request $request): Response
    {
        $campusS = $campusRepository->findAll();


        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['search'])){
            $keyWord = '%'.$request->get('search').'%';
            $campusS = $campusRepository->findByWord($keyWord);
            return $this->render('campus/index.html.twig',
                [ // les passe à Twig "
                    "campusS" => $campusS, ]);
        }


        return $this->render('campus/index.html.twig',
            [ // les passe à Twig "
                "campusS" => $campusS, ]);
    }

/**
 * @Route("/campus/modifier/{id}", name="app_campus_modifier")
 */
public function modifier(Campus $campus, CampusRepository $campusRepository, Request $request): Response
{
    return $this->render('campus/modifier.html.twig',
        [ // les passe à Twig "
            "campus" => $campus, ]);
}
}