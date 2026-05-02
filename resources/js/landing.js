                        // Navbar scroll effect
                        window.addEventListener('scroll', () => {
                            document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 20);
                        });

                        // Initialize map
                        const map = L.map('map', { attributionControl: false }).setView([-6.2088, 106.8456], 13);

                        const darkTiles = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
                        const lightTiles = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

                        let activeTileLayer = L.tileLayer((localStorage.getItem('theme') || 'light') === 'light' ? lightTiles : darkTiles, {
                            maxZoom: 19
                        }).addTo(map);

                        window.addEventListener('theme-changed', (e) => {
                            map.removeLayer(activeTileLayer);
                            activeTileLayer = L.tileLayer(e.detail.theme === 'light' ? lightTiles : darkTiles, {
                                maxZoom: 19
                            }).addTo(map);
                        });

                        function formatRupiah(num) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                        }

                        // =============================================
                        // USER LOCATION (for distance estimation)
                        // =============================================
                        let userLatLng = null;
                        let userMarker = null;

                        const userIcon = L.divIcon({
                            className: 'user-location-marker',
                            html: '<div class="user-marker-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>',
                            iconSize: [36, 36],
                            iconAnchor: [18, 18]
                        });

                        if ('geolocation' in navigator) {
                            navigator.geolocation.getCurrentPosition(
                                pos => {
                                    userLatLng = L.latLng(pos.coords.latitude, pos.coords.longitude);
                                    // Place user marker on map
                                    userMarker = L.marker([pos.coords.latitude, pos.coords.longitude], { icon: userIcon, zIndexOffset: 1000 })
                                        .addTo(map)
                                        .bindTooltip('Lokasi Anda', {
                                            permanent: true, direction: 'bottom', offset: [0, 6],
                                            className: 'user-tooltip'
                                        });
                                },
                                () => { console.log('Geolocation denied or unavailable'); },
                                { enableHighAccuracy: false, timeout: 8000 }
                            );
                        }

                        function getDistanceText(cartLat, cartLng) {
                            if (!userLatLng) return null;
                            const cartPos = L.latLng(cartLat, cartLng);
                            const meters = userLatLng.distanceTo(cartPos);
                            if (meters < 1000) return Math.round(meters) + ' m';
                            return (meters / 1000).toFixed(1) + ' km';
                        }

                        function getDistanceMeters(cartLat, cartLng) {
                            if (!userLatLng) return null;
                            const cartPos = L.latLng(cartLat, cartLng);
                            return userLatLng.distanceTo(cartPos);
                        }

                        function estimateETA(meters) {
                            if (meters === null) return '';
                            if (meters < 500) return 'Saya sudah di lokasi';
                            if (meters < 1000) return '± 5 menit';
                            if (meters < 2000) return '± 10 menit';
                            if (meters < 5000) return '± 15 menit';
                            if (meters < 10000) return '± 30 menit';
                            return 'Lebih dari 30 menit';
                        }

                        // =============================================
                        // ORDER PANEL LOGIC
                        // =============================================
                        let currentOrderCart = null;
                        let orderItems = []; // { name, price, stock, qty }
                        const disabledMenuItems = ['cold brew']; // Menu yang tidak tersedia sementara

                        const orderPanel = document.getElementById('order-panel');
                        const orderBackdrop = document.getElementById('order-backdrop');
                        const orderSheet = document.getElementById('order-sheet');
                        const orderCartName = document.getElementById('order-cart-name');
                        const orderRiderName = document.getElementById('order-rider-name');
                        const orderDistance = document.getElementById('order-distance');
                        const orderMenuList = document.getElementById('order-menu-list');
                        const orderNotes = document.getElementById('order-notes');
                        const orderTotalPrice = document.getElementById('order-total-price');
                        const orderItemCount = document.getElementById('order-item-count');
                        const orderWaBtn = document.getElementById('order-wa-btn');
                        const orderNavBtn = document.getElementById('order-nav-btn');
                        const orderLocBtn = document.getElementById('order-loc-btn');
                        const orderCloseBtn = document.getElementById('order-close-btn');

                        function openOrderPanel(cart) {
                            currentOrderCart = cart;
                            orderItems = cart.menu.map(m => ({ ...m, qty: 0 }));

                            orderCartName.textContent = cart.name;
                            orderRiderName.textContent = 'Rider: ' + cart.rider;
                            orderNotes.value = '';

                            // Distance estimation
                            const dist = getDistanceText(cart.latitude, cart.longitude);
                            if (dist) {
                                orderDistance.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-2px;margin-right:4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> ± ${dist} dari lokasi Anda`;
                                orderDistance.style.display = 'flex';
                            } else {
                                orderDistance.style.display = 'none';
                            }

                            // Navigation button
                            orderNavBtn.href = `https://www.google.com/maps/dir/?api=1&destination=${cart.latitude},${cart.longitude}`;

                            // ETA dan Navigasi
                            // Jarak & ETA dihitung di background saat generate WA Link

                            // Location share button
                            if (userLatLng && currentOrderCart?.whatsapp) {
                                orderLocBtn.classList.remove('disabled');
                                orderLocBtn.href = generateLocationShareLink();
                            } else {
                                orderLocBtn.classList.add('disabled');
                                orderLocBtn.href = '#';
                            }

                            renderOrderMenu();
                            updateOrderTotal();

                            orderPanel.classList.add('open');
                            document.body.style.overflow = 'hidden';
                        }

                        function closeOrderPanel() {
                            orderPanel.classList.remove('open');
                            document.body.style.overflow = '';
                            currentOrderCart = null;
                        }

                        orderCloseBtn.addEventListener('click', closeOrderPanel);
                        orderBackdrop.addEventListener('click', closeOrderPanel);

                        function renderOrderMenu() {
                            if (orderItems.length === 0) {
                                orderMenuList.innerHTML = '<div class="order-empty">Belum ada menu tersedia di gerobak ini.</div>';
                                return;
                            }
                            orderMenuList.innerHTML = orderItems.map((item, i) => {
                                const isDisabled = disabledMenuItems.includes(item.name.toLowerCase());
                                return `
                                    <div class="order-menu-row${isDisabled ? ' order-menu-disabled' : ''}">
                                        <div class="order-menu-info">
                                            <span class="order-menu-name">${item.name}</span>
                                            <span class="order-menu-price">${formatRupiah(item.price)}</span>
                                        </div>
                                        <div class="order-qty-controls">
                                            <button class="order-qty-btn" onclick="changeQty(${i}, -1)" ${item.qty <= 0 || isDisabled ? 'disabled' : ''}>−</button>
                                            <span class="order-qty-value">${item.qty}</span>
                                            <button class="order-qty-btn" onclick="changeQty(${i}, 1)" ${isDisabled ? 'disabled' : ''}>+</button>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        }

                        function changeQty(index, delta) {
                            const item = orderItems[index];
                            if (disabledMenuItems.includes(item.name.toLowerCase())) return;
                            const newQty = item.qty + delta;
                            if (newQty < 0) return;
                            item.qty = newQty;
                            renderOrderMenu();
                            updateOrderTotal();
                        }

                        function updateOrderTotal() {
                            const selected = orderItems.filter(i => i.qty > 0);
                            const total = selected.reduce((sum, i) => sum + (i.price * i.qty), 0);
                            const count = selected.reduce((sum, i) => sum + i.qty, 0);

                            orderTotalPrice.textContent = formatRupiah(total);
                            orderItemCount.textContent = count + ' item dipilih';

                            if (count > 0 && currentOrderCart?.whatsapp) {
                                orderWaBtn.classList.remove('disabled');
                                orderWaBtn.href = generateWhatsAppLink();
                            } else {
                                orderWaBtn.classList.add('disabled');
                                orderWaBtn.href = '#';
                            }
                        }

        function generateOrderText() {
            if (!currentOrderCart) return '';
            const selected = orderItems.filter(i => i.qty > 0);
            if (selected.length === 0) return '';

            const total = selected.reduce((sum, i) => sum + (i.price * i.qty), 0);
            const notes = orderNotes.value.trim();

            const meters = getDistanceMeters(currentOrderCart.latitude, currentOrderCart.longitude);
            const eta = estimateETA(meters);

            let msg = `Halo Tether Brew *${currentOrderCart.name}*,\nSaya mau pesan:\n\n`;
            selected.forEach(item => {
                const sub = item.price * item.qty;
                msg += ` ${item.name} x${item.qty} = ${formatRupiah(sub)}\n`;
            });
            msg += `\n *Total: ${formatRupiah(total)}*`;
            if (eta) msg += `\n *Estimasi saya sampai: ${eta}*`;
            if (notes) msg += `\n\n Catatan: ${notes}`;
            if (userLatLng) {
                msg += `\n\n Lokasi saya: https://maps.google.com/?q=${userLatLng.lat},${userLatLng.lng}`;
            }
            msg += `\n\nMohon konfirmasi ketersediaannya dan jangan kemana-mana dulu ya! `;
            return msg;
        }

        function generateChatDraftText() {
            if (!currentOrderCart) return '';
            const selected = orderItems.filter(i => i.qty > 0);
            if (selected.length === 0) return '';

            const total = selected.reduce((sum, i) => sum + (i.price * i.qty), 0);
            const notes = orderNotes.value.trim();

            let msg = `Saya mau pesan: `;
            const items = selected.map(item => `${item.name} (x${item.qty})`);
            msg += items.join(', ');
            msg += `. Total: ${formatRupiah(total)}.`;
            
            if (notes) msg += ` Catatan: ${notes}`;
            return msg;
        }

        function generateWhatsAppLink() {
            if (!currentOrderCart) return '#';
            const phone = currentOrderCart.whatsapp;
            const msg = generateOrderText();
            return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
        }

        function generateLocationShareLink() {
            if (!currentOrderCart || !userLatLng) return '#';
            const phone = currentOrderCart.whatsapp;
            const dist = getDistanceText(currentOrderCart.latitude, currentOrderCart.longitude);
            const mapsLink = `https://maps.google.com/?q=${userLatLng.lat},${userLatLng.lng}`;

            let msg = `Halo Tether Brew *${currentOrderCart.name}*! 👋\n\n`;
            msg += `Saya tertarik untuk pesan kopi. Ini lokasi saya:\n`;
            msg += ` ${mapsLink}\n\n`;
            if (dist) msg += `Jarak saya ke gerobak Anda: ± ${dist}\n\n`;
            msg += `Apakah memungkinkan untuk mendekat ke lokasi saya? Terima kasih! 🙏`;

            return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
        }

        // Update WA link on notes change
        orderNotes.addEventListener('input', updateOrderTotal);

        // =============================================
        // MENU & MAP DATA
        // =============================================
        let allMenuItems = [];
        let allCartsData = [];

        fetch('/api/carts-map')
            .then(r => r.json())
            .then(carts => {
                allCartsData = carts;

                // Stats
                document.getElementById('stat-carts').textContent = carts.length;
                let menuSet = new Map();
                carts.forEach(c => c.menu.forEach(m => {
                    if (!menuSet.has(m.name)) menuSet.set(m.name, m);
                }));
                document.getElementById('stat-menus').textContent = menuSet.size;
                document.getElementById('cart-count-label').textContent = carts.length + ' gerobak aktif';

                allMenuItems = Array.from(menuSet.values());
                renderMenu('semua');

                document.querySelectorAll('.menu-tab').forEach(tab => {
                    tab.addEventListener('click', () => {
                        const cat = tab.textContent.includes('Coffee') && !tab.textContent.includes('Non')
                            ? 'kopi' : tab.textContent.includes('Non') ? 'non-kopi' : 'semua';
                        renderMenu(cat);
                    });
                });

                const markers = {};
                const brewIcon = L.icon({
                    iconUrl: '/custom_icon_maps.png',
                    iconSize: [40, 40], 
                    iconAnchor: [20, 40], 
                    popupAnchor: [0, -40]
                });

                function renderCartsOnMap(carts, initial = false) {
                    const bounds = [];
                    carts.forEach(cart => {
                        const latlng = [cart.latitude, cart.longitude];
                        bounds.push(latlng);

                        // Build popup (info only, order via panel)
                        const distText = getDistanceText(cart.latitude, cart.longitude);
                        const popupContent = `
                            <div class="popup-title">${cart.name}</div>
                            <div class="popup-rider">${cart.rider}${distText ? ' · <svg class="icon-two-tone" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:2px; margin-left:4px; margin-top:-2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>' + distText : ''}</div>
                            <div class="popup-menu-title">${cart.menu.length} menu tersedia</div>
                            <div class="popup-updated">Diperbarui: ${cart.updated_at}</div>
                            <div class="popup-contact-wrapper">
                                <button class="popup-order-btn" onclick="openOrderPanel(window.__cartsData.find(c=>c.id===${cart.id}))"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.2em;margin-right:4px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg> Pesan dari Gerobak Ini</button>
                            </div>
                        `;

                        if (markers[cart.id]) {
                            markers[cart.id].setLatLng(latlng).setPopupContent(popupContent);
                            // Update distance tooltip
                            if (distText) {
                                markers[cart.id].unbindTooltip();
                                markers[cart.id].bindTooltip(distText, {
                                    permanent: true, direction: 'bottom', offset: [0, 4],
                                    className: 'distance-tooltip'
                                });
                            }
                        } else {
                            const m = L.marker(latlng, { icon: brewIcon })
                                .addTo(map)
                                .bindPopup(popupContent, { maxWidth: 280 });
                            if (distText) {
                                m.bindTooltip(distText, {
                                    permanent: true, direction: 'bottom', offset: [0, 4],
                                    className: 'distance-tooltip'
                                });
                            }
                            markers[cart.id] = m;
                        }
                    });

                    // Store globally for popup button access
                    window.__cartsData = carts;

                    if (initial && bounds.length > 0) map.fitBounds(bounds, { padding: [50, 50] });
                }

                // =============================================
                // GEROBAK PAGINATION
                // =============================================
                const CARTS_PER_PAGE = 5;
                let currentCartPage = 1;
                let lastSortedCarts = [];

                function renderCartCards(carts) {
                    const grid = document.getElementById('cart-grid');
                    const paginationEl = document.getElementById('cart-pagination');
                    document.getElementById('cart-count-label').textContent = carts.length + ' gerobak aktif · live';

                    if (carts.length === 0) {
                        grid.innerHTML = '<div class="gerobak-empty-state">Belum ada gerobak aktif saat ini.</div>';
                        paginationEl.innerHTML = '';
                        return;
                    }

                    let sortedCarts = [...carts];
                    if (typeof userLatLng !== 'undefined' && userLatLng) {
                        sortedCarts.sort((a, b) => {
                            const distA = getDistanceMeters(a.latitude, a.longitude);
                            const distB = getDistanceMeters(b.latitude, b.longitude);
                            return (distA === null ? Infinity : distA) - (distB === null ? Infinity : distB);
                        });
                    }
                    lastSortedCarts = sortedCarts;

                    const totalPages = Math.ceil(sortedCarts.length / CARTS_PER_PAGE);
                    if (currentCartPage > totalPages) currentCartPage = totalPages;
                    if (currentCartPage < 1) currentCartPage = 1;

                    const startIdx = (currentCartPage - 1) * CARTS_PER_PAGE;
                    const pageCarts = sortedCarts.slice(startIdx, startIdx + CARTS_PER_PAGE);

                    grid.innerHTML = pageCarts.map(cart => {
                        const distText = getDistanceText(cart.latitude, cart.longitude);
                        return `
                        <div class="gerobak-card" onclick="map.setView([${cart.latitude},${cart.longitude}],16); document.getElementById('maps').scrollIntoView({behavior:'smooth'});">
                            <div class="gerobak-card-top">
                                <div class="gerobak-card-name"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em; margin-right:4px;"><path d="M6 9l1.5 11.5A2 2 0 0 0 9.5 22h5a2 2 0 0 0 2-1.5L18 9" /><line x1="4" y1="9" x2="20" y2="9" /><path d="M5 9 A 7 5 0 0 1 19 9" /><line x1="12" y1="4" x2="14" y2="0" /></svg> ${cart.name}</div>
                                <div class="gerobak-badge">Aktif</div>
                            </div>
                            <div class="gerobak-card-rider"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/></svg>${cart.rider} · <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:2px; margin-left:4px; margin-top:-2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>${cart.updated_at}${distText ? ' · <svg class="icon-two-tone" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:2px; margin-left:4px; margin-top:-2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> ' + distText : ''}</div>
                            <div class="gerobak-card-actions" onclick="event.stopPropagation()">
                                <button class="btn-cart-wa" onclick="openOrderPanel(window.__cartsData.find(c=>c.id===${cart.id}))"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.2em; margin-right:4px;"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> Pesan</button>
                            </div>
                        </div>
                    `}).join('');

                    // Render pagination
                    renderCartPagination(totalPages);
                }

                function renderCartPagination(totalPages) {
                    const paginationEl = document.getElementById('cart-pagination');
                    if (totalPages <= 1) {
                        paginationEl.innerHTML = '';
                        return;
                    }

                    let html = '';

                    // Previous button
                    html += `<button class="pagination-btn pagination-prev ${currentCartPage === 1 ? 'disabled' : ''}" onclick="goToCartPage(${currentCartPage - 1})" ${currentCartPage === 1 ? 'disabled' : ''}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>`;

                    // Page numbers with ellipsis
                    const pages = getPaginationRange(currentCartPage, totalPages);
                    pages.forEach(p => {
                        if (p === '...') {
                            html += `<span class="pagination-ellipsis">…</span>`;
                        } else {
                            html += `<button class="pagination-btn pagination-num ${p === currentCartPage ? 'active' : ''}" onclick="goToCartPage(${p})">${p}</button>`;
                        }
                    });

                    // Next button
                    html += `<button class="pagination-btn pagination-next ${currentCartPage === totalPages ? 'disabled' : ''}" onclick="goToCartPage(${currentCartPage + 1})" ${currentCartPage === totalPages ? 'disabled' : ''}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>`;

                    // Page info
                    html += `<div class="pagination-info">${lastSortedCarts.length} gerobak</div>`;

                    paginationEl.innerHTML = html;
                }

                function getPaginationRange(current, total) {
                    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
                    const pages = [];
                    if (current <= 3) {
                        pages.push(1, 2, 3, 4, '...', total);
                    } else if (current >= total - 2) {
                        pages.push(1, '...', total - 3, total - 2, total - 1, total);
                    } else {
                        pages.push(1, '...', current - 1, current, current + 1, '...', total);
                    }
                    return pages;
                }

                window.goToCartPage = function(page) {
                    const totalPages = Math.ceil(lastSortedCarts.length / CARTS_PER_PAGE);
                    if (page < 1 || page > totalPages) return;
                    currentCartPage = page;
                    renderCartCards(allCartsData);
                    // Smooth scroll to gerobak section top
                    document.getElementById('gerobak').scrollIntoView({ behavior: 'smooth', block: 'start' });
                };

                // Initial load
                renderCartsOnMap(carts, true);
                renderCartCards(carts);

                // Periodic refresh (every 8 seconds)
                setInterval(() => {
                    fetch('/api/carts-map?_t=' + new Date().getTime(), { cache: 'no-store' })
                        .then(r => r.json())
                        .then(carts => {
                            allCartsData = carts;
                            renderCartsOnMap(carts);
                            renderCartCards(carts);
                        })
                        .catch(err => console.warn('Refresh failed:', err));
                }, 8000);
            })
            .catch(err => {
                console.error('Initial load failed:', err);
                document.getElementById('cart-count-label').textContent = 'Gagal memuat data';
            });

        function renderMenu(category) {
            const grid = document.getElementById('menu-grid');
            const items = category === 'semua' ? allMenuItems : allMenuItems.filter(m => m.category === category);

            if (items.length === 0) {
                grid.innerHTML = '<div class="empty-state-text">Tidak ada menu</div>';
                return;
            }


            grid.innerHTML = items.map(m => {
                const isDisabled = disabledMenuItems.includes(m.name.toLowerCase());
                return `
                <div class="menu-card${isDisabled ? ' menu-card-disabled' : ''}">
                    <div class="menu-card-top">
                        <div class="menu-card-bg-circle"></div>
                        <div class="menu-card-hero">
                            ${m.image ? '<img src="/storage/image/' + m.image + '" alt="' + m.name + '" loading="lazy">' : '<img src="/storage/image/kopi-tether-new.webp" alt="' + m.name + '" loading="lazy">'}
                        </div>
                        <svg class="menu-card-wave" viewBox="0 0 1000 100" preserveAspectRatio="none"><path  d="M0,50 C300,120 700,-20 1000,50 L1000,105 L0,105 Z"></path></svg>
                    </div>
                    <div class="menu-card-info">
                        <div class="menu-card-name">${m.name}</div>
                        <div class="menu-card-desc">${m.category === 'kopi' ? 'Minuman kopi spesial dengan racikan khas Tether Brew' : 'Minuman segar pilihan non-kopi yang cocok untuk bersantai'}</div>
                        <div class="menu-card-bottom">
                            <span class="menu-card-price">${formatRupiah(m.price)}</span>
                            <a href="#maps" class="menu-card-btn relative inline-flex active:translate-y-0.5 items-center justify-center overflow-hidden text-white bg-orange-900 rounded-xl group transition-all duration-1000">
                                <span class="absolute w-0 h-0 transition-all duration-1000 ease-out bg-green-600 rounded-full group-hover:w-36 group-hover:h-36"></span>
                                <span class="absolute bottom-0 left-0 h-full -ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-auto h-full opacity-100 object-stretch" viewBox="0 0 487 487">
                                        <path fill-opacity=".15" fill-rule="nonzero" fill="#FFF" d="M0 .3c67 2.1 134.1 4.3 186.3 37 52.2 32.7 89.6 95.8 112.8 150.6 23.2 54.8 32.3 101.4 61.2 149.9 28.9 48.4 77.7 98.8 126.4 149.2H0V.3z"></path>
                                    </svg>
                                </span>
                                <span class="absolute top-0 right-0 w-12 h-full -mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="object-cover w-full h-full" viewBox="0 0 487 487">
                                        <path fill-opacity=".15" fill-rule="nonzero" fill="#FFF" d="M487 486.7c-66.1-3.6-132.3-7.3-186.3-37s-95.9-85.3-126.2-137.2c-30.4-51.8-49.3-99.9-76.5-151.4C70.9 109.6 35.6 54.8.3 0H487v486.7z"></path>
                                    </svg>
                                </span>
                                <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-green-400/20"></span>
                                <span class="relative text-base font-extrabold tracking-wide">Pesan</span>
                            </a>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }


        // =============================================
        // BOTTOM NAV: Active state on scroll (mobile)
        // =============================================
        const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
        const sectionIds = ['home', 'maps', 'menu', 'about', 'contact'];

        function updateBottomNav() {
            let current = 'home';
            for (const id of sectionIds) {
                const section = document.getElementById(id);
                if (section) {
                    const rect = section.getBoundingClientRect();
                    if (rect.top <= window.innerHeight / 2) {
                        current = id;
                    }
                }
            }
            bottomNavItems.forEach(item => {
                item.classList.toggle('active', item.dataset.section === current);
            });
        }

        window.addEventListener('scroll', updateBottomNav, { passive: true });

        // Expose functions globally for inline onclick handlers
        window.openOrderPanel = openOrderPanel;
        window.changeQty = changeQty;

        // =============================================
        // DM CHAT SYSTEM (with polling fallback)
        // =============================================
        let currentConversation = null;
        let chatEchoInstance = null;
        let chatMessages = [];
        let chatPollingTimer = null;
        let isChatDrawerOpen = false;

        function openChat() {
            const customerId = document.body.dataset.customerId;
            if (!customerId) {
                openAuthModal();
                return;
            }
            startChat();
        }

        function openAuthModal() {
            const modal = document.getElementById('auth-modal');
            if (modal) modal.style.display = 'flex';
        }

        function closeAuthModal() {
            const modal = document.getElementById('auth-modal');
            if (modal) modal.style.display = 'none';
        }

        async function startChat() {
            if (!currentOrderCart || !currentOrderCart.rider_id) return;

            try {
                const res = await fetch('/chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        rider_id: currentOrderCart.rider_id,
                        cart_id: currentOrderCart.id,
                    }),
                });

                if (!res.ok) throw new Error('Failed to start chat');

                const data = await res.json();
                currentConversation = data.conversation;

                // Update drawer header
                const riderName = currentOrderCart.rider || 'Rider';
                document.getElementById('chat-rider-name').textContent = riderName;
                document.getElementById('chat-rider-avatar').textContent = riderName.charAt(0).toUpperCase();

                openChatDrawer();
                await loadChatMessages();

                // Start polling for new messages (reliable fallback)
                startChatPolling();

                // Also try WebSocket for instant delivery
                subscribeToChatChannel();

                // Pre-fill draft text
                const draft = generateChatDraftText();
                if (draft) {
                    const chatInput = document.getElementById('chat-input');
                    if (chatInput) {
                        chatInput.value = draft;
                        chatInput.dispatchEvent(new Event('input'));
                        chatInput.focus();
                    }
                }
            } catch (err) {
                console.error('Chat start failed:', err);
            }
        }

        async function openChatFromHistory(cartId, riderId, riderName) {
            closeAccountModal();
            try {
                const res = await fetch('/chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        rider_id: riderId,
                        cart_id: cartId,
                    }),
                });

                if (!res.ok) throw new Error('Failed to start chat from history');

                const data = await res.json();
                currentConversation = data.conversation;

                // Update drawer header
                document.getElementById('chat-rider-name').textContent = riderName || 'Rider';
                document.getElementById('chat-rider-avatar').textContent = (riderName || 'R').charAt(0).toUpperCase();

                openChatDrawer();
                await loadChatMessages();
                startChatPolling();
                subscribeToChatChannel();

            } catch (err) {
                console.error('Chat open from history failed:', err);
            }
        }

        function openChatDrawer() {
            const drawer = document.getElementById('chat-drawer');
            if (drawer) drawer.style.display = 'flex';
            isChatDrawerOpen = true;
        }

        function closeChat() {
            const drawer = document.getElementById('chat-drawer');
            if (drawer) drawer.style.display = 'none';
            isChatDrawerOpen = false;

            // Stop polling
            stopChatPolling();

            // Unsubscribe from channel
            if (chatEchoInstance && currentConversation) {
                chatEchoInstance.leave(`conversation.${currentConversation.id}`);
            }
        }

        async function loadChatMessages() {
            if (!currentConversation) return;

            const container = document.getElementById('chat-messages');
            container.innerHTML = '<div class="chat-empty">Memuat pesan...</div>';

            try {
                const res = await fetch(`/chat/${currentConversation.id}/messages?_t=${Date.now()}`, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                });
                const data = await res.json();
                chatMessages = (data.data || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                renderChatMessages();
                scrollChatToBottom();

                // Mark as read
                fetch(`/chat/${currentConversation.id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
            } catch (err) {
                container.innerHTML = '<div class="chat-empty">Gagal memuat pesan</div>';
            }
        }

        // Polling: fetch new messages every 3 seconds
        function startChatPolling() {
            stopChatPolling(); // clear any existing timer
            chatPollingTimer = setInterval(async () => {
                if (!currentConversation || !isChatDrawerOpen) {
                    stopChatPolling();
                    return;
                }
                try {
                    const res = await fetch(`/chat/${currentConversation.id}/messages?_t=${Date.now()}`, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store',
                    });
                    const data = await res.json();
                    const freshMessages = (data.data || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                    // Only update if there are new messages
                    if (freshMessages.length !== chatMessages.length) {
                        chatMessages = freshMessages;
                        renderChatMessages();
                        scrollChatToBottom();

                        // Mark as read
                        fetch(`/chat/${currentConversation.id}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        });
                    }
                } catch (err) {
                    // Silently ignore polling errors
                }
            }, 3000);
        }

        function stopChatPolling() {
            if (chatPollingTimer) {
                clearInterval(chatPollingTimer);
                chatPollingTimer = null;
            }
        }

        function renderChatMessages() {
            const container = document.getElementById('chat-messages');
            const customerId = parseInt(document.body.dataset.customerId);

            if (chatMessages.length === 0) {
                container.innerHTML = '<div class="chat-empty">Belum ada pesan. Mulai percakapan!</div>';
                return;
            }

            container.innerHTML = chatMessages.map(msg => {
                const isSent = msg.sender_id === customerId;
                const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                let contentHtml = '';
                if (msg.attachment_path) {
                    if (msg.attachment_type === 'image') {
                        contentHtml += `<img src="/storage/${msg.attachment_path}" alt="attachment" class="chat-attachment-img" onclick="window.open('/storage/${msg.attachment_path}', '_blank')">`;
                    } else if (msg.attachment_type === 'pdf') {
                        contentHtml += `<a href="/storage/${msg.attachment_path}" target="_blank" class="chat-attachment-pdf">📄 Lihat PDF</a>`;
                    }
                }
                if (msg.body) {
                    contentHtml += `<div>${escapeHtmlChat(msg.body)}</div>`;
                }

                return `<div class="chat-bubble ${isSent ? 'sent' : 'received'}">
                    ${contentHtml}
                    <div class="chat-bubble-time">${time}</div>
                </div>`;
            }).join('');
        }

        function scrollChatToBottom() {
            const container = document.getElementById('chat-messages');
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 50);
        }

        function escapeHtmlChat(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        async function sendChatMessage() {
            if (!currentConversation) return;

            const input = document.getElementById('chat-input');
            const body = input.value.trim();
            if (!body) return;

            input.value = '';

            try {
                const res = await fetch(`/chat/${currentConversation.id}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ body }),
                });

                if (!res.ok) throw new Error('Send failed');

                const data = await res.json();
                chatMessages.push(data.message);
                renderChatMessages();
                scrollChatToBottom();
            } catch (err) {
                input.value = body;
            }
        }

        async function sendChatAttachment() {
            if (!currentConversation) return;

            const fileInput = document.getElementById('chat-file-input');
            const file = fileInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('attachment', file);
            formData.append('body', ''); // optional text, leave empty

            try {
                const res = await fetch(`/chat/${currentConversation.id}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!res.ok) throw new Error('Upload failed');

                const data = await res.json();
                chatMessages.push(data.message);
                renderChatMessages();
                scrollChatToBottom();
            } catch (err) {
                console.error('Attachment send failed:', err);
            }

            fileInput.value = ''; // reset input
        }

        // WebSocket subscription (best-effort, polling is the reliable fallback)
        function subscribeToChatChannel() {
            if (!currentConversation) return;

            try {
                import('laravel-echo').then((EchoModule) => {
                    import('pusher-js').then((PusherModule) => {
                        const Pusher = PusherModule.default;
                        window.Pusher = Pusher;

                        if (!chatEchoInstance) {
                            const EchoClass = EchoModule.default;
                            chatEchoInstance = new EchoClass({
                                broadcaster: 'reverb',
                                key: import.meta.env.VITE_REVERB_APP_KEY,
                                wsHost: import.meta.env.VITE_REVERB_HOST,
                                wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
                                wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
                                forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
                                enabledTransports: ['ws', 'wss'],
                                authorizer: (channel, options) => {
                                    return {
                                        authorize: (socketId, callback) => {
                                            fetch('/broadcasting/auth', {
                                                method: 'POST',
                                                credentials: 'same-origin',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    socket_id: socketId,
                                                    channel_name: channel.name
                                                })
                                            })
                                            .then(response => {
                                                if (!response.ok) throw new Error('Auth failed');
                                                return response.json();
                                            })
                                            .then(data => callback(false, data))
                                            .catch(error => callback(true, error));
                                        }
                                    };
                                }
                            });
                        }

                        chatEchoInstance.private(`conversation.${currentConversation.id}`)
                            .listen('MessageSent', (e) => {
                                // Check for duplicate (polling might have already added it)
                                if (chatMessages.some(m => m.id === e.id)) return;

                                chatMessages.push({
                                    id: e.id,
                                    conversation_id: e.conversation_id,
                                    sender_id: e.sender_id,
                                    body: e.body,
                                    attachment_path: e.attachment_path,
                                    attachment_type: e.attachment_type,
                                    created_at: e.created_at,
                                    sender: { name: e.sender_name },
                                });
                                renderChatMessages();
                                scrollChatToBottom();
                            });
                    });
                }).catch(err => {
                    console.warn('Echo import failed, polling fallback is active:', err);
                });
            } catch (err) {
                console.warn('Echo not available, polling fallback is active:', err);
            }
        }

        // Enter to send in chat input
        const chatInput = document.getElementById('chat-input');
        if (chatInput) {
            chatInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendChatMessage();
                }
            });
        }

        // Expose chat functions globally
        window.openChat = openChat;
        window.closeChat = closeChat;
        window.sendChatMessage = sendChatMessage;
        window.openAuthModal = openAuthModal;
        window.closeAuthModal = closeAuthModal;
        window.openChatFromHistory = openChatFromHistory;
        window.sendChatAttachment = sendChatAttachment;
