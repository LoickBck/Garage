<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Image;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Expression\Expression;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarController extends AbstractController
{

    /**
     * Permet d'afficher l'ensemble des présentations du site
     *
     * @param CarRepository $repo
     * @return Response
     */
    #[Route('/cars', name: 'cars_index')]
    public function index(CarRepository $repo): Response
    {

        $cars = $repo->findAll();

        return $this->render('car/index.html.twig', [
            'cars' => $cars
        ]);
    }


    /**
     * Permet d'ajouter une présentation à la bdd 
     *
     * @return Response
     */
    #[Route("/cars/new", name:"cars_create")]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {

        $car = new Car();

        $form = $this->createForm(CarType::class, $car);

        // $arrayForm = $request->request->all();
        // dump($arrayForm);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // gestion des images 
            foreach($car->getImages() as $image)
            {
                $image->setCar($car);
                $manager->persist($image);
            }


            // je persiste mon objet Car
            $manager->persist($car);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success', 
                "La présentation du <strong>".$car->getModel()."</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('cars_show',[
                'slug' => $car->getSlug()
            ]);

        }

        return $this->render("car/new.html.twig",[
            'myForm' => $form->createView()
        ]);

    }

    /**
     * Permet d'éditer une présentation
     *
     * @param Car $car
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route("/cars/{slug}/edit", name:"cars_edit")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["car"]'),
        message: "Cette présentation ne vous appartient pas , vous ne pouvez pas la modifier"
    )]
    public function edit(Request $request, Car $car, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            // $car->setSlug(""); //modifier l'url de la page par rapport au titre de la présentation

              // gestion des images 
              foreach($car->getImages() as $image)
              {
                  $image->setCar($car);
                  $manager->persist($image);
              }

              $manager->persist($car);
              $manager->flush();

              $this->addFlash(
                'success',
                "La présentation du <strong>".$car->getModel()."</strong> a bien été modifiée!"
              );

              return $this->redirectToRoute('cars_show',[
                'slug' => $car->getSlug()
              ]);
  
        }

        return $this->render("car/edit.html.twig", [
            'car' => $car,
            'myForm' => $form->createView()
            ]);
    }

        /**
     * Permet de supprimer une présentation
     *
     * @param Car $car
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/cars/{slug}/delete", name:"cars_delete")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["car"]'),
        message: "Cette présentation ne vous appartient pas, vous ne pouvez pas la supprimer"
    )]
    public function delete(Car $car, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            'success',
            "La présentation du <strong>".$car->getModel()."</strong> a bien été supprimée"
        );

        $manager->remove($car);
        $manager->flush();

        return $this->redirectToRoute('cars_index');
    }


    /**
     * Permet d'afficher une présentation
     * 
     * @param string $slug
     * @param Car $car
     * @return Response
     */
    #[Route("/cars/{slug}", name:"cars_show")]
    public function show(string $slug, Car $car): Response
    {
        // $car = $repo->findby(["slug"=>$slug])

        // dump($car);

        return $this->render("car/show.html.twig", [
            'car' => $car
        ]);
    }


}
