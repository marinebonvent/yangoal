<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ProfileEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\OrderStatusRepository;



final class ProfilController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    #[Route('/profil', name: 'app_profil')]

    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un nouveau mot de passe a été soumis
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                );
                $user->setPassword($hashedPassword);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Profil mis à jour avec succès!');
            return $this->redirectToRoute('app_profil');
        }
        
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


    #[Route('/donnees', name: 'app_donnees')]

    public function donnees(): Response
    {
        $user = $this->getUser();

        return $this->render('profil/donnees.html.twig', [
            'controller_name' => 'ProfilController',
            'user' => $user,

        ]);
    }

    #[Route('/historique', name: 'app_historique')]

    public function historique(orderStatusRepository $orderStatusRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderStatusRepository->findBy(['email' => $user->getEmail()]);


        return $this->render('profil/historique.html.twig', [
            'controller_name' => 'ProfilController',
            'user' => $user,
            'orders' => $orders,


        ]);
    }

    #[Route('/bons-de-reduction', name: 'app_bons')]

    public function bons(): Response
    {
        $user = $this->getUser();

        return $this->render('profil/bons.html.twig', [
            'controller_name' => 'ProfilController',
            'user' => $user,

        ]);
    }

    #[Route('/message', name: 'app_message')]

    public function favoris(): Response
    {
        $user = $this->getUser();

        return $this->render('profil/favoris.html.twig', [
            'controller_name' => 'ProfilController',
            'user' => $user,

        ]);
    }


}
