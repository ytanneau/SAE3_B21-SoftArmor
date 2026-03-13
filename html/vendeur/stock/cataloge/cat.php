<?php
define("HOME_GIT", "../../../../");
define("HOME_SITE", "../../../");

if (!isset($_SESSION)) {
    session_start();
}

// Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
    header('location: ' . HOME_SITE);
    exit;
}
// Sinon si je ne suis pas connecté, retour à la page connexion vendeur
else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    header('location: ../');
    exit;
}

require('./fpdf186/fpdf.php');
require_once HOME_GIT . '.config.php';
//require_once HOME_GIT . 'fonction_produit.php';

function info_produit_accueil()
{
    global $pdo;

    $requete = $pdo->prepare('
        SELECT p.*, url_image, alt, _image.titre
        FROM produit_en_ligne p
        INNER JOIN _image ON id_image_principale = _image.id_image WHERE id_vendeur = :id_vendeur');
    $requete->bindValue(':id_vendeur', $_SESSION['id_compte'], PDO::PARAM_INT);
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

class PDF extends FPDF
{

    // Tableau simple
    function BasicTable($data)
    {
        $i = 0;
        // Données
        foreach ($data as $row) {
                $x = $this->GetX();
                $y = $this->GetY();
                $this->Image(HOME_SITE . $row['url_image'], null,null,0, 30);
                $this->SetXY(45, $y);
                //$this->SetY($y);
                $this->MultiCell(60, 10, $row['nom_public'], 0);
                $this->SetXY(50+65, $y);
                $this->Cell(40, 20, round($row['prix'],2). " €", 0, 0, 'c');
            $this->Ln();
            $this->Line(0, $y+30, 200, $y+31);
            $this->SetY( $y+32);
        }
        $i++;
        if($i == 8){
            $i = 0;
            $this->AddPage();
        }

    }

}

// Chargement des données
$data = info_produit_accueil();
$pdf = new PDF();

//$data
$pdf->SetFont('Arial', '', 10);
$pdf->AddPage();
$pdf->Text($pdf->GetX(), $pdf->GetY(), "Le prix est sujet a variation");
$pdf->Ln();
$pdf->Ln();
$pdf->BasicTable( $data);
$pdf->Output();