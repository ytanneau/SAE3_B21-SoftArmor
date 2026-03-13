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
        // Données
        foreach ($data as $row) {
                $this->Image(HOME_SITE . $row['url_image'], null,null,0, 20);
                $this->Cell(40, 20, $row['nom_public'], 1);
                $this->Cell(40, 20, $row['prix'], 1);
            $this->Ln();
        }
    }

}

$data = info_produit_accueil();
$pdf = new PDF();
// Titres des colonnes
$header = array('Pays', 'Capitale', 'Superficie (km²)', 'Pop. (milliers)');
// Chargement des données
//$data
$pdf->SetFont('Arial', '', 11);
$pdf->AddPage();
$pdf->Text($pdf->GetX(), $pdf->GetY(), "Le prix est sujet a variation");
$pdf->Ln();
$pdf->BasicTable( $data);
$pdf->Output();