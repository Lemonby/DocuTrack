<?php

namespace App\Services;

use Exception;

class PdfService
{
    /**
     * Generate PDF from View
     *
     * @param string $viewPath Path to the view file (e.g., __DIR__ . '/../views/pdf/template.php')
     * @param array $data Data to be extracted into the view
     * @param string $filename Output filename
     * @param string $mode Output mode: 'I' (Inline), 'D' (Download), 'F' (File), 'S' (String)
     * @param array $config MPDF configuration options
     * @return mixed
     * @throws Exception
     */
    public function generate($viewPath, $data = [], $filename = 'document.pdf', $mode = 'I', $config = [])
    {
        // Check if mPDF is installed
        if (!class_exists('\Mpdf\Mpdf')) {
            throw new Exception("mPDF library not found. Please run: composer require mpdf/mpdf");
        }

        if (!file_exists($viewPath)) {
            throw new Exception("PDF Template not found: {$viewPath}");
        }

        // 1. Render HTML
        extract($data);
        ob_start();
        try {
            include $viewPath;
        } catch (Exception $e) {
            ob_end_clean();
            throw new Exception("Error rendering PDF template: " . $e->getMessage());
        }
        $html = ob_get_clean();

        // 2. Default Config
        $defaultConfig = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 25,
            'margin_right' => 25,
            'margin_top' => 25,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
            'orientation' => 'P',
            'default_font' => 'Arial',
            'tempDir' => sys_get_temp_dir() // Tambahkan temp directory
        ];

        $mpdfConfig = array_merge($defaultConfig, $config);

        // 3. Generate PDF
        try {
            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            
            // Set Metadata
            if (isset($data['pdf_title'])) {
                $mpdf->SetTitle($data['pdf_title']);
            }
            if (isset($data['pdf_author'])) {
                $mpdf->SetAuthor($data['pdf_author']);
            }
            $mpdf->SetCreator('DocuTrack System');

            $mpdf->WriteHTML($html);
            
            return $mpdf->Output($filename, $mode);
            
        } catch (Exception $e) {
            throw new Exception("mPDF Error: " . $e->getMessage());
        }
    }
}
