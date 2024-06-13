<?php
// URL GeoServer Anda
$geoServerUrl = 'http://localhost:8080/geoserver/pgwl/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=pgwl%3Atable_kec&maxFeatures=50&outputFormat=application%2Fjson';

// Nama workspace dan layer di GeoServer
$workspace = 'pgwl';
$layerName = 'table_kec';

// Nama pengguna dan kata sandi GeoServer
$username = 'admin';
$password = 'geoserver';

// Membangun URL untuk mengambil GeoJSON dari GeoServer
$geoJsonUrl = "http://localhost:8080/geoserver/pgwl/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=pgwl%3Atable_kec&maxFeatures=50&outputFormat=application%2Fjson";

// Konfigurasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $geoJsonUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

// Eksekusi cURL dan dapatkan respons
$response = curl_exec($ch);

// Periksa apakah ada kesalahan
if(curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    // Mengirimkan header JSON
    header('Content-Type: application/json');
    // Mengirimkan data GeoJSON ke klien
    echo $response;
}

// Tutup koneksi cURL
curl_close($ch);
?>
