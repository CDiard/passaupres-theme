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

    const map = createMap(mapEl, data);
    const icons = createIcons();
    const markers = createMarkers(data.markers || [], icons, data.interactive);

    map.addLayer(markers);

    fitMapToMarkers(map, markers, data);
}

init();

/* Create MAP */
function createMap(el, data) {
    const map = L.map(el, {
        center: data.center,
        zoom: data.zoom
    });

    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_satellite/{z}/{x}/{y}{r}.{ext}', {
        minZoom: 0,
        maxZoom: 20,
        ext: 'jpg',
        attribution: '&copy; CNES, Distribution Airbus DS, © Airbus DS, © PlanetObserver (Contains Copernicus Data) | &copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.control.locate({
        showPopup: false,
        strings: {title: 'Localisez-moi'},
        locateOptions: {maxZoom: 14}
    }).addTo(map);

    return map;
}

/* Create ICONS */
function createIcons() {
    return {
        primary: L.icon({
            iconUrl: `${THEME.assets}/images/svg/pin_primary.svg`,
            iconSize: [38, 48],
            iconAnchor: [19, 43],
            popupAnchor: [0, -40],
        }),
        secondary: L.icon({
            iconUrl: `${THEME.assets}/images/svg/pin_secondary.svg`,
            iconSize: [38, 48],
            iconAnchor: [19, 43],
            popupAnchor: [0, -40],
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
