<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use App\Repository\OrderStatusRepository;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;


final class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository, 
                          CategoriesRepository $categoriesRepository, Request $request): Response
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
        
        // Récupérer les paramètres de filtrage et tri
        $query = $request->query->get('query', '');
        $categoryId = $request->query->get('category');
        $sortBy = $request->query->get('sort', 'id'); // Valeur par défaut: id
        $direction = $request->query->get('direction', 'ASC'); // Valeur par défaut: ASC
        
        // Valider la direction (seulement ASC ou DESC)
        $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';
        
        // Valider le critère de tri
        $validSortFields = ['id', 'price', 'date'];
        $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'id';
        
        // Déterminer quels produits afficher en fonction des filtres
        if (!empty($query)) {
            // Si recherche textuelle, utiliser la méthode existante
            $products = $productsRepository->findByQuery($query);
        } else {
            // Sinon, utiliser notre nouvelle méthode avec tri
            $products = $productsRepository->findSorted($sortBy, $direction, $categoryId);
        }
        
        // Récupérer toutes les catégories pour le filtre
        $categories = $categoriesRepository->findAll();
        
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'itemCount' => $itemCount,
            'all' => $products,
            'categories' => $categories,
            'query' => $query,
            'selectedCategory' => $categoryId,
            'sortBy' => $sortBy,
            'direction' => $direction,
            'cartItems' => $cartItems,
            'total' => $total,

        ]);
    }

    #[Route('/shop/{id}', name: 'app_product_show')]
    public function show($id, ProductsRepository $productsRepository, SessionInterface $session, Request $request): Response
    {
        // Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $cartItems = [];
        $total = 0;
        $itemCount = 0;
        
        foreach ($cart as $productId => $quantity) {
            $cartProduct = $productsRepository->find($productId);
            
            if ($cartProduct) { // Vérification que le produit existe
                $cartItems[] = [
                    'product' => $cartProduct,
                    'quantity' => $quantity,
                ];
                
                // Calcul du total et du nombre d'articles
                $total += $cartProduct->getPrice() * $quantity;
                $itemCount += $quantity;
            } else {
                // Suppression de l'article du panier si le produit n'existe plus
                unset($cart[$productId]);
            }
        }
        
        // Récupération du produit à afficher (avec l'ID de l'URL)
        $product = $productsRepository->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        
        return $this->render('shop/show.html.twig', [
            'controller_name' => 'ShopController',
            'product' => $product,
            'itemCount' => $itemCount,
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/batterie-externe', name: 'app_batt')]
public function batt(
    SessionInterface $session, 
    ProductsRepository $productsRepository,
    CategoriesRepository $categoriesRepository,
    Request $request
): Response
{
    // Récupération du panier depuis la session
    $cart = $session->get('cart', []);
    $cartItems = [];
    $total = 0;
    $itemCount = 0;
    
    foreach ($cart as $id => $quantity) {
        $product = $productsRepository->find($id);
        
        if ($product) {
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
    
    // Récupérer les paramètres de tri
    $sortBy = $request->query->get('sort', 'id'); // Valeur par défaut: id
    $direction = $request->query->get('direction', 'ASC'); // Valeur par défaut: ASC
    
    // Valider la direction (seulement ASC ou DESC)
    $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';
    
    // Valider le critère de tri
    $validSortFields = ['id', 'price', 'date'];
    $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'id';
    
    // Récupérer la catégorie "batterie externe"
    $batterieCategory = $categoriesRepository->findOneBy(['Name' => 'Batterie Externe']); // Notez le 'N' majuscule ici
    
    // Si la catégorie existe, récupérer tous les produits de cette catégorie
    $products = [];
    if ($batterieCategory) {
        $categoryId = $batterieCategory->getId();
        // Utiliser la méthode findSorted avec uniquement la catégorie batterie externe
        $products = $productsRepository->findSorted($sortBy, $direction, $categoryId);
    }
    
    // Récupérer toutes les catégories pour le filtre (si vous souhaitez conserver le filtre)
    $categories = $categoriesRepository->findAll();
    
    return $this->render('shop/batterie.html.twig', [
        'controller_name' => 'ShopController',
        'itemCount' => $itemCount,
        'all' => $products,
        'categories' => $categories,
        'cartItems' => $cartItems,
        'total' => $total,
        'selectedCategory' => $batterieCategory ? $batterieCategory->getId() : null,
        'sortBy' => $sortBy,
        'direction' => $direction,
    ]);
}

#[Route('/couchage', name: 'app_couchage')]
public function couchage(
    SessionInterface $session, 
    ProductsRepository $productsRepository,
    CategoriesRepository $categoriesRepository,
    Request $request
): Response
{
    // Récupération du panier depuis la session
    $cart = $session->get('cart', []);
    $cartItems = [];
    $total = 0;
    $itemCount = 0;
    
    foreach ($cart as $id => $quantity) {
        $product = $productsRepository->find($id);
        
        if ($product) {
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
    
    // Récupérer les paramètres de tri
    $sortBy = $request->query->get('sort', 'id'); // Valeur par défaut: id
    $direction = $request->query->get('direction', 'ASC'); // Valeur par défaut: ASC
    
    // Valider la direction (seulement ASC ou DESC)
    $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';
    
    // Valider le critère de tri
    $validSortFields = ['id', 'price', 'date'];
    $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'id';
    
    // Récupérer la catégorie "batterie externe"
    $batterieCategory = $categoriesRepository->findOneBy(['Name' => 'Couchage']); // Notez le 'N' majuscule ici
    
    // Si la catégorie existe, récupérer tous les produits de cette catégorie
    $products = [];
    if ($batterieCategory) {
        $categoryId = $batterieCategory->getId();
        // Utiliser la méthode findSorted avec uniquement la catégorie batterie externe
        $products = $productsRepository->findSorted($sortBy, $direction, $categoryId);
    }
    
    // Récupérer toutes les catégories pour le filtre (si vous souhaitez conserver le filtre)
    $categories = $categoriesRepository->findAll();
    
    return $this->render('shop/couchage.html.twig', [
        'controller_name' => 'ShopController',
        'itemCount' => $itemCount,
        'all' => $products,
        'categories' => $categories,
        'cartItems' => $cartItems,
        'total' => $total,
        'selectedCategory' => $batterieCategory ? $batterieCategory->getId() : null,
        'sortBy' => $sortBy,
        'direction' => $direction,
    ]);
}


    #[Route('/camping-et-bouviac', name: 'app_camp')]
    public function camp(
        SessionInterface $session, 
        ProductsRepository $productsRepository,
        CategoriesRepository $categoriesRepository,
        Request $request
    ): Response
    {
        // Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $cartItems = [];
        $total = 0;
        $itemCount = 0;
        
        foreach ($cart as $id => $quantity) {
            $product = $productsRepository->find($id);
            
            if ($product) {
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
        
        // Récupérer les paramètres de tri
        $sortBy = $request->query->get('sort', 'id'); // Valeur par défaut: id
        $direction = $request->query->get('direction', 'ASC'); // Valeur par défaut: ASC
        
        // Valider la direction (seulement ASC ou DESC)
        $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';
        
        // Valider le critère de tri
        $validSortFields = ['id', 'price', 'date'];
        $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'id';
        
        // Récupérer la catégorie "batterie externe"
        $batterieCategory = $categoriesRepository->findOneBy(['Name' => 'Camping & Bouviac']); // Notez le 'N' majuscule ici
        
        // Si la catégorie existe, récupérer tous les produits de cette catégorie
        $products = [];
        if ($batterieCategory) {
            $categoryId = $batterieCategory->getId();
            // Utiliser la méthode findSorted avec uniquement la catégorie batterie externe
            $products = $productsRepository->findSorted($sortBy, $direction, $categoryId);
        }
        
        // Récupérer toutes les catégories pour le filtre (si vous souhaitez conserver le filtre)
        $categories = $categoriesRepository->findAll();
        
        return $this->render('shop/camp.html.twig', [
            'controller_name' => 'ShopController',
            'itemCount' => $itemCount,
            'all' => $products,
            'categories' => $categories,
            'cartItems' => $cartItems,
            'total' => $total,
            'selectedCategory' => $batterieCategory ? $batterieCategory->getId() : null,
            'sortBy' => $sortBy,
            'direction' => $direction,
        ]);
    }

    #[Route('/canoe-kayak', name: 'app_canoe')]
    public function canoe( 
        SessionInterface $session, 
        ProductsRepository $productsRepository,
        CategoriesRepository $categoriesRepository,
        Request $request
    ): Response
    {
        // Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $cartItems = [];
        $total = 0;
        $itemCount = 0;
        
        foreach ($cart as $id => $quantity) {
            $product = $productsRepository->find($id);
            
            if ($product) {
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
        
        // Récupérer les paramètres de tri
        $sortBy = $request->query->get('sort', 'id'); // Valeur par défaut: id
        $direction = $request->query->get('direction', 'ASC'); // Valeur par défaut: ASC
        
        // Valider la direction (seulement ASC ou DESC)
        $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';
        
        // Valider le critère de tri
        $validSortFields = ['id', 'price', 'date'];
        $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'id';
        
        // Récupérer la catégorie "batterie externe"
        $batterieCategory = $categoriesRepository->findOneBy(['Name' => 'Canoë/Kayak']); // Notez le 'N' majuscule ici
        
        // Si la catégorie existe, récupérer tous les produits de cette catégorie
        $products = [];
        if ($batterieCategory) {
            $categoryId = $batterieCategory->getId();
            // Utiliser la méthode findSorted avec uniquement la catégorie batterie externe
            $products = $productsRepository->findSorted($sortBy, $direction, $categoryId);
        }
        
        // Récupérer toutes les catégories pour le filtre (si vous souhaitez conserver le filtre)
        $categories = $categoriesRepository->findAll();
        
        return $this->render('shop/canoe.html.twig', [
            'controller_name' => 'ShopController',
            'itemCount' => $itemCount,
            'all' => $products,
            'categories' => $categories,
            'cartItems' => $cartItems,
            'total' => $total,
            'selectedCategory' => $batterieCategory ? $batterieCategory->getId() : null,
            'sortBy' => $sortBy,
            'direction' => $direction,
        ]);
    }


    #[Route('/sports-et-hobbies', name: 'app_sports')]
    public function sports( 
        SessionInterface $session, 
        ProductsRepository $productsRepository,
        CategoriesRepository $categoriesRepository,
        Request $request
    ): Response
    {
        // Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $cartItems = [];
        $total = 0;
        $itemCount = 0;
        
        foreach ($cart as $id => $quantity) {
            $product = $productsRepository->find($id);
            
            if ($product) {
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
        
        // Récupérer les paramètres de tri
        $sortBy = $request->query->get('sort', 'id'); // Valeur par défaut: id
        $direction = $request->query->get('direction', 'ASC'); // Valeur par défaut: ASC
        
        // Valider la direction (seulement ASC ou DESC)
        $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';
        
        // Valider le critère de tri
        $validSortFields = ['id', 'price', 'date'];
        $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'id';
        
        // Récupérer la catégorie "batterie externe"
        $batterieCategory = $categoriesRepository->findOneBy(['Name' => 'Sports & Hobbies']); // Notez le 'N' majuscule ici
        
        // Si la catégorie existe, récupérer tous les produits de cette catégorie
        $products = [];
        if ($batterieCategory) {
            $categoryId = $batterieCategory->getId();
            // Utiliser la méthode findSorted avec uniquement la catégorie batterie externe
            $products = $productsRepository->findSorted($sortBy, $direction, $categoryId);
        }
        
        // Récupérer toutes les catégories pour le filtre (si vous souhaitez conserver le filtre)
        $categories = $categoriesRepository->findAll();
        
        return $this->render('shop/sport.html.twig', [
            'controller_name' => 'ShopController',
            'itemCount' => $itemCount,
            'all' => $products,
            'categories' => $categories,
            'cartItems' => $cartItems,
            'total' => $total,
            'selectedCategory' => $batterieCategory ? $batterieCategory->getId() : null,
            'sortBy' => $sortBy,
            'direction' => $direction,
        ]);
    }


    #[Route('/promotions', name: 'app_promotions')]
    public function promo(SessionInterface $session, ProductsRepository $productsRepository,
                         CategoriesRepository $categoriesRepository, Request $request): Response
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
        
        // Récupérer les paramètres de filtrage et tri
        $query = $request->query->get('query', '');
        $categoryId = $request->query->get('category');
        $sortBy = $request->query->get('sort', 'id'); // Valeur par défaut: id
        $direction = $request->query->get('direction', 'ASC'); // Valeur par défaut: ASC
        
        // Valider la direction (seulement ASC ou DESC)
        $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';
        
        // Valider le critère de tri
        $validSortFields = ['id', 'price', 'date'];
        $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'id';
        
        // Récupérer tous les produits dont le pourcentage de promotion > 50%
        $qb = $productsRepository->createQueryBuilder('p')
            ->where('p.percentage > :percentage')
            ->setParameter('percentage', 30);
            
        // Filtrage par catégorie si spécifié
        if ($categoryId) {
            $qb->andWhere('p.category = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
        
        // Déterminer le champ de tri
        switch ($sortBy) {
            case 'price':
                $sortField = 'p.price';
                break;
            case 'date':
                $sortField = 'p.createdAt'; // ou le nom de votre champ date
                break;
            case 'id':
            default:
                $sortField = 'p.id';
        }
        
        // Appliquer le tri
        $qb->orderBy($sortField, $direction);
        
        // Récupérer les résultats
        $products = $qb->getQuery()->getResult();
        
        // Récupérer toutes les catégories pour le filtre
        $categories = $categoriesRepository->findAll();
        
        return $this->render('shop/promo.html.twig', [
            'controller_name' => 'PromotionController',
            'itemCount' => $itemCount,
            'all' => $products,
            'categories' => $categories,
            'query' => $query,
            'selectedCategory' => $categoryId,
            'sortBy' => $sortBy,
            'direction' => $direction,
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/order-status/check', name: 'app_suivre')]

    public function suivre(ProductsRepository $productsRepository,OrderStatusRepository $OrderStatusRepository ,SessionInterface $session, Request $request): Response
    {

           // Récupération du panier depuis la session
           $cart = $session->get('cart', []);
           $cartItems = [];
           $total = 0;
           $itemCount = 0;
           $itemCount = $session->get('itemCount');

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

        $formSubmitted = false; // Indicateur de soumission de formulaire
        $error_message = null;
        $success_message = null;
        $payment_pending = null;
        $nomClient = null;

        if ($request->isMethod('POST')) {
            $formSubmitted = true;
    
        // Récupère les données du formulaire
        $orderNumber = $request->request->get('order_number');
        $billingEmail = $request->request->get('billing_email');

        // Rechercher la commande avec SuccescommandeRepository directement
        $order = $OrderStatusRepository->findOneBy([
            'number' => $orderNumber,
            'email' => $billingEmail,
            
           
        ]);

      

        if (!$order) {
            // Cas où la commande n'existe pas
             // Marque le formulaire comme soumis
            $error_message = "Aucune commande ne correspond à ces informations. Veuillez vérifier votre e-mail ainsi que votre numéro de commande.";
        } else {
            // Cas où la commande existe - vérification de son statut
            $nomClient = $order->getName(); // Récupération du nom de celui qui a commandé

            if ($order->isStatut() === false) {
                // Si la commande est en attente de paiement (statut = false)
                $payment_pending = " en attente de paiement au nom de $nomClient. Merci de nous contacter par e-mail pour obtenir notre RIB. Dès réception du paiement, votre commande sera expédiée.";
            } elseif ($order->isStatut() === true) {
                // Si la commande est acceptée (statut = true)
                $success_message = "Votre commande est en cours de préparation. Vous recevrez un e-mail dès qu'elle sera expédiée.";
            }
        }
    }



        return $this->render('shop/suivre.html.twig', [
            'controller_name' => 'ProfilController',
            'error_message' => $error_message,
            'success_message' => $success_message,
            'payment_pending' => $payment_pending,
            'nomClient' => $nomClient,
            'formSubmitted' => $formSubmitted ,
            'itemCount' => $itemCount,
            'cartItems' => $cartItems,
            'total' => $total,
            'itemCount' => $itemCount,

        ]);
    }
}