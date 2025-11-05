<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Ajout produit</title>
    
        <meta charset="UTF-8">
        <style>
            label{
                padding-bottom: 50em;
            }
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
        </style>
    </head>
    <body>
        <header>

        </header>
        <main>
            <!-- Bouton de retour sur la page de gestion des stocks -->
            <a href="pageVendeur.php"><img src="" alt="bouton retour en arrière"></a>
            <h1>Ajouter un produit au stock</h1>

            <!-- Formulaire de saisie des infos du produit -->
            <form action="" name="formulaire" method="post" enctype="multiplart/data-form">
                <fieldset>
                    <div class="infoPlus">
                        <p>
                            <label for="nomPrv">Nom privé*</label>
                            <br>
                            <input class="nomProd" type="text" name="nomPrv" id="nomPrv" required>
                        </p>
                        <p>
                            <label for="nomPblc">Nom public du produit*</label>
                            <br>
                            <input class="nomProd" type="text" name="nomPblc" id="nomPblc" required>
                        </p>
                    </div>
                    <div class="infoPlus">
                        <p>
                            <label for="qtStock">Quantité en stock</label>
                            <br>
                            <input type="text" name="qtStock" id="qtStock">
                        </p>
                        <p>
                            <label for="tva">TVA* (%)</label>
                            <br>
                            <input type="text" name="tva" id="tva" required>
                        </p>
                        <p>
                            <label for="prixProd">Prix* (€)</label>
                            <br>
                            <input type="text" name="prixProd" id="prixProd" required>
                        </p>
                    </div>
                    <p>
                        <label for="descSimple">Description simple (200 caractéres maximum)</label>
                        <br>
                        <textarea class="descSimple" name="descSimple" id="descSimple" maxlength="200"></textarea>
                        <br>
                        <label for="descDetaille">Description détaillé (2000 caractéres maximum)</label>
                        <br>
                        <textarea class="descDetaille" type="textarea" name="descDetaille" id="descDetaille" maxlength="2000"></textarea>
                    </p>
                    <div class="infoPlus">
                        <p>
                            <label for="seuilAlerte">Seuil d'alerte</label>
                            <br>
                            <input type="text" name="seuilAlerte" id="seuilAlerte">
                        </p>
                        <p>
                            <label for="codeBarre">Code barre</label>
                            <br>
                            <input type="text" name="codeBarre" id="codeBarre">
                        </p>
                    </div>
                    <div class="infoPlus">
                        <p>
                            <label for="poidProd">Poids* (Kg)</label>
                            <br>
                            <input type="text" name="poidProd" id="poidProd" required>
                        </p>
                        <p>
                            <label for="volumeProd">Volume* (L)</label>
                            <br>
                            <input type="text" name="volumeProd" id="volumeProd" required>
                        </p>
                        <p>
                            <label for="checkMajeur">Réservé aux majeurs</label>
                            <br>
                            <input type="checkbox" name="checkMajeur" id="checkMajeur">
                        </p>    
                    </div>
                    <p>
                        <label for="photo">Importer des images du produit</label>
                        <input type="file" name="photo" accept=".png" required>
                    </p>
                    <input type="submit" value="Créer le produit" id="creerProduit">
                </fieldset>
            </form>
            <script>
                /*
                    Script pour assuré l'intégrité des données en temps réel (dynamique)
                */
                const descSimple = document.getElementById("descSimple");
                const checkMajeur = document.getElementById("checkMajeur");
                const creerProduit = document.getElementById("creerProduit");

                checkMajeur.addEventListener('change', () => {
                    if(checkMajeur.checked){
                        if(!confirm("Confirmer ?")){
                            checkMajeur.checked = false;
                        }
                    }
                })

                creerProduit.addEventListener('click' , () =>  {
                    if(!checkMajeur.checked){
                        checkMajeur.checked = false;
                    }
                    if(confirm("Confirmer la création du produit ?")) {
                        alert("Produit créer");
                    } else {
                        event.preventDefault();
                    }
                })

                descSimple.addEventListener('input', () => {
                    if(descSimple.value.length === 200){
                        alert("Maximum de caractère atteint");
                    }
                })
            </script>
            <?php 
                require_once "../../../config.php";
                
                $nomPrv = $_POST["nomPrv"];
                $nomPblc = $_POST["nomPblc"];
                $qtStock = $_POST["qtStock"];
                $tva = $_POST["tva"];
                $prixProd = $_POST["prixProd"];
                $descSimple = $_POST["descSimple"];
                $descDetaille = $_POST["descDetaille"];
                $seuilAlerte = $_POST["seuilAlerte"];
                $codeBarre = $_POST["codeBarre"];
                $poidProd = $_POST["poidProd"];
                $volumeProd = $_POST["volumeProd"];
                $checkMajeur = isset($_POST["checkMajeur"]);

                

                if($checkMajeur == 'on'){
                    $checkMajeur = true;
                } else {
                    $checkMajeur = false;
                }

                $sqlAjoutProduit = "INSERT INTO _produit(id_vendeur,nom_stock,nom_public,description,description_detaillee,code_barre,quantite,prix,tva,seuil_alerte,poids,volume,plus_18) 
                                    VALUES(:id_vend, :nomPrv, :nomPblc, :descSimple, :descDetaille, :codeBarre, :qtStock, :prixProd, :tva, :seuilAlerte, :poidProd, :volumeProd, :checkMajeur); 
                                    ";
                $stmt = $pdo->prepare($sqlAjoutProduit);
                $stmt->execute([
                    ':id_vend' => 1, // passé en $_SESSION une fois la page vendeur finis
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
                    ':checkMajeur' => $checkMajeur
                ]);

                $idProduit = $pdo->lastInsertId();

                echo $idProduit;

                /*
                    Image du produit
                */
                $image = $_POST["photo"];
                
                if(move_uploaded_file($image,"../../../images/")){
                    $nomImage = $idProduit . "_" . $image;
                    $url = "../images/" . $nomImage;
                    
                    $titre_img = explode('.',$image)[0];
                    $altDefault = "Image du produit : " . $titre_img;

                    $sqlImage = "INSERT INTO _image(url_image,titre,alt)
                            VALUES(:url_img, :titre_img, :alt_img);";
                    $stmt = $pdo->prepare($sqlImage);
                    $stmt->execute([
                    ':url_img' => $url, 
                    ':titre_img' => $titre_img, 
                    ':alt_img' => $altDefault
                    ]);
                    
                    $sqlImageProduit = "INSERT INTO _images_produit(id_produit,id_image_principale)
                                        VALUES(:id_prod,:id_image_princ);";
                    
                    $stmt = $pdo->prepare($sqlImageProduit);
                    $stmt->execute([
                        ':id_prod' => $idProduit,
                        ':id_image_princ' => 1 // a changer avec la SESSION
                    ]);
                }
                
            ?>
        </main>
        <footer>

        </footer>
    </body>
</html>