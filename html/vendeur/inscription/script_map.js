let map = L.map('map').setView([48.113,-2.642],8)

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map)


const longitude = document.getElementById("longitude")
const latitude = document.getElementById("latitude")

let marker = L.marker([longitude.value, latitude.value]).addTo(map)
map.setView([longitude.value, latitude.value], 10)