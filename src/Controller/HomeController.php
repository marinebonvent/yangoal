<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductsRepository $productsRepository,SessionInterface $session,Request $request): Response
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

         
        $itemCount = $session->get('itemCount');
        $products = $productsRepository->findBy([], ['id' => 'DESC'], 4);
        $this->addFlash('cart_success', 'Le produit a été ajouté au panier');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
            'itemCount' => $itemCount,
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }
}
