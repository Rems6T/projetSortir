<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\FiltreType;
use App\Model\Filtre;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MainController extends AbstractController
{

    /**
     * @Route("/", name="app_main_index")
     */
    public function index(SortieRepository $sortieRepo, EtatRepository $etatRepository,EntityManagerInterface $entityManager, Request $request): Response
    {

        $user = $this->getUser();

//Methode pour changer les etats des sorties


//Recupere toutes les sorties sauf les archiver
        $sorties = $sortieRepo->findAllExceptArchive();

        $aujourdhui = new \DateTime('now');
          //On charge tous les etats d'avance
        $enCours = $etatRepository->findOneBy(['libelle' => "Activité en cours"]);
        $passee = $etatRepository->findOneBy(['libelle' => "Passée"]);
        $archivee = $etatRepository->findOneBy(['libelle' => "Archivée"]);
        $cloturee = $etatRepository->findOneBy(['libelle' => "Clôturée"]);


        foreach ($sorties as $sortie) {

            //On recupere l'heure de la sortie
            $date = $sortie->getDateHeureDebut();


            $date2 = &$date;

            //on ajoute la duree
            $duree = $sortie->getDuree();
            if ($duree == null) {
                $duree = 1;
            }
            $interval = new \DateInterval('PT' . $duree . 'M');

            $finInscription = $sortie->getDateLimiteInscription();

            //on ajoute un mois pour avoir la date d'archivage
            $one_month = new DateInterval('P1M');
            $dateArchive = $date->add($one_month);

            if ($finInscription < $aujourdhui) { //Inscription finie

                if ($date < $aujourdhui) {
                    $dateFin = $date2->add($interval);
                    if ($dateFin > $aujourdhui && $sortie->getEtat()->getLibelle() != 'Activité en cours') {
                        $sortie->setEtat($enCours); // ENCOURS
                        $date2->sub($interval);
                    }
                    if ($dateFin < $aujourdhui && $sortie->getEtat()->getLibelle() != 'Passée') {
                        $sortie->setEtat($passee); //PASSEE
                        $date2->sub($interval);
                    }
                    if ($dateArchive < $aujourdhui && $sortie->getEtat()->getLibelle() != 'Archivée') {
                        $sortie->setEtat($archivee); //ARCHIVEE
                    }
                }
                if ($date > $aujourdhui && $sortie->getEtat()->getLibelle() != 'Clôturée') {
                    $sortie->setEtat($cloturee); //CLOTUREE
                }
            }
            //on save la modif en bdd
            $entityManager->persist($sortie);
            $entityManager->flush();
        }
//Fin Methode


//Creation du formulaire pour la barre de recherche
        $filtre = new Filtre();
        $filtreForm = $this->createForm(FiltreType::class, $filtre);



//Soumission du formulaire
        $filtreForm->handleRequest($request);
        if ($filtreForm->isSubmitted() && $filtreForm->isValid()) {
            $sorties = $sortieRepo->findByRecherche($filtre, $user);
            return $this->render('main/index.html.twig', [
                'sorties' => $sorties,

                'filtreForm' => $filtreForm->createView(),
            ]);
        }



        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,

            'filtreForm' => $filtreForm->createView(),
        ]);

    }


    /**
     * @Route("/sortie/inscription/{idParticipant}/{idSortie}",name="app_sortie_inscription")
     */
    public function inscriptionSortie(SortieRepository $repo, EntityManagerInterface $em, $idParticipant, $idSortie): Response
    {
        $today = new \DateTime('now');
        $sortie = $repo->find($idSortie);
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        $Participant = $repoParticipant->find($idParticipant);

        if ($today >= $sortie->getDateLimiteInscription()) {
            $this->addFlash('error', "Impossible de s'inscrire, la date d'inscription est dépassée");
            return $this->redirectToRoute("app_main_index");
        } elseif ($sortie->getNbInscriptionsMax() > count($sortie->getParticipantsInscrits())) {
            $sortie->addParticipantsInscrits($Participant);
            $em->flush();
            $this->addFlash('success', 'Vous avez était inscrit à la sortie !');
        } else {
            $this->addFlash('danger', 'La sortie est complète');
        }


        return $this->redirectToRoute("app_main_index");

    }

    /**
     * @Route("/sortie/desinscription/{idParticipant}/{idSortie}",name="app_sortie_desinscription")
     */

    public function desinscriptionSortie(SortieRepository $repo, EntityManagerInterface $em, $idParticipant, $idSortie): Response
    {

        $sortie = $repo->find($idSortie);
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        $Participant = $repoParticipant->find($idParticipant);
        $sortie->removeParticipantsInscrits($Participant);
        $em->flush();
        $this->addFlash('success', 'Vous êtes désinscrit de la sortie !');
        return $this->redirectToRoute("app_main_index");
    }


}
