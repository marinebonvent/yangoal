<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository, Request $request): Response
    {
        // Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $cartItems = [];
        $total = 0;
        $itemCount = 0;
        
        foreach ($cart as $id => $quantity) {
            $product = $productsRepository->find($id);
            
            if ($product) { // Vérification que le produit existe
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
                
                // Calcul du total et du nombre d'articles
                $total += $product->getPrice() * $quantity;
                $itemCount += $quantity;
            } else {
                // Suppression de l'article du panier si le produit n'existe plus
                unset($cart[$id]);
            }
        }
        
        // Mise à jour du compteur d'articles dans la session
        $session->set('itemCount', $itemCount);

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'itemCount' => $itemCount,
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'app_add')]
    public function add(Products $products, ProductsRepository $productsRepository, SessionInterface $session): Response
    {
        // Récupération de l'ID du produit
        $id = $products->getId();

        // Récupération du panier depuis la session (ou création s'il n'existe pas)
        $cart = $session->get('cart', []);
        $this->addFlash('cart_success', 'Le produit a été ajouté au panier');

        // Ajout du produit dans le panier ou incrémentation de sa quantité
        if (empty($cart[$id])) {
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        } // Mise à jour du panier dans la session
        $session->set('cart', $cart);
       
        
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_product_show', [
            'id' => $id,
            'added' => 'true'
        ]);
    }

    #[Route('/remove/{id}', name: 'app_remove')]
    public function remove(Products $products, SessionInterface $session, ProductsRepository $productsRepository): Response
    {
        // Récupération de l'ID du produit
        $id = $products->getId();
        
        // Récupération du panier
        $cart = $session->get('cart', []);
        
        // Décrémentation de la quantité ou suppression si quantité = 1
        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }

        // Mise à jour du panier dans la session
        $session->set('cart', $cart);
        
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(Product $product, SessionInterface $session, ProductsRepository $productsRepository): Response
    {
        // Récupération de l'ID du produit
        $id = $product->getId();
        
        // Récupération du panier
        $cart = $session->get('cart', []);
        
        // Suppression complète du produit du panier
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        // Mise à jour du panier dans la session
        $session->set('cart', $cart);
        
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart');
    }
}