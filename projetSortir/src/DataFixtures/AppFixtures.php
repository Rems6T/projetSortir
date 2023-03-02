<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method setNom(string $string)
 * @method setRue(string $string)
 * @method setLongitude(float $param)
 * @method setLatitude(float $param)
 */
class AppFixtures extends Fixture
{

    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){

        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager): void
    {

        //CAMPUS
        $campus1 = new Campus();
        $campus1->setNom('Lorient');
        $manager->persist($campus1);

        $campus2 = new Campus();
        $campus2->setNom("Nantes");
        $manager->persist($campus2);

        $campus3 = new Campus();
        $campus3->setNom('Rennes');
        $manager->persist($campus3);

        $campus4 = new Campus();
        $campus4->setNom('Brest');
        $manager->persist($campus4);
        //Participants

        $participant1 = new Participant();
        $hash = $this->encoder->encodePassword($participant1, 'mdp');
        $participant1
            ->setPseudo("PieDup")
            ->setNom("Pierre")
            ->setPrenom("Dupont")
            ->setTelephone('0606060606')
            ->setMail("pierre.dupont@eni.fr")
            ->setPassword($hash)
            ->setActif(true)
            ->setRoles(['ROLE_USER'])
            ->setBrochureFilename("013-garcon.png")
            ->setCampus($campus2);
        $manager->persist($participant1);

        $participant2 = new Participant();
        $hash = $this->encoder->encodePassword($participant2, 'admin');

        $participant2
            ->setPseudo("Admin")
            ->setNom('Yves')
            ->setPrenom('Cousteau')
            ->setTelephone('0606060675')
            ->setMail("yves.cousteau@eni.fr")
            ->setPassword($hash)
            ->setActif(false)
            ->setRoles(['ROLE_ADMIN','ROLE_USER'])
            ->setBrochureFilename("010-homme.png")
            ->setCampus($campus1);
        $manager->persist($participant2);

        $participant3 = new Participant();
        $hash = $this->encoder->encodePassword($participant3, 'pierre.j');

        $participant3
            ->setPseudo("Le codeur fou")
            ->setNom('Jean')
            ->setPrenom('Pierre')
            ->setTelephone('0606060665')
            ->setMail('j.pierre@eni.fr')
            ->setPassword($hash)
            ->setActif(false)
            ->setRoles(['ROLE_USER'])
            ->setBrochureFilename("011-fille.png")
            ->setCampus($campus2);
        $manager->persist($participant3);

        $participant4 = new Participant();
        $hash = $this->encoder->encodePassword($participant4, 'mimi.pol');

        $participant4
            ->setPseudo("Le chanteur")
            ->setNom('Michel')
            ->setPrenom('Polnaref')
            ->setTelephone('0606060636')
            ->setMail('mich.polna@eni.fr')
            ->setPassword($hash)
            ->setActif(true)
            ->setRoles(['ROLE_ADMIN','ROLE_USER'])
            ->setBrochureFilename("017-serveuse.png")
            ->setCampus($campus3);
        $manager->persist($participant4);

        $participant5 = new Participant();
        $hash = $this->encoder->encodePassword($participant5, 'nord10');
        $participant5
            ->setPseudo("Le blagueur")
            ->setNom('Nordine')
            ->setPrenom('Dinenor')
            ->setTelephone('0606060610')
            ->setMail('nordine.dine@eni.fr')
            ->setPassword($hash)
            ->setActif(false)
            ->setRoles(['ROLE_USER'])
            ->setBrochureFilename("018-garcon-1.png")
            ->setCampus($campus3);
        $manager->persist($participant5);

        // ETAT
        $etat1 = new Etat();
        $etat1->setLibelle('Créée');
        $manager->persist($etat1);

        $etat2 = new Etat();
        $etat2->setLibelle('Ouverte');
        $manager->persist($etat2);

        $etat3 = new Etat();
        $etat3->setLibelle('Activité en cours');
        $manager->persist($etat3);

        $etat4 = new Etat();
        $etat4->setLibelle('Clôturée');
        $manager->persist($etat4);

        $etat5 = new Etat();
        $etat5->setLibelle('Passée');
        $manager->persist($etat5);

        $etat6 = new Etat();
        $etat6->setLibelle('Annulée');
        $manager->persist($etat6);

        $etat7 = new Etat();
        $etat7->setLibelle('Archivée');
        $manager->persist($etat7);

        //Ville
        $ville1 = new Ville();
        $ville1
            ->setNom("Rennes")
            ->setCodePostal('35131');
        $manager->persist($ville1);

        $ville2 = new Ville();
        $ville2
            ->setNom("Nantes")
            ->setCodePostal('44800');
        $manager->persist($ville2);

        $ville3 = new Ville();
        $ville3
            ->setNom("Lorient")
            ->setCodePostal('56100');
        $manager->persist($ville3);

        $ville4 = new Ville();
        $ville4
            ->setNom("Brest")
            ->setCodePostal('29200');
        $manager->persist($ville4);


        // LIEUX
        $lieu = new Lieu();
        $lieu
            ->setNom('Bastille')
            ->setRue('Pl. de la Bastille')
            ->setLongitude(2.3695)
            ->setLatitude(48.8533)
            ->setVille($ville1);

        $manager->persist($lieu);

        $lieu2 = new Lieu();
        $lieu2
            ->setNom('Ile de Nantes')
            ->setRue('Ile de Nantes')
            ->setLongitude(-1.553621)
            ->setLatitude(47.218371)
            ->setVille($ville2);
        $manager->persist($lieu2);

        //SORTIE
        $dateDebut =new \DateTime();
        $dateLimite = new \DateTime();
        $sortie = new Sortie();
        $sortie
            ->setNom('Bowling')
            ->setDateHeureDebut($dateDebut->setDate(2023,01,23))
            ->setDuree(120)
            ->setDateLimiteInscription($dateLimite->setDate(2023,01,18))
            ->setNbInscriptionsMax(7)
            ->setInfosSortie('Deux parties de Bowling')
            ->setEtat($etat7)
            ->setSiteOrganisateur($campus2)
            ->setOrganisateur($participant4)
            ->setLieu($lieu2)
            ->addParticipantsInscrits($participant1)
            ->addParticipantsInscrits($participant3);
        $manager->persist($sortie);

        $dateDebut =new \DateTime();
        $dateLimite = new \DateTime();
        $sortie2 = new Sortie();
        $sortie2
            ->setNom('Resto')
            ->setDateHeureDebut($dateDebut->setDate(2023,03,23))
            ->setDuree(120)
            ->setDateLimiteInscription($dateLimite->setDate(2023,03,02))
            ->setNbInscriptionsMax(10)
            ->setInfosSortie('Pizzéria')
            ->setEtat($etat2)
            ->setSiteOrganisateur($campus3)
            ->setOrganisateur($participant2)
            ->setLieu($lieu)
            ->addParticipantsInscrits($participant2)
            ->addParticipantsInscrits($participant5);
        $manager->persist($sortie2);

        $dateDebut =new \DateTime();
        $dateLimite = new \DateTime();
        $sortie3 = new Sortie();
        $sortie3
            ->setNom('Tricot')
            ->setDateHeureDebut($dateDebut->setDate(2023,04,15))
            ->setDuree(120)
            ->setDateLimiteInscription($dateLimite->setDate(2023,04,10))
            ->setNbInscriptionsMax(5)
            ->setInfosSortie('Cours de tricot')
            ->setEtat($etat2)
            ->setSiteOrganisateur($campus1)
            ->setOrganisateur($participant3)
            ->setLieu($lieu)
            ->addParticipantsInscrits($participant1)
            ->addParticipantsInscrits($participant2);
        $manager->persist($sortie3);

        $dateDebut =new \DateTime();
        $dateLimite = new \DateTime();
        $sortie4 = new Sortie();
        $sortie4
            ->setNom('Badminton')
            ->setDateHeureDebut($dateDebut->setDate(2023,05,05))
            ->setDuree(120)
            ->setDateLimiteInscription($dateLimite->setDate(2023,05,1))
            ->setNbInscriptionsMax(3)
            ->setInfosSortie('Match en double')
            ->setEtat($etat2)
            ->setSiteOrganisateur($campus4)
            ->setOrganisateur($participant1)
            ->setLieu($lieu)
            ->addParticipantsInscrits($participant5)
            ->addParticipantsInscrits($participant4);
        $manager->persist($sortie4);


        $manager->flush();
    }

}
