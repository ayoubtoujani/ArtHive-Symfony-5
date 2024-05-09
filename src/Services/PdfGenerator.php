<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

class PdfGenerator
{
    private $pdf;

    public function __construct(\Knp\Snappy\Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    public function generatePdfResponse(string $html): Response
    {
        $filename = 'reports.pdf';

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $filename,
            'application/pdf',
            ResponseHeaderBag::DISPOSITION_INLINE
        );
    }
}
