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
        // $product = new Product();
        // $manager->persist($product);

         // ETAT
        $etat1 = new Etat();
        $etat1 ->setLibelle('Créée');
        $manager->persist($etat1);
        $manager->flush();
        $etat2 = new Etat();
        $etat2 ->setLibelle('Ouverte');
        $manager->persist($etat2);
        $manager->flush();
        $etat3 = new Etat();
        $etat3 ->setLibelle('Activité en cours');
        $manager->persist($etat3);
        $manager->flush();
        $etat4 = new Etat();
        $etat4 ->setLibelle('Clôturée');
        $manager->persist($etat4);
        $manager->flush();
        $etat5 = new Etat();
        $etat5 ->setLibelle('passée');
        $manager->persist($etat5);
        $manager->flush();
        $etat6 = new Etat();
        $etat6 ->setLibelle('Annulée');
        $manager->persist($etat6);
        $manager->flush();

        //CAMPUS
        $campus1 = new Campus();
        $campus1->setNom('Brest');
        $manager->persist($campus1);
        $manager->flush();

        $campus2 = new Campus();
        $campus2->setNom("Nantes");
        $manager->persist($campus2);
        $manager->flush();

        $campus3 = new Campus();
        $campus3 ->setNom('Rennes');
        $manager->persist($campus3);
        $manager->flush();

        //SORTIE
        $sortie = new Sortie();
        $sortie ->setNom('Bowling');
        $sortie->setDateHeureDebut( new \DateTime('d m Y h:i:s'));
        $sortie ->setDuree(120);
        $sortie -> setDateLimiteInscription($sortie->getDateHeureDebut());
        $sortie -> setNbInscriptionsMax(10);
        $sortie -> setInfosSortie('Deux parties de Bowling');
        $sortie -> setEtat($etat1);
        $manager->persist($sortie);
        $manager->flush();

        //Participants
        $participant1 = new Participant();
        $participant1->setNom("Pierre");
        $participant1->setPrenom("Dupont");
        $participant1->setTelephone(0606060606);
        $participant1->setMail("pierre.dupont@eni.fr");
        $participant1->setPassword("p.pond");
        $participant1->setAdministrateur(false);
        $participant1->setActif(true);
        $manager->persist($participant1);
        $participant2 = new Participant();
        $participant2->setNom('Yves');
        $participant2->setPrenom('Cousteau');
        $participant2->setTelephone(0606060675);
        $participant2->setMail("yves.cousteau@eni.fr");
        $participant2->setPassword('y.cous75');
        $participant2->setAdministrateur(true);
        $participant2->setActif(false);
        $manager->persist($participant2);
        $participant3 = new Participant();
        $participant3->setNom('Jean');
        $participant3->setPrenom('Pierre');
        $participant3->setTelephone('0606060665');
        $participant3->setMail('j.pierre@eni.fr');
        $participant3->setPassword('pierre.j');
        $participant3->setAdministrateur(false);
        $participant3->setActif(false);
        $manager->persist($participant3);
        $participant4 = new Participant();
        $participant4->setNom('Michel');
        $participant4->setPrenom('Polnaref');
        $participant4->setTelephone('0606060636');
        $participant4->setMail('mich.polna@eni.fr');
        $participant4->setPassword('mimi.pol');
        $participant4->setAdministrateur(true);
        $participant4->setActif(true);
        $manager->persist($participant4);
        $participant5 = new Participant();
        $participant5->setNom('Nordine');
        $participant5->setPrenom('Dinenor');
        $participant5->setTelephone(0606060610);
        $participant5->setMail('nordine.dine@eni.fr');
        $participant5->setPassword('nord10');
        $participant5->setAdministrateur(false);
        $participant5->setActif(false);
        $manager->persist($participant5);


        //Ville
        $ville1 = new Ville();
        $ville1->setNom("Rennes");
        $ville1->setCodePostal(35131);
        $manager->persist($ville1);

        $ville3 = new Ville();
        $ville3->setNom("Nantes");
        $ville3->setCodePostal(44800);
        $manager->persist($ville3);

       $manager->flush();

       // LIEUX
        $lieu = new Lieu();
        $lieu = $this->setNom('Bastille');
        $lieu = $this->setRue('Pl. de la Bastille');
        $lieu = $this->setLongitude(2.3695);
        $lieu = $this->setLatitude(48.8533);
        $manager->persist($lieu);
        $manager->flush();

        $lieu2 = new Lieu();
        $lieu2 = $this->setNom('Ile de Nantes');
        $lieu2 = $this->setRue('Ile de Nantes');
        $lieu2 = $this->setLongitude(-1.553621);
        $lieu2 = $this->setLatitude(47.218371);
        $manager->persist($lieu2);
        $manager->flush();
    }


}
