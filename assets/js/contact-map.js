(function () {
	"use strict";

	const root = document.getElementById("contact-stores-map");
	if (!root) return;

	let stores = [];
	try {
		stores = JSON.parse(root.dataset.stores || "[]");
	} catch {
		return;
	}
	if (!stores.length) return;

	const apiKey = (root.dataset.apiKey || "").trim();
	const iframe = root.querySelector(".contact-map__embed");
	const chips = root.querySelectorAll("[data-store-id]");

	function embedUrl(lat, lng, zoom) {
		return (
			"https://maps.google.com/maps?q=" +
			encodeURIComponent(lat + "," + lng) +
			"&hl=ro&z=" +
			zoom +
			"&output=embed"
		);
	}

	function mapsLink(store) {
		const q = store.address || store.name;
		return (
			"https://www.google.com/maps/search/?api=1&query=" +
			encodeURIComponent(q)
		);
	}

	function setActiveChip(id) {
		chips.forEach((chip) => {
			chip.classList.toggle("is-active", chip.dataset.storeId === id);
		});
	}

	function showEmbed(store, zoom) {
		if (!iframe || !store) return;
		iframe.src = embedUrl(store.lat, store.lng, zoom || 16);
		iframe.title = store.name + " — " + (store.detail || store.address);
	}

	function initEmbedMode() {
		const chisinau = stores.filter((s) => s.city === "Chișinău");
		const center = chisinau[0] || stores[0];
		showEmbed(center, 12);
		setActiveChip("");

		chips.forEach((chip) => {
			chip.addEventListener("click", () => {
				const id = chip.dataset.storeId || "";
				if (id === "") {
					const chisinau = stores.filter((s) => s.city === "Chișinău");
					const c = chisinau[0] || stores[0];
					showEmbed(c, 12);
					setActiveChip("");
					return;
				}
				const store = stores.find((s) => s.id === id);
				if (!store) return;
				showEmbed(store, 16);
				setActiveChip(id);
			});
		});
	}

	function initJsApi() {
		const script = document.createElement("script");
		script.src =
			"https://maps.googleapis.com/maps/api/js?key=" +
			encodeURIComponent(apiKey) +
			"&callback=initContactStoresMap&language=ro";
		script.async = true;
		script.defer = true;
		window.initContactStoresMap = function () {
			const panel = root.querySelector(".contact-map__panel");
			if (panel) panel.remove();
			if (iframe) iframe.remove();

			const mapEl = document.createElement("div");
			mapEl.id = "contact-gmap-canvas";
			mapEl.className = "contact-map__canvas";
			root.appendChild(mapEl);

			const bounds = new google.maps.LatLngBounds();
			const map = new google.maps.Map(mapEl, {
				zoom: 12,
				center: { lat: 47.028, lng: 28.86 },
				mapTypeControl: false,
				streetViewControl: false,
				fullscreenControl: true,
				styles: [
					{
						featureType: "poi",
						elementType: "labels",
						stylers: [{ visibility: "off" }],
					},
				],
			});

			const info = new google.maps.InfoWindow();

			stores.forEach((store) => {
				const pos = { lat: store.lat, lng: store.lng };
				bounds.extend(pos);
				const marker = new google.maps.Marker({
					position: pos,
					map,
					title: store.name,
				});
				const html =
					'<div class="contact-map__infowin">' +
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
					"</div>";
				marker.addListener("click", () => {
					info.setContent(html);
					info.open(map, marker);
				});
			});

			map.fitBounds(bounds, { top: 48, right: 48, bottom: 48, left: 48 });
			if (stores.length === 1) {
				map.setZoom(15);
			}
		};
		document.head.appendChild(script);
	}

	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, "&amp;")
			.replace(/</g, "&lt;")
			.replace(/>/g, "&gt;")
			.replace(/"/g, "&quot;");
	}

	if (apiKey) {
		initJsApi();
	} else {
		initEmbedMode();
	}
})();
