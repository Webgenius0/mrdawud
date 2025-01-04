@extends('backend.app')

@section('title', 'Location Edit')
@push('style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
        }

        .popup-card {
            max-width: 300px;
        }

        .popup-img {
            width: 100%;
            height: auto;
        }

        .advanced--search--modal,
        .maps--floating--option--menu {
            display: none;
        }

        .show {
            display: block;
        }
    </style>
@endpush

@section('content')
    <main class="app-content content">
        <!-- Search -->
        <div id="map"></div>
        <br>
        <form action="{{route('admin.location.update',$data->id)}}" method="post">
            @csrf
            <div class="row" style="display: flex; justify-content: center;">
                <div>
                    <input type="hidden" id="longitude" name="longitude" readonly value="{{$data->longitude ??''}}">
                    <input type="hidden" id="latitude" name="latitude" readonly value="{{$data->latitude ??''}}">
                </div>
                <div class="card" style="width: 60rem; padding:10px">
                    <div class="col-md-12" style="display: flex;">
                        <input type="text" id="locationInput" class="form-control"
                            placeholder="Enter search location name">
                        <button id="searchButton" class="btn btn-success" type="button">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </button>
                    </div><br>
                    <div class="col-md-12" style="display: flex;">
                        <input type="text" id="name" name="name" class="form-control" placeholder="Location name" readonly value="{{$data->name ?? ''}}">
                        <!-- Only 1 submit button here -->
                        <button id="submitButton" class="btn btn-success" type="submit">
                            <i class="fa-solid fa-check"></i> Update
                        </button>
                    </div><br>
                    <div class="col-md-12">
                        <button id="map--filter" type="button">Advanced Search</button>
                    </div>
                    <div class="advanced--search--modal">
                        <input type="text" id="countryInput" placeholder="Country">
                        <input type="text" id="cityInput" placeholder="City">
                        <input type="text" id="areaInput" placeholder="Area">
                        <input type="text" id="roadInput" placeholder="Road">
                        <input type="text" id="zipInput" placeholder="ZIP Code">
                        <button id="advancedSearchButton" type="button">Search</button>
                        <button class="advanced--search--close--button" type="button">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </main>
@endsection
@push('script')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const googleApiKey = "AIzaSyDl7ias7CMBPanjqPisVXwhXXVth21Cl5Y";

            const locations = [{
                    name: "Eiffel Tower",
                    latitude: 48.8584,
                    longitude: 2.2945
                },
                // Add more locations as needed...
            ];

            const customIcon = L.icon({
                iconUrl: "./assets/images/locationDot.png",
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32],
            });

            const map = L.map("map").setView([locations[0].latitude, locations[0].longitude], 5);

            L.tileLayer(`https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`, {
                maxZoom: 19,
                attribution: "&copy; The Media Vault",
            }).addTo(map);

            // Function to fetch Address using Google Geocoding API
            async function fetchAddress(lat, lng) {
                const geocodeUrl =
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${googleApiKey}`;
                const response = await fetch(geocodeUrl);
                const data = await response.json();
                return data.results[0]?.formatted_address || "Address not found";
            }

            // Add click event listener to capture coordinates
            map.on("click", async function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Set the latitude and longitude in the corresponding input fields
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;

                // Fetch the location name using reverse geocoding
                const locationName = await fetchAddress(lat, lng);

                document.getElementById("name").value = locationName;
                // Display an alert with the coordinates and location name
                alert(`Location selected:\nName: ${locationName}`);
            });

            // Function to fetch coordinates based on search query
            async function getCoordinates(locationName) {
                const url =
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(locationName)}`;
                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data && data.length > 0) {
                        return {
                            latitude: parseFloat(data[0].lat),
                            longitude: parseFloat(data[0].lon),
                        };
                    } else {
                        throw new Error("Location not found");
                    }
                } catch (error) {
                    console.error("Error fetching coordinates:", error.message);
                    alert("Unable to find the location. Please try again.");
                }
            }

            document.getElementById("searchButton").addEventListener("click", async () => {
                const locationName = document.getElementById("locationInput").value;
                if (locationName) {
                    const coordinates = await getCoordinates(locationName);
                    if (coordinates) {
                        const {
                            latitude,
                            longitude
                        } = coordinates;
                        map.setView([latitude, longitude], 10);
                        L.marker([latitude, longitude]).addTo(map).bindPopup(locationName).openPopup();
                    }
                } else {
                    alert("Please enter a location name.");
                }
            });

            document.getElementById("advancedSearchButton")?.addEventListener("click", async () => {
                const country = document.getElementById("countryInput").value;
                const city = document.getElementById("cityInput").value;
                const area = document.getElementById("areaInput").value;
                const road = document.getElementById("roadInput").value;
                const zip = document.getElementById("zipInput").value;

                const query = `${road} ${area} ${city} ${zip} ${country}`.trim();

                if (query) {
                    const coordinates = await getCoordinates(query);
                    if (coordinates) {
                        const {
                            latitude,
                            longitude
                        } = coordinates;
                        map.setView([latitude, longitude], 15);
                        L.marker([latitude, longitude]).addTo(map).bindPopup(query).openPopup();
                    }
                } else {
                    alert("Please provide at least one location detail.");
                }
            });

            document.querySelector("#map--filter").addEventListener("click", () => {
                document.querySelector(".advanced--search--modal").classList.toggle("show");
            });

            document.querySelector(".advanced--search--close--button").addEventListener("click", () => {
                document.querySelector(".advanced--search--modal").classList.remove("show");
            });
        });
    </script>
@endpush
