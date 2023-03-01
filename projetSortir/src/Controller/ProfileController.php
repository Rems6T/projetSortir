<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Repository\ParticipantRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="app_profile_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_profile_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParticipantRepository $participantRepository, FileUploader $fileUploader): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ProfileType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inscriptionFile = $form->get('inscriptionFile')->getData();

            if ($inscriptionFile) {
                $encoders = array(new JsonEncoder());
                $normalizers = array(new ObjectNormalizer());

                $serializer = new Serializer($normalizers, $encoders);

                $participantSerialized = $serializer->deserialize($inscriptionFile, Participant::class, 'json');

                foreach ($participantSerialized as $inscription) {
//                    Pseudo;Role;Password;Nom;Prenom;Telephone;Mail;Actif;Campus_id;brochure_filename
                    $participant = new Participant();
                    $participant
                        ->setPseudo($participantSerialized[0])
                        ->setRoles($participantSerialized[1])
                        ->setPassword($participantSerialized[2])
                        ->setNom($participantSerialized[3])
                        ->setPrenom($participantSerialized[4])
                        ->setTelephone($participantSerialized[5])
                        ->setMail($participantSerialized[6])
                        ->setActif($participantSerialized[7])
                        ->setCampus($participantSerialized[8])
                        ->setBrochureFilename($participantSerialized[9]);

                    $participantRepository->add($participant, true);
                }
                return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);

            } else {
                $brochureFile = $form->get('brochure')->getData();
                if ($brochureFile) {
                    $brochureFileName = $fileUploader->upload($brochureFile);
                    $participant->setBrochureFilename($brochureFileName);
                }

                $participantRepository->add($participant, true);
                return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('profile/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_profile_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('profile/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_profile_edit", methods={"GET", "POST"})
     */
    public function edit(Request               $request,
                         Participant           $participant,
                         ParticipantRepository $participantRepository,
                         SluggerInterface      $slugger
        , UserPasswordHasherInterface          $userPasswordHarsher): Response
    {
        $form = $this->createForm(ProfileType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $brochureFile
             */
            $brochureFile = $form->get('brochure')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                $brochureFile->move(
                    $this->getParameter('brochures_directory'),
                    $newFilename
                );

                // Delete old photo if it exists
                if (file_exists('uploads/brochures/' . $participant->getBrochureFilename()))
                    unlink('uploads/brochures/' . $participant->getBrochureFilename());

                // updates the 'brochureFilename' property to store the file name

                // instead of its contents
                $participant->setBrochureFilename($newFilename);
            }

            $participant->setPassword(
                $userPasswordHarsher->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );
            $participantRepository->add($participant, true);

            return $this->redirectToRoute('app_profile_index', ['id' => $participant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/management/{id}", name="app_profile_delete")
     */
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
    }

}
