<?php

namespace App\Service;

use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapper;

class Pdfgenerator
{
    public function __construct(private readonly DompdfFactoryInterface $factory ,private readonly   DompdfWrapper $wrapper)
    {
        
    }

    public function getPdf(string $html): string
    {
        return $this->wrapper->getPdf($html);
    }

    public function output(string $html): string
    {
        try {
            $dompdf = $this->factory->create();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();
            return $dompdf->output();
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }
}
