<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

  <title>Leaflet + GeoServer WMS</title>

  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    #map {
      width: 100%;
      height: 100%;
    }
    .legend-control {
      background: white;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 6px;
      box-shadow: 0 0 5px rgba(0,0,0,0.4);
      line-height: 1.4em;
      width: 150px;
    }
    .legend-control img {
      width: 130px;
    }
  </style>
</head>

<body>

<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script src="lib/L.Geoserver.js"></script>

<script>
// MAP UTAMA
var map = L.map("map").setView([-7.732521, 110.402376], 11);

var osm = L.tileLayer(
  "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  {
    maxZoom: 19,
    attribution: "© OpenStreetMap"
  }
).addTo(map);

// mengakses WMS dari GeoServer lokal
var urlLocalWMS = "http://localhost:8080/geoserver/wms";

// WMS LOKAL – ADMINISTRASI DESA
// Memanggil layer WMS dari GeoServer
var wmsAdminDesa = L.tileLayer.wms(urlLocalWMS, {
  layers: "PGWEB9:ADMINISTRASIDESA_AR_25K",
  format: "image/png",
  transparent: true
}).addTo(map);

// WMS EXTERNAL – JALAN (SLEMAN)
var jalan = L.tileLayer.wms(
  "https://geoportal.slemankab.go.id/geoserver/geonode/wms",
  {
    layers: "geonode:jalan_kabupaten_sleman_2023",
    format: "image/png",
    transparent: true
  }
).addTo(map);

// WMS LOKAL – DATA TITIK PENDUDUK SLEMAN (UPDATED)
var wmsPenduduk = L.tileLayer.wms(urlLocalWMS, {
  layers: "	PGWEB9:penduduk_sleman",   
  format: "image/png",
  transparent: true,
  styles: "",                           
  tiled: true,
  version: "1.1.0"
}).addTo(map);

// CONTROL LAYER
var overlayLayers = {
  "Administrasi Desa 25K": wmsAdminDesa,
  "Jalan Sleman": jalan,
  "Data Penduduk Sleman (Titik)": wmsPenduduk
};

L.control.layers(
  { "OpenStreetMap": osm },
  overlayLayers
).addTo(map);

// ========== LEGEND 1 – ADMINISTRASI DESA ==========
var legendAdmin = L.control({ position: "bottomleft" });

legendAdmin.onAdd = function (map) {
    //Membuat elemen HTML div untuk legend
  var div = L.DomUtil.create("div", "legend-control");


  //Membuat URL untuk mengambil legend otomatis dari GeoServer
  var url1 = "http://localhost:8080/geoserver/wms?" +
    "REQUEST=GetLegendGraphic" +
    "&VERSION=1.0.0" +
    "&FORMAT=image/png" +
    "&LAYER=PGWEB9:ADMINISTRASIDESA_AR_25K";

  div.innerHTML =
    "<b>Administrasi Desa</b><br>" +
    "<img src='" + url1 + "' alt='Legend'>";

  return div;
};
legendAdmin.addTo(map);

</script>

</body>
</html>
