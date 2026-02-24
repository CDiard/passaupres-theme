function init() {
    const mapEl = document.getElementById('map');
    if (!mapEl) return;

    const rawData = mapEl.dataset.map;
    if (!rawData) return;

    let data;

    try {
        data = JSON.parse(rawData);
        console.log(rawData)
    } catch (e) {
        console.error('Invalid map data', e);
        return;
    }

    const map = createMap(mapEl, data);
    const icons = createIcons();
    const markers = createMarkers(data.markers || [], icons);

    map.addLayer(markers);
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
function createMarkers(markersData = [], icons) {
    const cluster = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: true,
        zoomToBoundsOnClick: true,
    });

    markersData.forEach(marker => {
        if (!marker.lat || !marker.lng) return;

        const icon = icons[marker.type] || icons.primary;

        const m = L.marker([marker.lat, marker.lng], { icon });

        if (marker.popup) {
            m.bindPopup(marker.popup);
        }

        cluster.addLayer(m);
    });

    return cluster;
}
