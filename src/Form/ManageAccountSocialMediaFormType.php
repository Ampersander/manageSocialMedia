<?php

namespace App\Form;

use App\Entity\SocialMediaAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManageAccountSocialMediaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('social_media', ChoiceType::class, [
            //'mapped' => false,
            'choices'  => [
                'Twitter' => 'twitter',
                'Facebook compte' => 'facebook_account',
            ],
        
        ])
        ->add('name', TextType::class, [
            //'mapped' => false,
                
        ])
        ->add('apiKey', TextType::class, [
           // 'mapped' => false
                
        ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialMediaAccount::class,
        ]);
    }
}
