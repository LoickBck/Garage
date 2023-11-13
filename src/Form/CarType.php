<?php

namespace App\Form;

use App\Entity\Car;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CarType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, $this->getConfiguration('Marque','Donnez la marque du véhicule'))
            ->add('slug', TextType::class, $this->getConfiguration('Slug', 'Adresse web (automatique)',[
                'required' => false
            ]))
            ->add('model', TextType::class, $this->getConfiguration('Modèle','Donnez le modèle du véhicule'))
            ->add('coverImage', UrlType::class, $this->getConfiguration("Url de l'image", "Donnez l'adresse de votre image"))
            ->add('mileage', IntegerType::class, $this->getConfiguration('Nombre de kilomètres','Donnez le nombre de kilomètres du véhicule'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix','Indiquer le prix du véhicule'))
            ->add('n_owner', IntegerType::class, $this->getConfiguration('Nombre de propriétaires','Donnez le nombre de propriétaires du véhicule'))
            ->add('displacement', TextType::class, $this->getConfiguration('Cylindrée','Donnez la cylindrée du véhicule'))
            ->add('power', IntegerType::class, $this->getConfiguration('Puissance', 'Donnez la puissance du véhicule'))
            ->add('fuel', TextType::class, $this->getConfiguration('Carburant', 'Indiquez le type de carburant du véhicule en chevaux'))
            ->add('manufacturingYear', DateType::class, [
                'widget' => 'single_text',
                'html5' => false, // Ajoutez ceci si vous ne voulez pas de champ de type HTML5
                'format' => 'dd/MM/yyyy', // Spécifiez le format de date souhaité
                'help' => 'Indiquez la date dans ce format : DD/MM/YYYY', // Ajoutez un commentaire ici
            ])
            ->add('transmission', TextType::class, $this->getConfiguration('Transmission', 'Indiquez le type de transmission du véhicule'))
            ->add('description', TextType::class, $this->getConfiguration('Description', 'Donnez une description du véhicule'))
            ->add('options', TextareaType::class, $this->getConfiguration('Options', 'Donnez des options détaillées du véhicule'))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true, // pour le data_prototype
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
