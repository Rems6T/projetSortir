<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="app_campus")
     */
    public function index(CampusRepository $campusRepository,EntityManagerInterface $entityManager, Request $request): Response
    {
        $campusS = $campusRepository->findAll();

        //methode pour la barre de recherche
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['search'])) {
            $keyWord = '%' . $request->get('search') . '%';
            $campusS = $campusRepository->findByWord($keyWord);
            return $this->render('campus/index.html.twig',
                [ // les passe à Twig "
                    "campusS" => $campusS,]);
        }
        //ajout campus
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);
        //recupere les donnée et les injecte dans campusForm
        $campusForm->handleRequest($request);
        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();

            //On redirige vers la page des campus
            return $this->redirectToRoute('app_campus');
        }


        return $this->render('campus/index.html.twig',
            [ // les passe à Twig "
                "campusS" => $campusS,
                'campusForm'=>$campusForm->createView()]);
    }

    /**
     * @Route("/campus/modifier/{id}", name="app_campus_modifier")
     */
    public function modifier(Campus $campus, EntityManagerInterface $entityManager, Request $request): Response
    {
        $campusForm = $this->createForm(CampusType::class, $campus);
        //recupere les donnée et les injecte dans campusForm
        $campusForm->handleRequest($request);
        if ($campusForm->isSubmitted() && $campusForm->isValid()) {

            $entityManager->persist($campus);
            $entityManager->flush();

            //On redirige vers la page des campus
            return $this->redirectToRoute('app_campus');
        }
        return $this->render('campus/modifier.html.twig',
            [ // les passe à Twig "
                'campusForm' => $campusForm->createView()]);
    }


    /**
     * @Route("/campus/supprimer/{id}", name="app_campus_supprimer")
     */
    public function supprimer(Campus $campus): Response
    {

        return $this->render('campus/supprimer.html.twig',
            [ // les passe à Twig "
                "campus" => $campus,]);
    }
    /**
     * @Route("/campus/suppression/{id}", name="app_campus_suppression")
     */
    public function suppression(Campus $campus, EntityManagerInterface $entityManager,CampusRepository $campusRepository): Response
    {

        //on verifie si le campus n'a bien plus aucun participant et de sortie
        if(empty($campus->getParticipants()) && empty($campus->getSiteOrganisateur())){
            //on supprime en bdd
            $entityManager->remove($campus);
            $entityManager->flush();
        }


        //On redirige vers la page de la  sortie
        return $this->redirectToRoute('app_campus');
    }

}