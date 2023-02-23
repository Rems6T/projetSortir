<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CampusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',
                TextType::class,
                ['label' => 'Nom du campus',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez un nom de campus',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Le campus doit faire au moins {{ limit }} lettres',

                            'max' => 50,
                        ])
                    ]
                    ]
            )
            ->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campus::class,
        ]);
    }
}
