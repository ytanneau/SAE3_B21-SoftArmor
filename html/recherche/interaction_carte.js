const cotedarmor = document.getElementById("cotedarmor")
const finistere = document.getElementById("finistere")
const illeetvilaine = document.getElementById("illeetvilaine")
const morbihan = document.getElementById("morbihan")

let polygonFinistere

fetch("finistere.geojson")
  .then(res => res.json())
  .then(data => {

    polygonFinistere = L.geoJSON(data, {
      style: {
        color: "blue",
        weight: 2,
        fillOpacity: 0.2
      }
    })
  })

finistere.addEventListener('click', () => {

  if (finistere.checked) {
    polygonFinistere.addTo(map);
    map.fitBounds(polygonFinistere.getBounds())
  } else {
    map.removeLayer(polygonFinistere)
  }

});


cotedarmor.addEventListener('click', (e) => {
    
})