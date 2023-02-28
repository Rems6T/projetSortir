<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;

use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
            $this->addFlash('success', 'Vous avez bien créé une sortie !');
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

            }
            //Si bouton publier
            if ($sortieForm->getClickedButton() === $sortieForm->get('publier')) {
                //l'etat en "ouvert"
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
                //todo: Alerte message="sortie ouverte"
            }
            $this->addFlash('success', 'Vous avez bien modifié la sortie !');
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
            $this->addFlash('success', 'Vous avez bien annulé la sortie !');
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
    /**
     * @Route("/sortieLieu", name="app_sortie_getLieu" , methods={"GET"})
     */
    public function getLieu(Request $request,LieuRepository $lieuRepository){
        // Récupérer le lieu sélectionnée depuis la requête

        $lieu = $lieuRepository->find($request->get('lieu'));
        $data = array(
            array('Rue' => $lieu->getRue()),
            array('Ville'=>$lieu->getVille()->getNom()),
            array('Code Postal'=>$lieu->getVille()->getCodePostal()),
            array('latitude'=>$lieu->getLatitude()),
            array('Longitude'=>$lieu->getLongitude()),
        );


        // Retourner les lieux en tant que réponse JSON
        return new JsonResponse($data);
    }
}
