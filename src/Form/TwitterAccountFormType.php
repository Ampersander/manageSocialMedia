<?php

namespace App\Form;

use App\Entity\TwitterAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TwitterAccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'mapped' => false,
            'attr'   =>  array(
                'class'   => 'm-2'),
            'constraints' => new NotBlank(),       
        ])
        ->add('consumerKey', TextType::class, [
            'attr'   =>  array(
                'class'   => 'm-2'),
            'constraints' => new NotBlank(),
                
        ])
        ->add('consumerSecret', TextType::class, [
            'attr'   =>  array(
                'class'   => 'm-2'),
            'constraints' => new NotBlank(),
                 
         ])
         ->add('accessToken', TextType::class, [
            'attr'   =>  array(
                'class'   => 'm-2'),
            'constraints' => new NotBlank(),
                
        ])
        ->add('accessTokenSecret', TextType::class, [
            'attr'   =>  array(
                'class'   => 'm-2'),
            'constraints' => new NotBlank(),
                 
         ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TwitterAccount::class,
        ]);
    }
}
