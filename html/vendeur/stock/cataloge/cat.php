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

//require('./fpdf186/fpdf.php');
require('./tfpdf/tfpdf.php');
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

function info_produit_id($id)
{
    global $pdo;

    $requete = $pdo->prepare('
        SELECT p.*, url_image, alt, _image.titre
        FROM produit_en_ligne p
        INNER JOIN _image ON id_image_principale = _image.id_image WHERE id_vendeur = :id_vendeur AND id_produit = :id_produit');
    $requete->bindValue(':id_vendeur', $_SESSION['id_compte'], PDO::PARAM_INT);
    $requete->bindValue(':id_produit', $id, PDO::PARAM_INT);
    $requete->execute();
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function info_produit($id_liste)
{
    $res = [];
    foreach ($id_liste as $id){
        array_push($res, info_produit_id($id));
    }
}

class PDF extends tFPDF
{

    // Tableau simple
    function BasicTable($data)
    {
        $i = 0;
        // Données
        foreach ($data as $row) {
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Image(HOME_SITE . $row['url_image'], null, null, 20, 20);
            $this->SetXY(45, $y);
            //$this->SetY($y);
            $this->MultiCell(60, 10, $row['nom_public'], 0);
            $this->SetXY(50 + 65, $y);
            $this->Cell(30, 20, round($row['prix']+$row['prix']*($row['tva']/100), 2) . " €", 0, 0, 'c');
            $this->etoile($row['note_moy'], $this->GetX(), $this->GetY());
            $this->Ln();
            $this->Line(20, $y + 20, 200, $y + 21);
            $this->SetY($y + 22);
            $i++;
            if ($i == 11) {
                $i = 0;
                $this->AddPage();
                $this->Text($this->GetX(), $this->GetY(), "Le prix est sujet à variation");
                $this->head_tableau();
            }

        }
    }

    function etoile($moyenne, $x, $y)
    {
        $e=0;
        if (is_null($moyenne)) {
            $this->Cell(40, 20, "pas de note", 0, 0, 'c');
            return null;
        }
        if ($moyenne > 5 || $moyenne < 0) {
            $this->Cell(40, 20, "pas de note", 0, 0, 'c');
            return null;
        }
        // code de iwan pour calculer et afficher les moyennes d'un produit en fonction de sa moyenne
        for ($i = 1; $i <= floor($moyenne); $i++) {
            $this->Image(HOME_SITE."image/etoile_pleine.png", $x+5+5*$e, $y+5, 5, 5);
            $e++;
        }
        if (fmod(floor($moyenne * 2), 2)) {
            $this->Image(HOME_SITE."image/etoile_demi.png", $x+5+5*$e, $y+5, 5, 5);
            $e++;
        }
        for ($i = 5; $i > round($moyenne); $i--) {
            $this->Image(HOME_SITE."image/etoile_vide.png", $x+5+5*$e, $y+5, 5, 5);
            $e++;
        }
    }

    function head_tableau(){
        $this->SetFont('Arial', 'B', 10);
        $this->Ln();
        $this->SetY(15);
        $this->Cell(35, 5, "Image", 0, 0, 'c');
        $this->Cell(70, 5, "Nom", 0, 0, 'c');
        $this->Cell(30, 5, "Prix TTC", 0, 0, 'c');
        $this->Cell(40, 5, "Note", 0, 0, 'c');
        $this->SetFont('DejaVu', '', 10);
        $this->SetY(20);
    }

}



// Chargement des données
$data = info_produit_accueil();
//$data = info_produit($id_liste);

$pdf = new PDF();

//$data
$pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
$pdf->SetFont('DejaVu', '', 10);
//$pdf->SetFont('Arial', '', 10);
$pdf->AddPage();
$pdf->Text($pdf->GetX(), $pdf->GetY(), "Le prix est sujet à variation");
$pdf->head_tableau();
$pdf->Ln();
$pdf->BasicTable($data);
$pdf->Output();