<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\MailerService4;


final class OrderController extends AbstractController
{
    private $mailerService4;

    public function __construct(MailerService4 $mailerService4)
    {
        $this->mailerService4 = $mailerService4;
    }
    #[Route('/order', name: 'app_order')]
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
         if ($total === 0) {
            $this->addFlash('warning', 'Votre panier est vide. Ajoutez des articles avant de finaliser votre commande.');
            return $this->redirectToRoute('app_cart'); // Remplacez 'app_panier' par le nom de la route vers votre page de panier
        }
         // Mise à jour du compteur d'articles dans la session
         $session->set('itemCount', $itemCount);
         
         if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $data = $request->request->all();
            
            // Stocker temporairement en session pour la page intermédiaire
            $session->set('order_data', $data);
            
            // Stocker également les informations du panier pour la page intermédiaire
            $session->set('cart_summary', [
                'cartItems' => $cartItems,
                'total' => $total,
                'itemCount' => $itemCount
            ]);
            
            // Rediriger vers la page de vérification
            return $this->redirectToRoute('app_order_verify');
        }
 
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'itemCount' => $itemCount,
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }
    
    #[Route('/order/verify', name: 'app_order_verify')]
    public function verify(SessionInterface $session,ProductsRepository $productsRepository, Request $request ,     MailerService4 $mailerService4): Response
    {

        $mailerService4->sendMail('Un client a visité la page de paiement');

        
        $orderData = $session->get('order_data', []);
        $cartSummary = $session->get('cart_summary', []);

        // Si les données n'existent pas ou sont incomplètes, rediriger vers la page de commande
    if (empty($orderData) || empty($cartSummary)) {
        $this->addFlash('warning', 'Veuillez remplir le formulaire de commande.');
        return $this->redirectToRoute('app_order');
    }
    
    // Vérifier que le panier n'est pas vide
    if (empty($cartSummary['cartItems']) || $cartSummary['total'] === 0) {
        $this->addFlash('warning', 'Votre panier est vide. Ajoutez des articles avant de finaliser votre commande.');
        return $this->redirectToRoute('app_cart');
    }
        
        // Vérifier si des données existent, sinon rediriger vers la page de commande
        if (empty($orderData)) {
            return $this->redirectToRoute('app_order');
        }
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
         if ($total === 0) {
            $this->addFlash('warning', 'Votre panier est vide. Ajoutez des articles avant de finaliser votre commande.');
            return $this->redirectToRoute('app_cart'); // Remplacez 'app_panier' par le nom de la route vers votre page de panier
        }
        return $this->render('order/verify.html.twig', [
            'order' => $orderData,
            'cartItems' => $cartSummary['cartItems'] ?? [],
            'total' => $cartSummary['total'] ?? 0,
            'itemCount' => $cartSummary['itemCount'] ?? 0,
        ]);
    }
}