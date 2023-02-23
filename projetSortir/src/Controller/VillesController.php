<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VillesController extends AbstractController
{
    /**
     * @Route("/villes", name="app_villes")
     */
    public function index(VilleRepository $villeRepository,EntityManagerInterface $entityManager, Request $request): Response
    {
        $villes = $villeRepository->findAll();

        //methode pour la barre de recherche
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['search'])) {
            $keyWord = '%' . $request->get('search') . '%';
            $villes = $villeRepository->findByWord($keyWord);
            return $this->render('villes/index.html.twig',
                [ // les passe à Twig "
                    "villes" => $villes,]);
        }

        //ajout campus
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        //recupere les données et les injecte dans villeForm
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            //On redirige vers la page des campus
            return $this->redirectToRoute('app_villes');
        }

        return $this->render('villes/index.html.twig', [
            "villes" => $villes,
            'villeForm'=>$villeForm->createView()
        ]);
    }
    /**
     * @Route("/villes/modifier/{id}", name="app_villes_modifier")
     */
    public function modifier(Ville $ville, EntityManagerInterface $entityManager, Request $request): Response
    {
        $villeForm = $this->createForm(VilleType::class, $ville);
        //recupere les donnée et les injecte dans campusForm
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {

            $entityManager->persist($ville);
            $entityManager->flush();

            //On redirige vers la page des campus
            return $this->redirectToRoute('app_villes');
        }
        return $this->render('villes/modifier.html.twig',
            [ // les passe à Twig "
                'villeForm' => $villeForm->createView()]);
    }
    /**
     * @Route("/villes/supprimer/{id}", name="app_villes_supprimer")
     */
    public function supprimer(Ville $ville): Response
    {
        


        return $this->render('villes/supprimer.html.twig',
            [ // les passe à Twig "
                "ville" => $ville,]);
    }
    /**
     * @Route("/villes/suppression/{id}", name="app_villes_suppression")
     */
    public function suppression(Ville $ville, EntityManagerInterface $entityManager,LieuRepository $lieuRepository): Response
    {
        $lieu =$lieuRepository->findBy(["ville"=>$ville]);


        //on verifie si la ville n'a bien plus aucun lieu
        if(empty($lieu)){
            //on supprime en bdd
            $entityManager->remove($ville);
            $entityManager->flush();
        }


        //On redirige vers la page de la  sortie
        return $this->redirectToRoute('app_villes');
    }

}
