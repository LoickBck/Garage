<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Car;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{

    
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // création d'un admin
        $admin = new User();
        $admin->setFirstName('Loick')
            ->setLastName('Buck')
            ->setEmail('buckl@epse.be')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
            ->setRoles(['ROLE_ADMIN'])
            ->setPicture('');

        $manager->persist($admin);

        // gestion des utilisateurs
        $users = [];
        $genres = ['male', 'femelle'];

        for ($u = 1; $u <= 10; $u++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://picsum.photos/seed/picsum/500/500';

            $hash = $this->passwordHasher->hashPassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setPassword($hash)
                ->setPicture($picture);

            $manager->persist($user);

            $users[] = $user; // ajouter un user au tableau (pour les annonces)
        }

        // Génération des voitures
        for ($i = 1; $i <= 20; $i++) {
            $car = new Car();
            $car->setModel($faker->word)
                ->setManufacturingYear($faker->numberBetween(1900, 2100))
                ->setPrice($faker->numberBetween(5000, 50000))
                ->setDescription($faker->paragraph);

            $manager->persist($car);

            for($i=1; $i<=30; $i++)
        {
            $car = new Car();
            $model = $faker->sentence();
            $coverImage = 'https://picsum.photos/seed/picsum/1000/350';
            $description= $faker->paragraph(2);
            $options = '<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>';

           // liaison avec l'user
           $user = $users[rand(0, count($users)-1)];

           $car
           ->setBrand($brand)
           ->setModel($model)
           ->setCoverImage($coverImage)
           ->setMileage($mileage)
           ->setNOwner($nOwner)
           ->setPrice(rand(0, 9999999))
           ->setDisplacement($displacement)
           ->setPower($power)
           ->setFuel($fuel)
           ->setManufacturingYear($manufacturingYear)
           ->setTransmission($transmission)
           ->setDescription($description)
           ->setOptions($options);
       

            // Gestion de la galerie d'images pour chaque voiture
            for ($g = 1; $g <= rand(2, 5); $g++) {
                $image = new Image();
                $image->setUrl('https://picsum.photos/id/' . $g . '/900')
                    ->setCaption($faker->sentence())
                    ->setCar($car);
                $manager->persist($image);
            }
            $manager->persist($car);
        }
        $manager->flush();
    }
}
}