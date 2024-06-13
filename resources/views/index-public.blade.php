@extends('layouts.templates')

@section('styles')
    <style>
        html,
        body {
            height: 100%;
            width: 100%;
        }

        #map {
            height: calc(100vh - 56px);
            width: 100%;
            margin: 0;
        }
    </style>
@endsection
</head>

@section('content')
    <div id ="map"></div>
@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/terraformer@1.0.7/terraformer.js"></script>
    <script src="https://unpkg.com/terraformer-wkt-parser@1.1.2/terraformer-wkt-parser.js"></script>
    <script>
        //Map
        var map = L.map('map').setView([-6.1767, 106.7550], 12);

        /* Tile Basemap */
        var basemap1 = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '<a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="DIVSIGUGM" target="_blank">DIVSIG UGM</a>' //menambahkan nama//
        });
        var basemap4 = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org / ">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
        });
        basemap1.addTo(map);

        //layer kecamatan
        var kecamatanLayer = L.geoJson(null, {
            style: function(feature) {
                var randomColor = getRandomColor();
                return {
                    fillColor: randomColor,
                    weight: 2,
                    opacity: 1,
                    color: 'white',
                    dashArray: '3',
                    fillOpacity: 0.8
                };
            },
            onEachFeature: function(feature, layer) {
                var content = "Kecamatan: " + feature.properties.wadmkc + "<br>";
                layer.on({
                    click: function(e) {
                        layer.bindPopup(content).openPopup();
                    },
                    mouseover: function(e) {
                        layer.setStyle({
                            weight: 3,
                            color: '#666',
                            dashArray: '',
                            fillOpacity: 0.2
                        });
                    },
                    mouseout: function(e) {
                        kecamatanLayer.resetStyle(layer);
                    }
                });
            }
        });

        // Fungsi untuk mendapatkan warna random
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        fetch('/get-geojson') // Ubah endpoint sesuai dengan rute Anda
            .then(response => response.json())
            .then(data => {
                kecamatanLayer.addData(data); // Menambahkan data GeoJSON ke layer
                kecamatanLayer.addTo(map); // Menambahkan layer ke peta
            })
            .catch(error => console.log('Error loading GeoJSON:', error));



        var jalanLayer = L.layerGroup();
        fetch('Geojson/jalan.geojson')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    style: function(feature) {
                        return {
                            color: "#ff0000",
                            weight: 0.5
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.namobj) {
                            layer.bindPopup("Nama Jalan: " + feature.properties.namobj);
                        }
                    }
                }).addTo(jalanLayer);
            })
            .catch(error => console.error('Error loading GeoJSON data:', error));

        // Load GeoJSON data for trans
        var transLayer = L.layerGroup();
        fetch('Geojson/trans.geojson')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    style: function(feature) {
                        return {
                            color: "#0000ff",
                            weight: 1
                        };
                    },

                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.jurusan && feature.properties
                            .koridor) {
                            // Membuat konten popup dengan informasi tambahan
                            var popupContent = "Jurusan: " + feature.properties.jurusan + "<br>Koridor: " +
                                feature.properties.koridor;
                            // Mengikat konten popup ke layer
                            layer.bindPopup(popupContent);
                        }
                    }
                }).addTo(transLayer);
            })
            .catch(error => console.error('Error loading GeoJSON data:', error));

        var baseMaps = {
            "OpenStreetMap": basemap1,
            "Stadia Dark Mode": basemap4,
        };

        var overlayMaps = {

        };


        L.control.layers(baseMaps, overlayMaps, {
            collapsed: false,
            position: 'topright'
        }).addTo(map);




        /* GeoJSON Point */
        var point = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Name: " + feature.properties.name + "<br>" +
                    "Description: " + feature.properties.description + "<br>" +
                    "Foto: <img src='{{ asset('storage/image/') }}/" + feature.properties.image +
                    "'class='img-thumbnail' alt=''> ";

                layer.on({
                    click: function(e) {
                        point.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        point.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        // menampilkan point pada peta
        $.getJSON("{{ route('api.points') }}", function(data) {
            point.addData(data);
            map.addLayer(point);
        });


        /* GeoJSON polyline */
        var polyline = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Name: " + feature.properties.name + "<br>" +
                    "Description: " + feature.properties.description + "<br>" +
                    "Foto: <img src='{{ asset('storage/image/') }}/" + feature.properties.image +
                    "'class='img-thumbnail' alt=''> ";

                layer.on({
                    click: function(e) {
                        polyline.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        polyline.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        // menampilkan polyline pada peta
        $.getJSON("{{ route('api.polylines') }}", function(data) {
            polyline.addData(data);
            map.addLayer(polyline);
        });

        /* GeoJSON polygon */
        var polygon = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Name: " + feature.properties.name + "<br>" +
                    "Description: " + feature.properties.description + "<br>" +
                    "Foto: <img src='{{ asset('storage/image/') }}/" + feature.properties.image +
                    "'class='img-thumbnail' alt=''> ";

                layer.on({
                    click: function(e) {
                        polygon.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        polygon.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        // menampilkan polygon pada peta
        $.getJSON("{{ route('api.polygons') }}", function(data) {
            polygon.addData(data);
            map.addLayer(polygon);
        });



        //layer control
        var overlayMaps = {
            'Sarpras': point,
            'Kecamatan': kecamatanLayer,
            'Jalan': jalanLayer,
            'Jalur Trans': transLayer
        };
        var layerControl = L.control.layers(null, overlayMaps, {
            collapsed: false
        }).addTo(map);
    </script>
@endsection
