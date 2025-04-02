<?php
require_once APPPATH . '../vendor/autoload.php';

// require_once APPPATH . 'third_party/dompdf/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf {
    public function createPDF($html, $filename, $download = false) {
        $options = new Options();
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($download) {
            $dompdf->stream($filename . ".pdf", ["Attachment" => true]);
        } else {
            return $dompdf->output();
        }
    }
}
