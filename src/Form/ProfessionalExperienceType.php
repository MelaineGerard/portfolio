<?php

namespace App\Form;

use App\Entity\ProfessionalExperience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessionalExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'required' => true
            ])
            // Liste déroulante
            ->add('jobType', ChoiceType::class, [
                'label' => 'Type de poste',
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Stage' => 'Stage',
                    'Alternance' => 'Alternance',
                    'Freelance' => 'Freelance',
                    'Intérim' => 'Intérim',
                ],
                'required' => true
            ])
            ->add('description', TextType::class, [
                'label' => 'Description du poste',
                'required' => true
            ])
            ->add('startDate', TextType::class, [
                'label' => 'Date de début',
                'required' => true
            ])
            ->add('endDate', TextType::class, [
                'label' => 'Date de fin',
                'required' => true
            ])
            ->add('companyWebsite', UrlType::class, [
                'label' => 'Site web de l\'entreprise',
                'required' => true
            ])
            ->add('button', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfessionalExperience::class,
        ]);
    }
}
