<?php

namespace App\Form;

use App\Entity\User;
use App\Form\AnnonceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountType extends AnnonceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class , $this->getConfiguration("Prénom", "Votre prénom"))
            ->add('lastName', TextType::class , $this->getConfiguration("Nom", "Votre nom"))
            ->add('email', EmailType::class , $this->getConfiguration("Email", "Votre adresse email"))
            ->add('picture', UrlType::class , $this->getConfiguration("Url", "Url de votre photo de profile"))
            ->add('introduction', TextType::class , $this->getConfiguration("Introduction", "Présentation rapide"))
            ->add('description', TextareaType::class , $this->getConfiguration("Description détaillée", "Dites nous en un peu plus sur vous."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
