<?php

namespace App\Controller;

use App\Repository\ReclamationgroupeRepository;
use App\Services\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    private $pdfGenerator;

    public function __construct(PdfGenerator $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    #[Route('/admin/reports/pdf', name: 'app_admin_reports_pdf')]
    public function generatePdfFromReports(ReclamationgroupeRepository $reclamationRepository)
    {
        $reports = $reclamationRepository->findAll();

        $htmlContent = $this->renderView('admin/reports_pdf.html.twig', [
            'reports' => $reports,
        ]);

        $filename = 'reports_' . date('Ymd_His') . '.pdf';
        $this->pdfGenerator->generatePdf($htmlContent, $filename);
    }
}