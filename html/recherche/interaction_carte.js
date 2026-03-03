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

let groupSelectionne = []
let group

finistere.addEventListener('click', () => {
    if (finistere.checked) {
        polygonFinistere.addTo(map)
        groupSelectionne.push(polygonFinistere)
        group = L.featureGroup(groupSelectionne)
        map.fitBounds(group.getBounds())
        console.log(groupSelectionne)
    } else {
        map.removeLayer(polygonFinistere)
        groupSelectionne.pop(polygonFinistere)
        console.log(groupSelectionne)
        if(groupSelectionne.length > 0) map.fitBounds(group.getBounds())
        else map.setView([48.113,-2.642],8)
    }
});

cotedarmor.addEventListener('click', (e) => {
    if(cotedarmor.checked) {
        polygonCoteDarmor.addTo(map)
        groupSelectionne.push(polygonCoteDarmor)
        console.log(groupSelectionne)
        group = L.featureGroup(groupSelectionne)
        map.fitBounds(group.getBounds())
    } else {
        map.removeLayer(polygonCoteDarmor)
        groupSelectionne.pop(polygonCoteDarmor)
        console.log(groupSelectionne)
        if(groupSelectionne.length > 0) map.fitBounds(group.getBounds())
        else map.setView([48.113,-2.642],8)
    }
})