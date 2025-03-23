<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SearchResultController extends AbstractController
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }



    #[Route('/search', name: 'app_search_result')]
    public function index(Request $request , ProductsRepository $servicerepo, SessionInterface $session,): Response
    {

        $searchQuery = $request->get('q');
        
        $results = $this->searchService->search($searchQuery);

         // Récupération du panier depuis la session
         $cart = $session->get('cart', []);
         $cartItems = [];
         $total = 0;
         $itemCount = 0;
         
         foreach ($cart as $id => $quantity) {
             $product = $servicerepo->find($id);
             
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
        

        return $this->render('search_result/index.html.twig', [
            'controller_name' => 'SearchResultController',
            'results'=>$results ,
            'searchQuery'=>$searchQuery ,
            'cartItems' => $cartItems,
            'total' => $total,
            'itemCount' => $itemCount,


            
        ]);
    }
}
