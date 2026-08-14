(function () {
	"use strict";

	const root = document.getElementById("contact-stores-map");
	if (!root) return;

	const dataEl = document.getElementById("contact-stores-data");
	let stores = [];
	try {
		const raw = dataEl ? dataEl.textContent : root.dataset.stores || "[]";
		stores = JSON.parse(raw);
	} catch (e) {
		console.error("contact-map: nu pot citi locațiile", e);
		return;
	}
	if (!stores.length) return;

	stores = stores.map((s) => ({
		...s,
		lat: Number(s.lat),
		lng: Number(s.lng),
	}));

	const apiKey = (root.dataset.apiKey || "").trim();
	const chips = root.querySelectorAll("[data-store-id]");
	const canvas = root.querySelector(".contact-map__canvas");

	function mapsLink(store) {
		const q = store.address || store.lat + "," + store.lng;
		return (
			"https://www.google.com/maps/search/?api=1&query=" +
			encodeURIComponent(q)
		);
	}

	function popupHtml(store) {
		return (
			'<div class="contact-map__popup">' +
			"<strong>" +
			escapeHtml(store.name) +
			"</strong>" +
			(store.detail
				? '<br><span style="opacity:.85">' +
				  escapeHtml(store.detail) +
				  "</span>"
				: "") +
			'<br><a href="' +
			mapsLink(store) +
			'" target="_blank" rel="noopener noreferrer">Deschide în Google Maps</a>' +
			"</div>"
		);
	}

	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, "&amp;")
			.replace(/</g, "&lt;")
			.replace(/>/g, "&gt;")
			.replace(/"/g, "&quot;");
	}

	function setActiveChip(id) {
		chips.forEach((chip) => {
			chip.classList.toggle("is-active", (chip.dataset.storeId || "") === id);
		});
	}

	function chisinauStores() {
		return stores.filter((s) => s.city === "Chișinău");
	}

	let mapInstance = null;
	let markersById = {};
	let gmap = null;
	let gmarkers = [];

	function fitLeaflet(list) {
		if (!mapInstance || !list.length) return;
		if (list.length === 1) {
			mapInstance.setView([list[0].lat, list[0].lng], 16);
			return;
		}
		const bounds = L.latLngBounds(list.map((s) => [s.lat, s.lng]));
		mapInstance.fitBounds(bounds, { padding: [56, 56], maxZoom: 15 });
	}

	function fitGoogle(list) {
		if (!gmap || !window.google || !list.length) return;
		if (list.length === 1) {
			gmap.setCenter({ lat: list[0].lat, lng: list[0].lng });
			gmap.setZoom(16);
			return;
		}
		const bounds = new google.maps.LatLngBounds();
		list.forEach((s) => bounds.extend({ lat: s.lat, lng: s.lng }));
		gmap.fitBounds(bounds, { top: 56, right: 48, bottom: 48, left: 48 });
	}

	function selectStore(id) {
		setActiveChip(id);
		if (id === "") {
			const list = chisinauStores();
			fitLeaflet(list);
			fitGoogle(list);
			return;
		}
		const store = stores.find((s) => s.id === id);
		if (!store) return;
		if (mapInstance) {
			mapInstance.setView([store.lat, store.lng], 16, { animate: true });
			const m = markersById[id];
			if (m) m.openPopup();
		}
		if (gmap) {
			gmap.setCenter({ lat: store.lat, lng: store.lng });
			gmap.setZoom(16);
			const gm = gmarkers.find((x) => x._storeId === id);
			if (gm && window.google) {
				google.maps.event.trigger(gm, "click");
			}
		}
	}

	function bindChips() {
		chips.forEach((chip) => {
			chip.addEventListener("click", () => {
				selectStore(chip.dataset.storeId || "");
			});
		});
	}

	function initLeaflet() {
		if (!canvas || typeof L === "undefined") return false;

		const map = L.map(canvas, { scrollWheelZoom: false });
		mapInstance = map;

		L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
			attribution:
				'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
			maxZoom: 19,
		}).addTo(map);

		const pin = L.divIcon({
			className: "contact-map__pin",
			html: "<span aria-hidden=\"true\"></span>",
			iconSize: [28, 28],
			iconAnchor: [14, 28],
			popupAnchor: [0, -28],
		});

		stores.forEach((store) => {
			const marker = L.marker([store.lat, store.lng], { icon: pin }).addTo(map);
			marker.bindPopup(popupHtml(store));
			markersById[store.id] = marker;
		});

		bindChips();
		fitLeaflet(chisinauStores());
		setActiveChip("");
		window.setTimeout(() => map.invalidateSize(), 150);
		return true;
	}

	function initGoogle() {
		if (!apiKey || !canvas) return;

		window.initContactStoresMap = function () {
			gmap = new google.maps.Map(canvas, {
				zoom: 12,
				center: { lat: 47.025, lng: 28.835 },
				mapTypeControl: false,
				streetViewControl: false,
				fullscreenControl: true,
			});

			const info = new google.maps.InfoWindow();

			stores.forEach((store) => {
				const pos = { lat: store.lat, lng: store.lng };
				const marker = new google.maps.Marker({
					position: pos,
					map: gmap,
					title: store.name,
				});
				marker._storeId = store.id;
				gmarkers.push(marker);
				marker.addListener("click", () => {
					info.setContent(popupHtml(store));
					info.open(gmap, marker);
				});
			});

			bindChips();
			selectStore("");
		};

		const script = document.createElement("script");
		script.src =
			"https://maps.googleapis.com/maps/api/js?key=" +
			encodeURIComponent(apiKey) +
			"&callback=initContactStoresMap&language=ro";
		script.async = true;
		script.defer = true;
		document.head.appendChild(script);
	}

	function showMapPlaceholder() {
		if (!canvas) return;
		canvas.innerHTML =
			'<p class="contact-map__consent-placeholder">Harta interactivă necesită cookie-uri funcționale. ' +
			'<button type="button" class="contact-map__consent-btn" data-cookie-settings>Acceptă în setări cookies</button></p>';
	}

	function loadLeafletAssets(callback) {
		if (window.L) {
			callback();
			return;
		}
		const css = document.createElement("link");
		css.rel = "stylesheet";
		css.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
		css.integrity = "sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=";
		css.crossOrigin = "anonymous";
		document.head.appendChild(css);

		const script = document.createElement("script");
		script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
		script.integrity = "sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=";
		script.crossOrigin = "anonymous";
		script.onload = callback;
		document.head.appendChild(script);
	}

	function startMap() {
		if (canvas) {
			canvas.innerHTML = "";
		}
		if (apiKey) {
			initGoogle();
			return;
		}
		loadLeafletAssets(() => {
			const started = initLeaflet();
			if (!started) {
				console.error("contact-map: Leaflet nu este încărcat");
			}
		});
	}

	function whenMapAllowed(fn) {
		if (typeof window.hasFunctionalConsent === "function" && window.hasFunctionalConsent()) {
			fn();
			return;
		}
		showMapPlaceholder();
		window.addEventListener(
			"cookieconsent",
			(e) => {
				if (e.detail && e.detail.level === "all") {
					fn();
				}
			},
			{ once: true }
		);
	}

	whenMapAllowed(startMap);
})();
