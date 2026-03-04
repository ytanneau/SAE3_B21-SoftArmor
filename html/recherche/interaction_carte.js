const cotedarmor = document.getElementById("cotedarmor")
const finistere = document.getElementById("finistere")
const illeetvilaine = document.getElementById("illeetvilaine")
const morbihan = document.getElementById("morbihan")
const btn_reset_filter = document.getElementById("btn_reset_filter")

btn_reset_filter.addEventListener('click', (e) => {
    // permet de retirer tout les layers de la cartes 
    map.removeLayer(polygonCoteDarmor)
    map.removeLayer(polygonFinistere)
    map.removeLayer(polygonIlleEtVilaine)
    map.removeLayer(polygonMorbihan)
    groupeSelectionne = []
})

// Création des polygones grâce aux fichiers geojson
let polygonCoteDarmor
fetch("cotes-d-armor.geojson").then(res => res.json()).then(data => {
    polygonCoteDarmor = L.geoJSON(data)
})

let polygonFinistere
fetch("finistere.geojson").then(res => res.json()).then(data => {
    polygonFinistere = L.geoJSON(data)
})

let polygonIlleEtVilaine
fetch("ille-et-vilaine.geojson").then(res => res.json()).then(data => {
    polygonIlleEtVilaine = L.geoJSON(data)
})

let polygonMorbihan
fetch("morbihan.geojson").then(res => res.json()).then(data => {
    polygonMorbihan = L.geoJSON(data)
})

let groupSelectionne = [] // tableau pour les layers actifs afin d'adapter la vue sur la carte
let group

finistere.addEventListener('click', () => {
    if (finistere.checked) {
        // Ajout du polygon à la carte
        polygonFinistere.addTo(map)

        // Mise en place du groupe pour adapter automatiquement la vue
        groupSelectionne.push(polygonFinistere)
        group = L.featureGroup(groupSelectionne)

        // affichage adapte au groupe
        map.fitBounds(group.getBounds())
    } else {
        // enleve le polygone de la carte
        map.removeLayer(polygonFinistere)
        
        // enleve le polygone du groupe pour readapter la vue
        groupSelectionne = groupSelectionne.filter(dept => dept !== polygonFinistere)
        group = L.featureGroup(groupSelectionne)

        // si le groupe n'est pas vide alors on adapte la vue avec les layers restant 
        // sinon on revient à la vue initial
        if(groupSelectionne.length > 0) map.fitBounds(group.getBounds())
        else map.setView([48.113,-2.642],8)
    }
});

cotedarmor.addEventListener('click', (e) => {
    if(cotedarmor.checked) {
        polygonCoteDarmor.addTo(map)

        groupSelectionne.push(polygonCoteDarmor)
        group = L.featureGroup(groupSelectionne)

        map.fitBounds(group.getBounds())

    } else {
        map.removeLayer(polygonCoteDarmor)

        groupSelectionne = groupSelectionne.filter(dept => dept !== polygonCoteDarmor)
        group = L.featureGroup(groupSelectionne)

        if(groupSelectionne.length > 0) map.fitBounds(group.getBounds())
        else map.setView([48.113,-2.642],8)

    }
})

illeetvilaine.addEventListener('click', (e) => {
    if(illeetvilaine.checked) {
        polygonIlleEtVilaine.addTo(map)

        groupSelectionne.push(polygonIlleEtVilaine)
        group = L.featureGroup(groupSelectionne)

        map.fitBounds(group.getBounds())   
    } else {
        map.removeLayer(polygonIlleEtVilaine)

        groupSelectionne = groupSelectionne.filter(dept => dept !== polygonIlleEtVilaine)
        group = L.featureGroup(groupSelectionne)

        if(groupSelectionne.length > 0) map.fitBounds(group.getBounds())
        else map.setView([48.113,-2.642],8)
    }
})

morbihan.addEventListener('click', (e) => {
    if(morbihan.checked) {
        polygonMorbihan.addTo(map)

        groupSelectionne.push(polygonMorbihan)
        group = L.featureGroup(groupSelectionne)
        
        map.fitBounds(group.getBounds())
    } else {
        map.removeLayer(polygonMorbihan)

        groupSelectionne = groupSelectionne.filter(dept => dept !== polygonMorbihan)
        group = L.featureGroup(groupSelectionne)

        if(groupSelectionne.length > 0) map.fitBounds(group.getBounds())
        else map.setView([48.113,-2.642],8)
    }
})