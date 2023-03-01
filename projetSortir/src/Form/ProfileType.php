<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('password')
            ->add('telephone')
            ->add('mail',
                EmailType::class,
                ['label' => 'Email'])
            ->add('campus',
                EntityType::class,
                ['class' => Campus::class,
                    'label' => 'Campus',
                    'choice_label' => 'nom'
                ])
            ->add('brochure', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/png',
                            'application/jpeg',
                            'application/jpg'
                        ],
                        'mimeTypesMessage' => 'Merci de mettre une image.'
                    ])
                ]
            ]);

//        $builder->add('inscriptionFile', FileType::class, [
//            'label' => 'Fichier csv pour l\'inscription de plusieurs utilisateurs.',
//            'mapped' => false,
//            'required' => false,
//            'constraints' => [
//                new File([
//                    'maxSize' => '8k',
//                    'mimeTypes' => [
//                        'application/csv'
//                    ],
//                    'mimeTypesMessage' => 'Merci de mettre un fichier ".csv".'
//                ])
//            ]
//        ])
//        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
