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

/**
 * @method setNom(string $string)
 * @method setRue(string $string)
 * @method setLongitude(float $param)
 * @method setLatitude(float $param)
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        //CAMPUS
        $campus1 = new Campus();
        $campus1->setNom('Brest');
        $manager->persist($campus1);

        $campus2 = new Campus();
        $campus2->setNom("Nantes");
        $manager->persist($campus2);

        $campus3 = new Campus();
        $campus3->setNom('Rennes');
        $manager->persist($campus3);


        //Participants
        $participant1 = new Participant();
        $participant1
            ->setPseudo("PieDup")
            ->setNom("Pierre")
            ->setPrenom("Dupont")
            ->setTelephone(0606060606)
            ->setMail("pierre.dupont@eni.fr")
            ->setPassword("p.pond")
            ->setAdministrateur(false)
            ->setActif(true)
            ->setRoles(["Nourriture"])
            ->setCampus($campus2);
        $manager->persist($participant1);

        $participant2 = new Participant();
        $participant2
            ->setPseudo("Cousyves")
            ->setNom('Yves')
            ->setPrenom('Cousteau')
            ->setTelephone(0606060675)
            ->setMail("yves.cousteau@eni.fr")
            ->setPassword('y.cous75')
            ->setAdministrateur(true)
            ->setActif(false)
            ->setRoles(["Nourriture"])
            ->setCampus($campus1);
        $manager->persist($participant2);

        $participant3 = new Participant();
        $participant3
            ->setPseudo("Le codeur fou")
            ->setNom('Jean')
            ->setPrenom('Pierre')
            ->setTelephone('0606060665')
            ->setMail('j.pierre@eni.fr')
            ->setPassword('pierre.j')
            ->setAdministrateur(false)
            ->setActif(false)
            ->setRoles(["Billets", "Guide"])
            ->setCampus($campus2);
        $manager->persist($participant3);

        $participant4 = new Participant();
        $participant4
            ->setPseudo("Le chanteur")
            ->setNom('Michel')
            ->setPrenom('Polnaref')
            ->setTelephone('0606060636')
            ->setMail('mich.polna@eni.fr')
            ->setPassword('mimi.pol')
            ->setAdministrateur(true)
            ->setActif(true)
            ->setRoles(["Billets"])
            ->setCampus($campus3);
        $manager->persist($participant4);

        $participant5 = new Participant();
        $participant5
            ->setPseudo("Le blagueur")
            ->setNom('Nordine')
            ->setPrenom('Dinenor')
            ->setTelephone(0606060610)
            ->setMail('nordine.dine@eni.fr')
            ->setPassword('nord10')
            ->setAdministrateur(false)
            ->setActif(false)
            ->setRoles(["Nourriture"])
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


        //Ville
        $ville1 = new Ville();
        $ville1
            ->setNom("Rennes")
            ->setCodePostal(35131);
        $manager->persist($ville1);

        $ville2 = new Ville();
        $ville2
            ->setNom("Nantes")
            ->setCodePostal(44800);
        $manager->persist($ville2);


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
        $dateLimite = new \DateTime();
        $sortie = new Sortie();
        $sortie
            ->setNom('Bowling')
            ->setDateHeureDebut(new \DateTime())
            ->setDuree(120)
            ->setDateLimiteInscription($dateLimite->setDate(2023,05,23))
            ->setNbInscriptionsMax(10)
            ->setInfosSortie('Deux parties de Bowling')
            ->setEtat($etat1)
            ->setSiteOrganisateur($campus2)
            ->setOrganisateur($participant4)
            ->setLieu($lieu2);
        $manager->persist($sortie);


        $manager->flush();
    }
}
