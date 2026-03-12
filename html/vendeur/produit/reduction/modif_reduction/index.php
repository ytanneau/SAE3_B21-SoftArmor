<script>
    function fill_form_with_data(){
        const d1 = new Date(dateDebut.value + "T00:00:00");
        const d2 = new Date(dateFin.value + "T00:00:00");

        const diffJours = (d2 - d1) / 86400000;
        cout.value = PRIX * diffJours + PRIX + "€";
        
        euro.value = prixInitial * (pourcentage.value / 100);
        euro.value = Number.parseFloat(euro.value).toFixed(2);
        prixFinal.value = prixInitial - euro.value;
        prixFinal.value = Number.parseFloat(prixFinal.value).toFixed(2);
    }

    fill_form_with_data();
</script>