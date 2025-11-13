<?php                 
    // appel du fichier de configuration bdd
    require_once "../../../../.config.php";

    if (!isset($_SESSION)) {
    session_start();
    }
    //verifie si quelqun est connecté
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../../');
        exit;
    }
    $id_compte = $_SESSION['id_compte'];
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Ajout produit</title>
    
        <meta charset="UTF-8">
        <style>
            fieldset{
                width: 50em;
            }
            .nomProd{
                width: 20em;
            }
            .infoPlus{
                display: flex;
                flex-direction: row;
            }
            .infoPlus > p{
                margin-right: 10px;
            }
            .descSimple{
                width: 50em;
                height : 10em;
            }
            .descDetaille{
                width: 50em;
                height : 25em;
            }
            p{
                display : flex;
                flex-direction : column; 
            }
        </style>
    </head>
    <body>
        <header>

        </header>
        <main>
            <!-- Bouton de retour sur la page de gestion des stocks -->
            <a href="../"><img src="" alt="bouton retour en arrière"></a>
            <h1>Ajouter un produit au stock</h1>

            <!-- Formulaire de saisie des infos du produit -->
            <form action="" name="formulaire" method="post" enctype="multipart/form-data">
                <fieldset>
                    <h3>Informations produit</h3>
                    <div class="infoPlus">
                        <p>
                            <label for="nomPrv">Libellé privé*</label>
                            <input class="nomProd" type="text" name="nomPrv" id="nomPrv" required>
                        </p>
                        <p>
                            <label for="nomPblc">Libellé public*</label>
                            <input class="nomProd" type="text" name="nomPblc" id="nomPblc" required>
                        </p>
                    </div>
                    <div class="infoPlus">
                        <p>
                            <label for="prixProd">Prix* hors taxe (€)</label>
                            <input type="text" name="prixProd" id="prixProd" required>
                        </p>
                        <p>
                            <label for="tva">TVA* (%)</label>
                            <input type="number" name="tva" id="tva" required>
                        </p>
                        
                        <p>
                            <label for="codeBarre">Code barre*</label>
                            <input type="text" name="codeBarre" id="codeBarre" maxlength="13" style="width:162.4px" required>
                            <span id="messageErrCodeBarre" style="display:none; color:red">Le code barre doit comporter 13 chiffres</span>
                        </p>
                    </div>
                    <div>
                        <label for="checkMajeur">Réservé aux majeurs</label>
                        <input type="checkbox" name="checkMajeur" id="checkMajeur">
                    </div>
                    <div class="infoPlus">
                        <p>
                            <label for="categorie">Catégories*</label>
                            <select name="categorie" id="categorie" style="width: 175px;" required>
                                <option value="">-- Choisir une catégorie --</option>

                                <?php
                                    $categorie = $pdo->query("SELECT nom_categorie FROM _categorie WHERE nom_categorie_sup IS NULL");
                                    $lignesCategorie = $categorie->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($lignesCategorie as $nomCat){ ?>
                                    <option value="<?php echo htmlspecialchars($nomCat['nom_categorie']) ?>">
                                        <?php echo htmlspecialchars($nomCat['nom_categorie']) ?>
                                    </option>
                                <?php    
                                }
                                ?>
                            </select>
                            <div id="divSousCategorieAlimentaire" style="display:none;">
                                <p>
                                    <label for="sous_categorie">Sous-catégories*</label>

                                    <select name="sous_categorie" id="sous_cate">
                                        <option value="">-- Choisir une catégorie --</option>
                                        <option value="Boisson">
                                            <?php 
                                                $categorie = $pdo->query("SELECT nom_categorie FROM _categorie WHERE nom_categorie = 'Boisson' "); 
                                                echo $categorie->fetch(PDO::FETCH_ASSOC)['nom_categorie']; 
                                            ?>
                                        </option>
                                        <option value="Salé">
                                            <?php 
                                                $categorie = $pdo->query("SELECT nom_categorie FROM _categorie WHERE nom_categorie = 'Salé' "); 
                                                echo $categorie->fetch(PDO::FETCH_ASSOC)['nom_categorie']; 
                                            ?>
                                        </option>
                                        <option value="Sucré">
                                            <?php 
                                                $categorie = $pdo->query("SELECT nom_categorie FROM _categorie WHERE nom_categorie = 'Sucré' "); 
                                                echo $categorie->fetch(PDO::FETCH_ASSOC)['nom_categorie']; 
                                            ?>
                                        </option>
                                    </select>
                                </p>
                            </div>
                        </p>
                    </div>
                    <div class="infoPlus">
                        <p>
                            <label for="">Quantité acheté</label>
                            <input type="text" name="qtAchete">
                        </p>
                        <p id="blockUniteVetement" style="display:none;">
                            <label for="uniteVetement">Unités de Masse</label>
                            <br>
                            <select name="unite" id="uniteVetement">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="xs">XS</option>
                                <option value="s">S</option>
                                <option value="m">M</option>
                                <option value="l">L</option>
                                <option value="xl">XL</option>
                            </select>
                        </p>
                        <p id="blockUniteMasse" style="display:none;">
                            <label for="uniteMasse">Unités de masse</label>
                            <br>
                            <select name="unite" id="uniteMasse">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="g">g</option>
                                <option value="kg">kg</option>
                            </select>
                        </p>
                        <p id="blockUniteLiquide" style="display:none;">
                            <label for="uniteLiquide">Unités de liquide</label>
                            <br>
                            <select name="unite" id="uniteLiquide">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="ml">ml</option>
                                <option value="cl">cl</option>
                                <option value="l">l</option>
                            </select>
                        </p>
                    </div>
                    <hr>
                    <h3>Photo principale</h3>
                    <div>
                        <label for="photo">Importer des images du produit*</label>
                        <input type="file" name="photo" id="photo" accept=".png" required>
                    </div>
                    <hr>
                    <h3>Gestion de stock</h3>
                    <div class="infoPlus">
                        <p>
                            <label for="qtStock">Quantité en stock</label>
                            <input type="number" name="qtStock" id="qtStock">
                        </p>
                        <p>
                            <label for="seuilAlerte">Seuil d'alerte</label>
                            <input type="number" name="seuilAlerte" id="seuilAlerte">
                        </p>
                    </div>
                    <hr>
                    <h3>Description</h3>
                    <p>
                        <label for="descSimple">Description simple (200 caractéres maximum)</label>
                        <textarea class="descSimple" name="descSimple" id="descSimple" maxlength="200"></textarea>
                        <label for="descDetaille">Description détaillé (2000 caractéres maximum)</label>
                        <textarea class="descDetaille" type="textarea" name="descDetaille" id="descDetaille" maxlength="2000"></textarea>
                    </p>
                    <hr>
                    <h3>Livraison</h3>
                    <div class="infoPlus">
                        <p>
                            <label for="poidProd">Poids* (Kg)</label>
                            <input type="text" name="poidProd" id="poidProd" required>
                        </p>
                        <p>
                            <label for="volumeProd">Volume du colis* (L)</label>
                            <input type="text" name="volumeProd" id="volumeProd" required>
                        </p>   
                    </div>
                    <br>
                    <a href="../index.php"><input type="submit" value="Créer le produit" id="creerProduit"></a>
                </fieldset>
            </form>
            
            <script>
                /*
                    Script pour assuré l'intégrité des données en temps réel (dynamique)
                */
                const nomPrv = document.getElementById("nomPrv"); 
                const nomPblc = document.getElementById("nomPblc");

                const categorie = document.getElementById("categorie");
                const divSousCategorie = document.getElementById("divSousCategorieAlimentaire");
                const selectSousCategorieAlimentaire = document.getElementById("sous_cate");

                const uniteLiquide = document.getElementById("blockUniteLiquide");
                const uniteMasse = document.getElementById("blockUniteMasse");
                const uniteVetement = document.getElementById("blockUniteVetement");

                const tva = document.getElementById("tva");
                const prix = document.getElementById("prixProd");

                const descSimple = document.getElementById("descSimple");
                const descDetaille = document.getElementById("descDetaille");
                
                const codeBarre = document.getElementById("codeBarre");
                const messageErrCodeBarre = document.getElementById("messageErrCodeBarre");

                const poid = document.getElementById("poidProd");
                const volume = document.getElementById("volumeProd");
                const checkMajeur = document.getElementById("checkMajeur");

                const photo = document.getElementById("photo");

                const creerProduit = document.getElementById("creerProduit");
                
                selectSousCategorieAlimentaire.addEventListener('change', () => {
                    if(selectSousCategorieAlimentaire.value === "Sucré" || selectSousCategorieAlimentaire.value === "Salé"){
                        uniteLiquide.style.display = "none";
                        uniteVetement.style.display = "none";
                        
                        uniteMasse.style.display = "block";
                    } else if (selectSousCategorieAlimentaire.value === "Boisson"){
                        uniteMasse.style.display = "none";
                        uniteVetement.style.display = "none";

                        uniteLiquide.style.display = "block";
                    } else {
                        uniteMasse.style.display = "none";
                        uniteVetement.style.display = "none";
                        uniteLiquide.style.display = "none";
                    }
                })

                categorie.addEventListener('change', () => {
                    if(categorie.value === "Alimentaire"){
                        divSousCategorie.style.display = "block";
                    } 
                    else if(categorie.value === "Electroménager" || categorie.value === "Electronique" ||
                    categorie.value === "Soin & Hygiène"){
                        uniteMasse.style.display = "block";

                        uniteVetement.style.display = "none";
                        uniteLiquide.style.display = "none";
                        
                        selectSousCategorieAlimentaire.value = null;
                        divSousCategorie.style.display = "none";
                    } else {
                        uniteMasse.style.display = "none";
                    }
                })

                descSimple.addEventListener('input', () => {
                    if(descSimple.value.length === 200){
                        alert("Maximum de caractère atteint");
                    }
                })
                descDetaille.addEventListener('input', ()=> {
                    if(descDetaille.value.length === 2000){
                        alert("Maximum de caratère atteint");
                    }
                })

                
                function checkCodeBarre(chaineCodeBarre){
                    if(chaineCodeBarre.length < 13) return true;
                    else return false;
                }
                codeBarre.addEventListener('input', () =>{
                    codeBarre.value = codeBarre.value.replace(/\D/g,"");
                    if(codeBarre.value.length < 13){
                        messageErrCodeBarre.style.display = "block";
                        event.preventDefault();
                    } else {
                        messageErrCodeBarre.style.display = "none";
                    }
                })

                checkMajeur.addEventListener('change', () => {
                    if(checkMajeur.checked){
                        if(!confirm("Confirmer ?")){
                            checkMajeur.checked = 0;
                        }
                    }
                })

                creerProduit.addEventListener('click' , () =>  {
                    if(!checkMajeur.checked){
                        checkMajeur.checked = 0;
                    }

                    if ((nomPrv.value === "") || (nomPblc.value === "") || 
                        (categorie.value === "") || (tva.value === "") || 
                        (prix.value === "") || (codeBarre.value === "") || 
                        (poid.value === "") || (volume.value === "") || 
                        (photo.files.length === 0) || (checkCodeBarre(codeBarre.value))){
                        alert("Les champs obligatoires ne sont pas tous remplis")
                        event.preventDefault();
                    } else if(confirm("Confirmer la création du produit ?")) {
                        alert("Produit créer");
                    } else {
                        event.preventDefault();
                    }
                })
            </script>
            
            <?php 
                if($_SERVER["REQUEST_METHOD"] == "POST"){
                    // recupération des données du formulaire
                    $nomPrv = $_POST["nomPrv"];
                    $nomPblc = $_POST["nomPblc"];
                    
                    $tva = $_POST["tva"];
                    $prixProd = $_POST["prixProd"];
                    $descSimple = $_POST["descSimple"];
                    $descDetaille = $_POST["descDetaille"];
                    $codeBarre = $_POST["codeBarre"];
                    $poidProd = $_POST["poidProd"];
                    $volumeProd = $_POST["volumeProd"];
                    $quantite = $_POST["qtAchete"];
                    $unite = $_POST["unite"];

                    if($_POST["sous_categorie"] === ""){ $categorie = $_POST["categorie"]; }
                    else { $categorie = $_POST["sous_categorie"]; }                    

                    if($_POST["qtStock"] === ""){ $qtStock = 0; } 
                    else { $qtStock = $_POST["qtStock"]; }
                    
                    if($_POST["seuilAlerte"] === ""){ $seuilAlerte = 0; } 
                    else { $seuilAlerte = $_POST["seuilAlerte"]; }

                    // redéfinition du critéres sur la majorité suivant l'état du bouton
                    $checkMajeur = isset($_POST["checkMajeur"]) ? 1 : 0;

                    // insertion du produit dans la base de données
                    $sqlAjoutProduit = "INSERT INTO _produit(id_vendeur,nom_stock,nom_public,description,description_detaillee,code_barre,quantite,prix,tva,seuil_alerte,poids,volume,plus_18,quantite_unite) 
                                        VALUES(:id_vend, :nomPrv, :nomPblc, :descSimple, :descDetaille, :codeBarre, :qtStock, :prixProd, :tva, :seuilAlerte, :poidProd, :volumeProd, :checkMajeur, :qtunite); 
                                        ";
                    $stmt = $pdo->prepare($sqlAjoutProduit);
                    $stmt->execute([
                        ':id_vend' => $id_compte, // passé en $_SESSION une fois la page vendeur finis
                        ':nomPrv' => $nomPrv,
                        ':nomPblc' => $nomPblc,
                        ':descSimple' => $descSimple,
                        ':descDetaille' => $descDetaille,
                        ':codeBarre' => $codeBarre,
                        ':qtStock' => $qtStock,
                        ':prixProd' => $prixProd,
                        ':tva' => $tva,
                        ':seuilAlerte' => $seuilAlerte,
                        ':poidProd' => $poidProd, 
                        ':volumeProd' => $volumeProd,
                        ':checkMajeur' => $checkMajeur,
                        ':qtunite' => $quantite . ";" . $unite
                    ]);

                    $idProduit = $pdo->lastInsertId();

                    // mise en relation entre le produit et sa catégorie dans la bdd 
                    $sqlProduitCategorie = "INSERT INTO _produit_dans_categorie(id_produit,nom_categorie)
                                            VALUES(:id_prod,:nom_cate);
                                            ";
                    $stmt = $pdo->prepare($sqlProduitCategorie);
                    $stmt->execute([
                        ':id_prod' => $idProduit,
                        ':nom_cate' => $categorie
                    ]);

                    /*
                        Image du produit
                    */
                    // vérification de la presence d'une images 
                    if (isset($_FILES['photo'])){
                        $nomImageTemp = $_FILES['photo']['name'];
                        $cheminTemp = $_FILES['photo']['tmp_name'];
                        
                        $nomImage = $idProduit . "_1.png";
                        $cheminFinal = "../../../images/" . $nomImage;
                        $url = "images/" . $nomImage;

                        $titre_img = explode('.',$nomImageTemp)[0];
                        $altDefault = "Image du produit : " . $titre_img;

                        
                        if(move_uploaded_file($cheminTemp,$cheminFinal)){
                            // insertion des images dans la bdd 
                            $sqlImage = "INSERT INTO _image(url_image,titre,alt)
                                        VALUES(:url_img, :titre_img, :alt_img);";
                            $stmt = $pdo->prepare($sqlImage);
                            $stmt->execute([
                                ':url_img' => $url, 
                                ':titre_img' => $titre_img, 
                                ':alt_img' => $altDefault
                            ]);
                            
                            // mise en relation entre le produit et l'image principale dans la bdd 
                            // en utilisant l'id du produit et l'id de l'image 
                            $sqlImageProduit = "INSERT INTO _images_produit(id_produit,id_image_principale)
                                                VALUES(:id_prod,:id_image_princ);";
                            $idImage = $pdo->lastInsertId();
                            $stmt = $pdo->prepare($sqlImageProduit);
                            $stmt->execute([
                                ':id_prod' => $idProduit,
                                ':id_image_princ' => $idImage
                            ]);
                        }
                    }
                }
            ?>
        </main>
        <footer>

        </footer>
    </body>
</html>