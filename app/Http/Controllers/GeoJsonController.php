<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeoJsonController extends Controller
{
    public function fetchGeoJson()
    {
        // URL GeoServer Anda
        $geoServerUrl = 'http://localhost:8080/geoserver/pgwl/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=pgwl%3Atable_kec&maxFeatures=50&outputFormat=application%2Fjson';

        // Nama pengguna dan kata sandi GeoServer
        $username = 'admin';
        $password = 'geoserver';

        // Konfigurasi cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $geoServerUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

        // Eksekusi cURL dan dapatkan respons
        $response = curl_exec($ch);

        // Periksa apakah ada kesalahan
        if(curl_errno($ch)) {
            return response()->json(['error' => 'Curl error: ' . curl_error($ch)], 500);
        }

        // Tutup koneksi cURL
        curl_close($ch);

        return response($response, 200)
                ->header('Content-Type', 'application/json');
    }
}
