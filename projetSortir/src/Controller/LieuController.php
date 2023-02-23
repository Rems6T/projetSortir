<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu/modifier/{id}", name="app_lieu_modifier")
     */
    public function modifier(Lieu $lieu, Request $request, EntityManagerInterface $entityManager): Response
    {

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        //recupere les donnée et les injecte dans lieuForm
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            $entityManager->persist($lieu);
            $entityManager->flush();

            //On redirige vers la page des ville
            return $this->redirectToRoute('app_villes');
        }
        return $this->render('lieu/modifier.html.twig',
            [ // les passe à Twig "
                'lieuForm' => $lieuForm->createView()]);
    }
}
