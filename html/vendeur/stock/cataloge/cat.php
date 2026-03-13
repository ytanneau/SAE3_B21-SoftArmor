<?php
require('./fpdf186/fpdf.php');

class PDF extends FPDF
{

    // Tableau simple
    function BasicTable($header, $data)
    {
        // En-tête
        foreach ($header as $col)
            $this->Cell(40, 7, $col, 1);
        $this->Ln();
        // Données
        foreach ($data as $row) {
            foreach ($row as $col)
                $this->Cell(40, 6, $col, 1);
            $this->Ln();
        }
    }

}


$pdf = new PDF();
// Titres des colonnes
$header = array('Pays', 'Capitale', 'Superficie (km²)', 'Pop. (milliers)');
// Chargement des données
//$data
$pdf->SetFont('Arial', '', 14);
$pdf->AddPage();
$pdf->Text($pdf->GetX(),$pdf->GetY(), "Le prix est sujet a variation");
$pdf->Ln();
//$pdf->BasicTable($header, $data);
$pdf->Output();