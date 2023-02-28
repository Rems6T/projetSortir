<?php

namespace App\Form;

use App\Entity\Campus;

use App\Entity\Lieu;
use App\Entity\Sortie;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',
            TextType::class,
            ['label'=>'Nom de la sortie'])
            ->add('dateHeureDebut',
            DateType::class,
            ['label'=>'Date et heure de la sortie',
                'widget' => 'single_text',
                ])
            ->add('dateLimiteInscription',
            DateType::class, [
                'label'=>'Date Limite d\'inscription',
                    'widget' => 'single_text',

                ])
            ->add('nbInscriptionsMax',
            NumberType::class,
                ['label'=>''])
            ->add('duree',
                NumberType::class,
                ['label'=>'Durée'])
            ->add('infosSortie',
            TextareaType::class,
                ['label'=>'Description et infos'])
            ->add('siteOrganisateur',
            EntityType::class,
            ['class'=>Campus::class,
                'label'=>'Campus',
                'choices'=>[],

            ])
            ->add('ville', ChoiceType::class,[
                'choices'=>$this->getVille(),
                'label'=>'Ville :',
                'mapped' =>false,
            ])
            ->add('lieu',
                EntityType::class,
            ['class'=>Lieu::class,
                'label'=>'Lieu',
                'choices'=>[]
            ])

            ->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publier', SubmitType::class, ['label' => 'Publier'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }

    private function getVille()
    {


        $villes = $this->entityManager->getRepository(Ville::class)->createQueryBuilder('c')

            ->getQuery()
            ->getResult();

        $choices = [];
        $choices[" "]=" ";
        foreach ($villes as $ville) {
            $choices[$ville->getNom()] = $ville->getId();
        }

        return $choices;
    }
}
