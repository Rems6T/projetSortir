<?php

namespace App\Form;

use App\Entity\Campus;
use App\Model\Filtre;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FiltreType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'class'=> Campus::class,
                'choice_label'=>'nom',
                'required' => false,
            ])
            ->add('search', SearchType::class,[
                'required' => false,

            ])
            ->add('dateDebut', DateTimeType::class,[
                'required' => false,
                'widget' => 'single_text',

            ])
            ->add('dateLimite', DateTimeType::class,[
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('estOrganisateur', CheckboxType::class,[
                'label'    => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
            ])
            ->add('estInscrit', CheckboxType::class,[
                'label'    => 'Sorties auxquelles je suis inscrite',
                'required' => false,
            ])
            ->add('pasInscrit', CheckboxType::class,[
                'label'    => 'Sorties auxquelles je ne suis pas inscrite',
                'required' => false,
            ])
            ->add('estPassees', CheckboxType::class,[
                'label'    => 'Sorties passées',
                'required' => false,
            ])
            ->add('rechercher', SubmitType::class, ['label' => 'Rechercher']);

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filtre::class,
        ]);
    }

}