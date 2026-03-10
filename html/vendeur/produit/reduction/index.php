<h3>Réduction</h3>
                <p>Prix actuel : <?=htmlentities($prix)?>€</p>
                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="pourcentage">Pourcentage : </label>
                        <input type="text" id="pourcentage" name="pourcentage">
                        <p style="display:none;" id="warning3">Le pourcentage ne peut <br>être supérieur à 100</p>
                    </div>
                    <div class="en_colonne">
                        <label for="euro">Remise appliquée : </label>
                        <input type="text" id="euro" name="euro" readonly>
                    </div>
                    <div class="en_colonne">
                        <label for="prixFinal">Prix final : </label>
                        <input type="text" id="prixFinal" readonly>
                    </div>
                </div>
                <script>
                    const warning3 = document.getElementById("warning3");
        const pourcentage = document.getElementById("pourcentage");
        const euro = document.getElementById("euro");
        const prixInitial = <?= json_encode($prix) ?>;
        const prixFinal = document.getElementById("prixFinal");
        warning3.style.display = "none";
        if (pourcentage.value >= 100){
                warning3.style.display = "block";
                event.preventDefault();
            }
        pourcentage.addEventListener('input', () => {
            pourcentage.value = pourcentage.value.replace(",",".");
            pourcentage.value = pourcentage.value.replace(/[^\d.,]/g,"");
            if(pourcentage.value <= 100){
                calculR();
            } else {
                warning3.style.display = "block";
            }
            
        })

        function calculR(){
            if(pourcentage.value != ""){
                prixFinal.value = prixInitial * (1 - pourcentage.value / 100);
                euro.value = prixInitial - prixFinal.value;
                prixFinal.value = Number.parseFloat(prixFinal.value).toFixed(2) + "€";
                euro.value = Number.parseFloat(euro.value).toFixed(2);
            } else {
                euro.value = "";
            }
        }
                </script>