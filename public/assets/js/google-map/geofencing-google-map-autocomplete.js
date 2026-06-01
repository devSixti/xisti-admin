let map, marker, infowindow, geocoder, drawingManager, polygon;
let sessionToken = null;

$(document).ready(function () {
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});

async function initMap() {
    const { Map } = await google.maps.importLibrary("maps");
    const { Marker } = await google.maps.importLibrary("marker");
    const { DrawingManager } = await google.maps.importLibrary("drawing");

    const center = new google.maps.LatLng(lati, longi);

    map = new Map(document.getElementById("map"), {
        center: center,
        zoom: 10,
    });

    geocoder = new google.maps.Geocoder();
    infowindow = new google.maps.InfoWindow();

    marker = new Marker({
        map,
        // position: center,
        // draggable: false,
        // visible: false
    });

    marker.addListener("dragend", () => {
        const pos = marker.getPosition();
        geocoder.geocode({ location: pos }, (results, status) => {
            if (status === "OK" && results[0]) {
                bindDataToForm(results[0].formatted_address, pos.lat(), pos.lng());
                infowindow.setContent(results[0].formatted_address);
                infowindow.open(map, marker);
            }
        });
    });

    drawingManager = new DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [google.maps.drawing.OverlayType.POLYGON],
        },
        polygonOptions: {
            strokeColor: "#000",
            strokeOpacity: 0.8,
            strokeWeight: 5,
            fillColor: "#8e8585",
            fillOpacity: 0.35,
            editable: true,
            draggable: true,
        },
    });

    drawingManager.setMap(map);

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
        if (polygon) polygon.setMap(null);
        polygon = event.overlay;
        updatePolygonCoordinates(polygon);
        attachPolygonListeners(polygon);
        attachPolygonClickHandler(polygon); // ✅ Added
    });

    // Restore existing polygon if editing
    const latVals = document.getElementById("lat").value;
    const lngVals = document.getElementById("lang").value;

    if (latVals && lngVals) {
        const latArr = latVals.split(",").map(Number);
        const lngArr = lngVals.split(",").map(Number);

        if (latArr.length === lngArr.length && latArr.length > 2) {
            const polygonCoords = latArr.map((lat, i) => ({ lat, lng: lngArr[i] }));

            polygon = new google.maps.Polygon({
                paths: polygonCoords,
                strokeColor: "#000",
                strokeOpacity: 0.8,
                strokeWeight: 5,
                fillColor: "#8e8585",
                fillOpacity: 0.35,
                editable: true,
                draggable: true,
            });

            polygon.setMap(map);
            attachPolygonListeners(polygon);
            attachPolygonClickHandler(polygon); // ✅ Added
            updatePolygonCoordinates(polygon);

            const bounds = new google.maps.LatLngBounds();
            polygon.getPath().forEach(latlng => bounds.extend(latlng));
            map.fitBounds(bounds);

            // ✅ Center marker at polygon center
            const polyCenter = bounds.getCenter();
            marker.setPosition(polyCenter);
            marker.setVisible(false);

            // ✅ Optional: reverse geocode center
            geocoder.geocode({ location: polyCenter }, (results, status) => {
                if (status === "OK" && results[0]) {
                    bindDataToForm(results[0].formatted_address, polyCenter.lat(), polyCenter.lng());
                    // infowindow.setContent(results[0].formatted_address);
                    // infowindow.open(map, marker);
                }
            });
        }
    }

    document.getElementById("reset").addEventListener("click", () => {
        if (polygon) polygon.setMap(null);
        polygon = null;
        drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
        $("#area_name, #lat, #lang").val('');
        $("#status").val('1');
        $("#searchInput").val('');
        document.getElementById("suggestions").innerHTML = "";
        infowindow.close();
        const originalCenter = new google.maps.LatLng(lati, longi);
        map.setCenter(originalCenter);
        marker.setPosition(originalCenter);
        marker.setVisible(false);
        map.setZoom(10);
    });

    const debouncedSuggestions = debounce(fetchSuggestions, 1000);
    document.getElementById("searchInput").addEventListener("input", debouncedSuggestions);
}

function attachPolygonClickHandler(polygonInstance) {
    google.maps.event.addListener(polygonInstance, 'click', function (event) {
        const latlng = event.latLng;
        marker.setPosition(latlng);
        marker.setVisible(false);

        geocoder.geocode({ location: latlng }, (results, status) => {
            if (status === "OK" && results[0]) {
                bindDataToForm(results[0].formatted_address, latlng.lat(), latlng.lng());
                infowindow.setContent(results[0].formatted_address);
                infowindow.open(map, marker);
            }
        });
    });
}

function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

async function fetchSuggestions(e) {
    const input = e.target.value.trim();
    const suggestionsDiv = document.getElementById("suggestions");

    if (!sessionToken) sessionToken = generateUUID();

    if (input.length < 2) {
        suggestionsDiv.innerHTML = "";
        return;
    }

    const payload = {
        input,
        languageCode: "en",
        sessionToken,
        locationBias: {
            circle: {
                center: {
                    latitude: lati,
                    longitude: longi,
                },
                radius: 50000,
            },
        },
    };

    try {
        const res = await fetch("https://places.googleapis.com/v1/places:autocomplete", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Goog-Api-Key": apiKey,
                "X-Goog-FieldMask": "suggestions.placePrediction.placeId,suggestions.placePrediction.structuredFormat,suggestions.placePrediction.text"             // Get all fields in response
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();
        suggestionsDiv.innerHTML = "";

        if (res.ok && data.suggestions) {
            data.suggestions.forEach(suggestion => {
                const div = document.createElement("div");
                div.className = "suggestion-item";

                // Apply styles to the suggestion container
                Object.assign(div.style, {
                    display: "flex",
                    alignItems: "center",
                    padding: "8px",
                    cursor: "pointer",
                    borderBottom: "1px solid #eee",
                    backgroundColor: "#fff",
                    transition: "background-color 0.2s"
                });

                // Marker icon
                const icon = document.createElement("span");
                icon.innerHTML = '<i class="fas fa-map-marker-alt"></i>';
                Object.assign(icon.style, {
                    marginRight: "8px",
                    fontSize: "16px",
                    color: "#555"
                });

                // Place text
                const text = document.createElement("span");
                text.innerText = suggestion.placePrediction.text.text;
                text.style.flex = "1";

                // Assemble
                div.appendChild(icon);
                div.appendChild(text);

                // Hover effects
                div.addEventListener("mouseover", () => {
                    div.style.backgroundColor = "#f0f0f0";
                });
                div.addEventListener("mouseout", () => {
                    div.style.backgroundColor = "#fff";
                });

                // On click
                div.addEventListener("click", () => {
                    fetchPlaceDetails(suggestion.placePrediction.placeId);
                    sessionToken = generateUUID(); // Reset session token
                    suggestionsDiv.innerHTML = ""; // Clear suggestions
                });

                suggestionsDiv.appendChild(div);
            });
        } else {
            const noRes = document.createElement("div");
            noRes.innerText = "No suggestions found";
            Object.assign(noRes.style, {
                padding: "8px",
                backgroundColor: "#ffeaea",
                color: "#a94442",
                borderBottom: "1px solid #f5c2c2",
                fontSize: "14px",
                textAlign: "left",
                textIndent: "20px",
                cursor: "default",
                borderRadius: "6px",
                marginBottom: "6px"
            });
            suggestionsDiv.appendChild(noRes);
        }
    } catch (err) {
        console.error("Autocomplete error:", err);
        suggestionsDiv.innerHTML = "";
    }
}

async function fetchPlaceDetails(placeId) {
    try {
        const res = await fetch(`https://places.googleapis.com/v1/places/${placeId}?fields=location,displayName,formattedAddress&key=${apiKey}`, {
            headers: {
                "X-Goog-Api-Key": apiKey,
            }
        });
        const data = await res.json();
        if (data.location) {
            const lat = data.location.latitude;
            const lng = data.location.longitude;
            const address = data.formattedAddress || data.displayName.text;
            const latlng = new google.maps.LatLng(lat, lng);

            map.setCenter(latlng);
            // map.setZoom(17); // Zoom in close to selected place
            marker.setPosition(latlng);
            marker.setVisible(false);
            bindDataToForm(address, null, null);
            // infowindow.setContent(address);
            // infowindow.open(map, marker);
        }
    } catch (err) {
        console.error("Place details fetch error:", err);
    }
}

function updatePolygonCoordinates(polygonInstance) {
    const path = polygonInstance.getPath().getArray();
    const latList = [], lngList = [];
    path.forEach(latlng => {
        latList.push(latlng.lat());
        lngList.push(latlng.lng());
    });
    $("#lat").val(latList.join(","));
    $("#lang").val(lngList.join(","));
}

function attachPolygonListeners(polygonInstance) {
    const path = polygonInstance.getPath();
    google.maps.event.clearListeners(path, 'insert_at');
    google.maps.event.clearListeners(path, 'remove_at');
    google.maps.event.clearListeners(path, 'set_at');

    ['insert_at', 'remove_at', 'set_at'].forEach(eventName => {
        google.maps.event.addListener(path, eventName, () => {
            updatePolygonCoordinates(polygonInstance);
        });
    });
}

function bindDataToForm(address, lat, lng) {
    $("#getaddress").val(address);
    $("#searchInput").val(address);
    // $("#lat").val(lat);
    // $("#lang").val(lng);
}

function debounce(func, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

document.addEventListener('click', function (event) {
    const searchInput = document.getElementById("searchInput");
    const suggestionsDiv = document.getElementById("suggestions");
    const clickedOutsideInput = !searchInput.contains(event.target);
    const clickedOutsideSuggestions = !suggestionsDiv.contains(event.target);

    if (clickedOutsideInput && clickedOutsideSuggestions) {
        suggestionsDiv.innerHTML = '';
    }
});
