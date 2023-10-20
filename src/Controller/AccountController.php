<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;

class AccountController extends AbstractController
{
    /**
     * Permet à l'utilisateur de se connecter
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        $loginError = null;

        if ($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            $loginError = 'Trop de tentatives de connexion. Réessayez plus tard.';
        }
        
        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @return void
     */
    #[Route("/logout", name: "account_logout")]
    public function logout(): void
    {

    }

    /**
     * Permet d'afficher le formulaire d'inscription ainsi que la gestion de l'inscription de l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
     #[Route('/register', name: 'account_register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            //gestion de l'image 
            $file = $form['picture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($this->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().".".$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }
            //gestion de l'inscription de l'utilisateur dans la base de données
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('account_login');

        }
        return $this->render('account/registration.html.twig', [
        'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet à l'utilisateur de modifier son profil
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * */
    #[Route('/account/profile', name: 'account_profile')]
    public function profile (Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); // Permet de récupérer l'utilisateur connecté

        // Pour la validation des images(plus tard validation groups)
        $filename = $user->getPicture();
        if(!empty($filename)){
            $user->setPicture(
            new File($this->getParameter('uploads_directory')."/".$user->getPicture())
            );
        }

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setSlug('')
                ->setPicture($filename);
            
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Les données de votre profil ont bien été enregistrées');
        }

        return $this->render('account/profile.html.twig', [
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet à l'utilisateur de modifier son mot de passe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/account/password-update', name: 'account_password')]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser(); // Permet de récupérer l'utilisateur connecté
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getPassword()))
            {
                $form->get('oldPassword')->addError(new FormError ("Le mot de passe que vous avez renseigné n'est pas le mot de passe actuel")); //
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Votre nouveau mot de passe a bien été enregistré');

                return $this->redirectToRoute('homepage');
            }

        }

        return $this->render('account/password.html.twig', [
            'myForm' => $form->createView()
        ]);
    }

}