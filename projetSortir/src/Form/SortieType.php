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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;

class SortieType extends AbstractType
{
    private $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->security =$security;
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',
            TextType::class,
            ['label'=>'Nom de la sortie : '])
            ->add('dateHeureDebut',
            DateTimeType::class,
            ['label'=>'Date et heure de la sortie : ',
                'widget' => 'single_text',
                ])
            ->add('dateLimiteInscription',
            DateTimeType::class, [
                'label'=>'Date Limite d\'inscription : ',
                    'widget' => 'single_text',

                ])
            ->add('nbInscriptionsMax',
            NumberType::class,
                ['label'=>'Nombre d\'inscriptions max : '])
            ->add('duree',
                NumberType::class,
                ['label'=>'Durée : '])
            ->add('infosSortie',
            TextareaType::class,
                ['label'=>'Description et infos : '])
            ->add('siteOrganisateur',
            EntityType::class,
            ['class'=>Campus::class,
                'label'=>'Campus : ',
                'choice_label'=>'nom'
            ])
            ->add('ville', ChoiceType::class,[
                'choices'=>$this->getVille(),
                'label'=>'Ville :',
                'mapped' =>false,
                'constraints' => [

                    new NotEqualTo([
                        'value'=>"",
                        'message' => 'Choisissez une ville',
                    ])]
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $sortie = $event->getData();
            $form = $event->getForm();

            // checks if the Sortie object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "sortie"
            if (!$sortie || null === $sortie->getId()) {
                $form->add('lieu',
                    ChoiceType::class,
                    [
                        'label'=>'Lieu : ',
                        'choices'=>$this->getCampus(),
                        'required'=>true,
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Choisissez un lieu',
                            ]),
                            new NotEqualTo([
                                'value'=>"",
                                'message' => 'Choisissez un lieu',
                            ])]
                    ]);
            }else{
                $form->add('lieu',
                    EntityType::class,
                    ['class'=>Lieu::class,
                        'label'=>'Lieu',
                        'choice_label'=>'nom',
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Please enter a password',
                            ]),
                        new NotEqualTo([
                            'value'=>"",
                            'message' => 'Please choisissez une ville',
                        ])]

                    ]);
            }
        });


           $builder ->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer'])
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
        $choices[" "]="";
        foreach ($villes as $ville) {
            $choices[$ville->getNom()] = $ville->getId();
        }

        return $choices;
    }

    private function getCampus()
    {
        $choices = [];
        $choices[" "]="";
        return $choices;
    }
}
