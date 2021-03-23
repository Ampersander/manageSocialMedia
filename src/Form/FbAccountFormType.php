<?php

namespace App\Form;

use App\Entity\FbAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FbAccountFormType extends AbstractType
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
        ->add('accountId', TextType::class, [
            'attr'   =>  array(
                'class'   => 'm-2'),
            'constraints' => new NotBlank(),
                
        ])
        ->add('shortLivedToken', TextType::class, [
            'attr'   =>  array(
                'class'   => 'm-2'),
            'constraints' => new NotBlank(),
                 
         ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FbAccount::class,
        ]);
    }
}
