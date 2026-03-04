const cotedarmor = document.getElementById("cotedarmor")
const finistere = document.getElementById("finistere")
const illeetvilaine = document.getElementById("illeetvilaine")
const morbihan = document.getElementById("morbihan")

let polygonFinistere
fetch("finistere.geojson").then(res => res.json()).then(data => {
    polygonFinistere = L.geoJSON(data)
})

let polygonCoteDarmor
fetch("cotes-d-armor.geojson").then(res => res.json()).then(data => {
    polygonCoteDarmor = L.geoJSON(data)
})

let polygonIlleEtVilaine
fetch("ille-et-vilaine.geojson").then(res => res.json()).then(data => {
    polygonIlleEtVilaine = L.geoJSON(data)
})

let polygonMorbihan
fetch("morbihan.geojson").then(res => res.json()).then(data => {
    polygonMorbihan = L.geoJSON(data)
})

let groupSelectionne = []
let group

finistere.addEventListener('click', () => {
    if (finistere.checked) {
        // Ajout du polygon
        polygonFinistere.addTo(map)

        // Mise en place du groupe
        groupSelectionne.push(polygonFinistere)
        group = L.featureGroup(groupSelectionne)

        // affichage adapte au groupe
        map.fitBounds(group.getBounds())
    } else {
        map.removeLayer(polygonFinistere)

        groupSelectionne = groupSelectionne.filter(dept => dept !== polygonFinistere)
        group = L.featureGroup(groupSelectionne)

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