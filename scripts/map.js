function init() {
    const mapEl = document.getElementById('map');
    if (!mapEl) return;

    const rawData = mapEl.dataset.map;
    if (!rawData) return;

    let data;

    try {
        data = JSON.parse(rawData);
    } catch (e) {
        console.error('Invalid map data', e);
        return;
    }

    const preview = data.preview ?? false;

    const map = createMap(mapEl, data);
    const icons = createIcons(preview);
    const markers = createMarkers(data.markers || [], icons, data.interactive);

    map.addLayer(markers);

    if (!preview) {
        fitMapToMarkers(map, markers, data);
    }
}

init();

/* Create MAP */
function createMap(el, data) {
    const map = L.map(el, {
        center: data.center,
        zoom: data.zoom
    });

    if (!data?.preview) {
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            minZoom: 0,
            maxZoom: 20,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        }).addTo(map);

        L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            minZoom: 0,
            maxZoom: 20,
            attribution: 'Esri',
            pane: 'overlayPane'
        }).addTo(map);
    } else {
        L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
            minZoom: 0,
            maxZoom: 20,
            ext: 'png',
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="https://www.hotosm.org/" target="_blank">Humanitarian OpenStreetMap Team</a>, <a href="https://openstreetmap.fr/" target="_blank">OpenStreetMap Fr</a>'
        }).addTo(map);
    }


    if (!data?.preview) {
        L.control.locate({
            showPopup: false,
            strings: {title: 'Localisez-moi'},
            locateOptions: {maxZoom: 14}
        }).addTo(map);
    }

    return map;
}

/* Create ICONS */
function createIcons(preview) {
    const options = !preview ? {
        iconSize: [38, 48],
        iconAnchor: [19, 43],
        popupAnchor: [0, -40]
    } : {
        iconSize: [70, 80],
        iconAnchor: [35, 75],
        popupAnchor: [0, -40]
    };

    return {
        primary: L.icon({
            iconUrl: `${THEME.assets}/images/svg/pin_primary.svg`,
            ...options
        }),
        secondary: L.icon({
            iconUrl: `${THEME.assets}/images/svg/pin_secondary.svg`,
            ...options
        }),
    };
}

/* Create MARKERS */
function createMarkers(markersData = [], icons, interactive) {
    const cluster = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: true,
        zoomToBoundsOnClick: true,
    });

    markersData.forEach(marker => {
        if (!marker.lat || !marker.lng) return;

        const icon = icons[marker.type] || icons.primary;

        const m = L.marker([marker.lat, marker.lng], { icon, interactive });

        if (marker.popup) {
            m.bindPopup(marker.popup, {
                auto: true,
                autoPanPadding: [80, 80],
                closeButton: false
            });
        }

        cluster.addLayer(m);
    });

    return cluster;
}

/* Auto ZOOM */
function fitMapToMarkers(map, markers, data) {
    const layers = markers.getLayers();

    if (!layers.length) {
        map.setView(data.center, data.zoom);
        return;
    }

    if (layers.length === 1) {
        map.setView(layers[0].getLatLng(), 12);
        return;
    }

    const bounds = L.latLngBounds(
        layers.map(layer => layer.getLatLng())
    );

    map.fitBounds(bounds, {
        padding: [50, 50],
        maxZoom: 14,
        animate: true
    });
}
