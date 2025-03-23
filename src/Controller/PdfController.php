<?php

namespace App\Controller;

use App\Service\Pdfgenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function index(Pdfgenerator $pdfgenerator): Response
    {
        // Contenu HTML pour le PDF
        $htmlContent = '<h1>ddd</h1>';
 $hmtl=$this->renderView('thankyou/email.html.twig');
        // Générer le contenu du PDF avec le service Pdfgenerator
        $pdfContent = $pdfgenerator->getPdf( $html);

        // Créer une réponse HTTP avec le contenu PDF
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }
}
