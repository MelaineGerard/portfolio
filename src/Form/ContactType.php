<?php

namespace App\Form;

use MeteoConcept\HCaptchaBundle\Form\HCaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{

    public function __construct(
        private readonly string $hcaptchaSiteKey
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom', 'required' => true])
            ->add('prenom', TextType::class, ['label' => 'Prénom', 'required' => true])
            ->add('email', TextType::class, ['label' => 'Email', 'required' => true])
            ->add('message', TextareaType::class, ['label' => 'Message', 'required' => true])
            ->add('captcha', HCaptchaType::class, [
                'label' => 'Veuillez prouver que vous êtes humain',
                'required' => true,
                'hcaptcha_site_key' => $this->hcaptchaSiteKey,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
