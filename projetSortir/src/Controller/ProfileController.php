<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Repository\ParticipantRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
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

            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $participant->setBrochureFilename($brochureFileName);
            }

            $participantRepository->add($participant, true);
            return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
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
    public function edit(Request                     $request,
                         Participant                 $participant,
                         ParticipantRepository       $participantRepository,
                         SluggerInterface            $slugger,
                         UserPasswordHasherInterface $userPasswordHarsher): Response
    {
        $form = $this->createForm(ProfileType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $brochureFile
             */
            $brochureFile = $form->get('brochure')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
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

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                if (file_exists('uploads/brochures/' . $participant->getBrochureFilename()))
                    unlink('uploads/brochures/' . $participant->getBrochureFilename());

                $participant->setBrochureFilename($newFilename);
            }

            $participant->setPassword(
                $userPasswordHarsher->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );
            $participantRepository->add($participant, true);

            return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
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
        $participantRepository->remove($participant, true);

        return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/managementActif/{id}", name="app_profile_actif")
     */
    public function actif(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($participant->isActif(true)) {
            $participantRepository->add($participant->setActif(false), true);
        } else {
            $participantRepository->add($participant->setActif(true), true);
        }
        return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
    }
}
