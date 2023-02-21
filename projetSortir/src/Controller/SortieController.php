<?php

namespace App\Controller;


use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/{id}", name="app_sortie")
     */
    public function afficher(Sortie $sortie): Response
    {

        //le lieu associé a la sortie
        $lieu = $sortie->getLieu();
        //le campus associé a la sortie
        $campus = $sortie->getSiteOrganisateur();
        //la ville associé au lieu
        $ville = $lieu->getVille();
        //la liste des participants associé a la sortie
        $participants = $sortie->getParticipantInscrit();
        return $this->render('sortie/afficher.html.twig', [
            'controller_name' => 'SortieController',
            'sortie'=>$sortie,
            'lieu'=>$lieu,
            'campus'=>$campus,
            'ville'=>$ville,
            'participants'=>$participants
        ]);
    }
}
