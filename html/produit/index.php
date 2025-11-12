<?php
// Inclusion du fichier de configuration
require_once('../../.config.php');

// Vérifie que l'id_produit est passé dans l'URL
if (!isset($_GET['id_produit']) || !is_numeric($_GET['id_produit'])) {
    die("ID du produit invalide.");
}

$id_produit = (int) $_GET['id_produit'];

// $id_produit = $pdo->query("SELECT id_produit from _produit");

// Requête pour récupérer les infos du produit
$sql = "
SELECT 
    p.*,
    v.id_compte AS vendeur,
    i_principale.url_image AS image_principale,
    i1.url_image AS image1,
    i2.url_image AS image2
FROM _produit p
JOIN _vendeur v ON p.id_vendeur = v.id_compte
LEFT JOIN _images_produit ip ON p.id_produit = ip.id_produit
LEFT JOIN _image i_principale ON ip.id_image_principale = i_principale.id_image
LEFT JOIN _image i1 ON ip.id_image1 = i1.id_image
LEFT JOIN _image i2 ON ip.id_image2 = i2.id_image
WHERE p.id_produit = :id_produit
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
    $stmt->execute();
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        die("Produit introuvable.");
    }

} catch (PDOException $e) {
    die("Erreur lors de la récupération du produit : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($produit['nom_public'] ?? 'Produit'); ?></title>
</head>
<body>

    <?php
    // Affichage des images en priorité (image principale puis secondaires)
    $imageKeys = ['image_principale', 'image1', 'image2'];
    foreach ($imageKeys as $k) {
        if (!empty($produit[$k])) {
            echo '<div><img src="' . htmlspecialchars($produit[$k]) . '" alt="' . htmlspecialchars($produit['nom_public'] ?? 'Image') . '"></div>';
        }
    }

    // Nom du produit
    echo '<h1>' . htmlspecialchars($produit['nom_public'] ?? 'Produit') . '</h1>';

    // Vendeur
    if (!empty($produit['vendeur'])) {
        echo '<p><strong>Vendeur :</strong> ' . htmlspecialchars($produit['vendeur']) . '</p>';
    }

    // Description courte
    if (!empty($produit['description'])) {
        echo '<p><strong>Description :</strong> ' . nl2br(htmlspecialchars($produit['description'])) . '</p>';
    }

    // Prix
    if (isset($produit['prix'])) {
        $formatted = is_numeric($produit['prix']) ? number_format($produit['prix'], 2, ',', ' ') . ' €' : htmlspecialchars($produit['prix']);
        echo '<p><strong>Prix :</strong> ' . $formatted . ' (TVA ' . htmlspecialchars($produit['tva'] ?? '') . '%)</p>';
    }

    // Description détaillée
    if (!empty($produit['description_detaillee'])) {
        echo '<p><strong>Détails :</strong> ' . nl2br(htmlspecialchars($produit['description_detaillee'])) . '</p>';
    }
    ?>
    <?php
    $stock = isset($produit['stock']) ? (int)$produit['stock'] : null;
    $disabled = ($stock !== null && $stock <= 0) ? 'disabled' : '';
    ?>
    
    <button type="button" onclick="window.location.href='achat/coordonnees.php?id_produit=<?php echo (int)$id_produit; ?>'" <?php echo $disabled; ?>>
        <?php echo $disabled ? 'Indisponible' : 'Acheter'; ?>
    </button>

</body>
</html>
