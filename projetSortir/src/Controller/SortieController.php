<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/creer", name="app_sortie_creer")
     */
    public function creer(Request $request, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        //recupere les donnée et les injecte dans sortieForm
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            //on injecte les données manquantes
            //l'organisateur
            $sortie->setOrganisateur($this->getUser());
            //Si bouton enregistrer
            if ($sortieForm->getClickedButton() === $sortieForm->get('enregistrer')) {

                //l'etat en "crée"
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Créée']));

                //todo: Alerte message="sortie crée"

            }
            //Si bouton publier
            if (($sortieForm->getClickedButton() === $sortieForm->get('publier')) && $sortieForm->isValid()) {

                //l'etat en "ouvert"
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
                //todo: Alerte message="sortie ouverte"
            }
            //On save en bdd
            $entityManager->persist($sortie);
            $entityManager->flush();

            //On redirige vers la page de la nouvelle sortie
            return $this->redirectToRoute('app_sortie', ['id' => $sortie->getId()]);
        }


        return $this->render('sortie/creer.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/modifier/{id}", name="app_sortie_modifier")
     */
    public function modifier(Sortie $sortie, Request $request, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        //recupere les donnée et les injecte dans sortieForm
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //Si bouton enregistrer
            if ($sortieForm->getClickedButton() === $sortieForm->get('enregistrer')) {

                //l'etat en "crée"
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Créée']));
                //todo: Alerte message="sortie crée"
            }
            //Si bouton publier
            if ($sortieForm->getClickedButton() === $sortieForm->get('publier')) {
                //l'etat en "ouvert"
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
                //todo: Alerte message="sortie ouverte"
            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            //On redirige vers la page de la  sortie
            return $this->redirectToRoute('app_sortie', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/modifier.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/sortie/annuler/{id}", name="app_sortie_annuler")
     */
    public function annuler(Sortie $sortie, Request $request, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        //on check si le formulaire est rempli
        if ($request->get('motif')) {
            //on change l'etat en Annulée
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));
            //on change la description par le motif
            $sortie->setInfosSortie($request->get('motif'));
            //on save en bdd
            $entityManager->persist($sortie);
            $entityManager->flush();

            //On redirige vers la page de la  sortie
            return $this->redirectToRoute('app_sortie', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie
        ]);
    }


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
        $participants = $sortie->getParticipantsInscrits();

        return $this->render('sortie/afficher.html.twig', [
            'controller_name' => 'SortieController',
            'sortie' => $sortie,
            'lieu' => $lieu,
            'campus' => $campus,
            'ville' => $ville,
            'participants' => $participants,
        ]);
    }
}
