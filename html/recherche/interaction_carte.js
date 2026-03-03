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


finistere.addEventListener('click', () => {
    if (finistere.checked) {
        polygonFinistere.addTo(map)
        map.fitBounds(polygonFinistere.getBounds())
    } else {
        map.removeLayer(polygonFinistere)
        map.setView([48.113,-2.642],8)
    }
});

cotedarmor.addEventListener('click', (e) => {
    if(cotedarmor.checked) {
        polygonCoteDarmor.addTo(map)
        map.fitBounds(polygonCoteDarmor.getBounds())
    } else {
        map.removeLayer(polygonCoteDarmor)
        map.setView([48.113,-2.642],8)
    }
})