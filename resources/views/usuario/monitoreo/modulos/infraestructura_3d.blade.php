@extends('layouts.usuario')

@section('title', 'Croquis de Infraestructura | ' . $acta->establecimiento->nombre)

@push('styles')
    <style>
        #blueprint-canvas {
            background-color: #f8fafc;
            background-image:
                linear-gradient(rgba(226, 232, 240, 0.4) 1px, transparent 1px),
                linear-gradient(90deg, rgba(226, 232, 240, 0.4) 1px, transparent 1px);
            background-size: 40px 40px;
            cursor: crosshair;
            touch-action: none;
            border-radius: 0.5rem;
            border: 2px solid #e2e8f0;
            width: 100%;
            height: 100%;
            display: block;
        }

        .tool-btn { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .tool-btn:hover { transform: translateY(-2px); }
        .tool-btn.active { background-color: #4f46e5; color: white; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }

        .blueprint-container { position: relative; user-select: none; }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        .btn-saving { pointer-events: none; opacity: 0.7; }
        .undo-redo-btn:disabled { opacity: 0.3; cursor: not-allowed; }

        /* Mini-mapa */
        #minimap-panel {
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #minimap-panel.collapsed {
            height: 44px;
            overflow: hidden;
        }
        #minimap-container {
            height: 220px;
            border-radius: 0 0 1rem 1rem;
            overflow: hidden;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 0.75rem !important;
            font-family: Inter, sans-serif;
            font-size: 11px;
        }
    </style>
@endpush

@section('content')
    <!-- Leaflet CSS — cargado aquí porque el layout no usa @stack('styles') -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    @php
        $lat = $acta->establecimiento->latitud;
        $lng = $acta->establecimiento->longitud;
        $hasCoords = !is_null($lat) && !is_null($lng);
        $nombreEstab = e($acta->establecimiento->nombre);
    @endphp

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tabletEditor', () => {
                let canvas, ctx;
                let isDragging = false;
                let dragTarget = null;
                let offset = { x: 0, y: 0 };
                const GRID = 10;
                const MAX_HISTORY = 50;

                return {
                    elements: @json($contenido['elementos'] ?? []),
                    connections: @json($contenido['conexiones'] ?? []),
                    tool: 'ambiente',
                    hwType: 'router',
                    layers: { furniture: true, network: true, power: true, calles: false },
                    tileCache: {},
                    tileOpacity: 0.5,
                    tileZoom: 21.5,
                    geoLat: {{ $hasCoords ? $lat : 'null' }},
                    geoLng: {{ $hasCoords ? $lng : 'null' }},
                    name: '',
                    roomSubtype: 'consultorio',
                    attrs: { wifi: false, light: false },
                    selectedId: null,
                    hoveredEl: null,
                    mouseX: 0, mouseY: 0,
                    isConnecting: false,
                    connectionStart: null,
                    sidebarOpen: true,
                    panelVisible: true,
                    isSaving: false,
                    isFullscreen: false,
                    history: [],
                    future: [],

                    /* ─── Lifecycle ─── */
                    init() {
                        this.$nextTick(() => {
                            canvas = document.getElementById('blueprint-canvas');
                            if (!canvas) { console.error('Canvas not found'); return; }
                            ctx = canvas.getContext('2d');
                            this.resizeCanvas();
                            this.draw();
                            this._refreshIcons();
                            window.addEventListener('resize', () => this.resizeCanvas());
                        });

                        this.$watch('sidebarOpen', () => {
                            this.$nextTick(() => setTimeout(() => { this.resizeCanvas(); this._refreshIcons(); }, 350));
                        });
                        this.$watch('tool', () => this.$nextTick(() => this._refreshIcons()));
                        this.$watch('selectedId', () => this.$nextTick(() => this._refreshIcons()));
                    },

                    _refreshIcons() {
                        if (window.lucide) window.lucide.createIcons();
                    },

                    /* ─── Fullscreen Toggle ─── */
                    toggleFullscreen() {
                        const el = document.getElementById('tablet-editor-container');
                        if (!el) return;
                        
                        if (!document.fullscreenElement) {
                            if (el.requestFullscreen) {
                                el.requestFullscreen();
                            } else if (el.webkitRequestFullscreen) {
                                el.webkitRequestFullscreen();
                            } else if (el.msRequestFullscreen) {
                                el.msRequestFullscreen();
                            }
                            this.isFullscreen = true;
                        } else {
                            if (document.exitFullscreen) {
                                document.exitFullscreen();
                            } else if (document.webkitExitFullscreen) {
                                document.webkitExitFullscreen();
                            } else if (document.msExitFullscreen) {
                                document.msExitFullscreen();
                            }
                            this.isFullscreen = false;
                        }
                    },

                    /* ─── Canvas Resize (HiDPI-safe) ─── */
                    resizeCanvas() {
                        const container = document.getElementById('canvas-container');
                        if (!container || !canvas) return;
                        const dpr = window.devicePixelRatio || 1;
                        const w = container.clientWidth;
                        const h = container.clientHeight;
                        canvas.width  = Math.round(w * dpr);
                        canvas.height = Math.round(h * dpr);
                        canvas.style.width  = `${w}px`;
                        canvas.style.height = `${h}px`;
                        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                        ctx.lineCap  = 'round';
                        ctx.lineJoin = 'round';
                        this.draw();
                    },

                    /* ─── Logical canvas size (CSS pixels) ─── */
                    get logicalW() { return canvas ? parseFloat(canvas.style.width)  : 0; },
                    get logicalH() { return canvas ? parseFloat(canvas.style.height) : 0; },

                    /* ─── Selected Element Data Binding ─── */
                    get selectedEl() { return this.elements.find(e => e.id === this.selectedId) || null; },
                    get selectedElName() { return this.selectedEl ? this.selectedEl.name : ''; },
                    set selectedElName(val) {
                        if (this.selectedEl) {
                            this.selectedEl.name = val;
                            this.draw();
                        }
                    },

                    /* ─── History (Undo/Redo) ─── */
                    _snapshot() {
                        const snap = JSON.stringify({ elements: this.elements, connections: this.connections });
                        this.history.push(snap);
                        if (this.history.length > MAX_HISTORY) this.history.shift();
                        this.future = [];
                    },
                    undo() {
                        if (!this.history.length) return;
                        this.future.push(JSON.stringify({ elements: this.elements, connections: this.connections }));
                        const prev = JSON.parse(this.history.pop());
                        this.elements    = prev.elements;
                        this.connections = prev.connections;
                        this.selectedId  = null;
                        this.draw();
                    },
                    redo() {
                        if (!this.future.length) return;
                        this.history.push(JSON.stringify({ elements: this.elements, connections: this.connections }));
                        const next = JSON.parse(this.future.pop());
                        this.elements    = next.elements;
                        this.connections = next.connections;
                        this.selectedId  = null;
                        this.draw();
                    },

                    /* ─── Hover ─── */
                    checkHover(x, y) {
                        this.hoveredEl = this.elements.find(el =>
                            x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h
                        ) || null;
                    },

                    /* ─── Add Element ─── */
                    addElement(type = this.tool) {
                        this._snapshot();
                        const lw = this.logicalW || 800;
                        const lh = this.logicalH || 600;
                        const w = type === 'hardware' ? 50 : (type === 'pasillo' ? 300 : (type === 'puerta' ? 40 : 120));
                        const h = type === 'hardware' ? 40 : (type === 'pasillo' ? 60  : (type === 'puerta' ? 40 : 100));
                        const rx = Math.round((Math.random() * (lw - w - 20) + 10) / GRID) * GRID;
                        const ry = Math.round((Math.random() * (lh - h - 20) + 10) / GRID) * GRID;
                        const newEl = {
                            id:      crypto.randomUUID(),
                            type,
                            subtype: type === 'ambiente' ? this.roomSubtype : (type === 'hardware' ? this.hwType : null),
                            name:    this.name || (type === 'hardware' ? this.hwType.toUpperCase() : (type === 'ambiente' ? (this.roomSubtype?.toUpperCase() || 'AMBIENTE') : type.toUpperCase())),
                            x: rx, y: ry, w, h,
                            rot: 0,
                            attrs: { ...this.attrs }
                        };
                        this.elements.push(newEl);
                        this.selectedId = newEl.id;
                        this.name = '';
                        this.draw();
                    },

                    /* ─── Main Draw ─── */
                    draw() {
                        if (!ctx || !canvas) return;
                        const lw = this.logicalW;
                        const lh = this.logicalH;
                        ctx.clearRect(0, 0, lw, lh);
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, lw, lh);

                        /* Capa de mapa base (tiles OSM) */
                        if (this.layers.calles && this.geoLat !== null) this.drawStreetBase(lw, lh);

                        /* Grid (logical px) */
                        ctx.strokeStyle = '#f1f5f9';
                        ctx.lineWidth = 1;
                        for (let x = 0; x <= lw; x += 40) {
                            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, lh); ctx.stroke();
                        }
                        for (let y = 0; y <= lh; y += 40) {
                            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(lw, y); ctx.stroke();
                        }

                        if (this.layers.network) this.drawConnections();

                        this.elements.forEach(el => {
                            ctx.save();
                            ctx.translate(el.x + el.w / 2, el.y + el.h / 2);
                            ctx.rotate((el.rot || 0) * Math.PI / 180);
                            ctx.translate(-(el.x + el.w / 2), -(el.y + el.h / 2));

                            let fill = '#ffffff', stroke = '#475569';
                            const type    = (el.type    || '').toLowerCase();
                            const subtype = (el.subtype || '').toLowerCase();

                            if (type === 'ambiente') {
                                switch (subtype) {
                                    case 'consultorio':   fill = '#bbf7d0'; stroke = '#16a34a'; break;
                                    case 'emergencias':   fill = '#fecaca'; stroke = '#dc2626'; break;
                                    case 'quirofano':     fill = '#bae6fd'; stroke = '#0284c7'; break;
                                    case 'administracion':fill = '#e9d5ff'; stroke = '#9333ea'; break;
                                    case 'baño':          fill = '#cffafe'; stroke = '#0891b2'; break;
                                    default:              fill = '#f1f5f9'; stroke = '#94a3b8'; break;
                                }
                            } else if (type === 'pasillo') { fill = '#f8fafc'; stroke = '#64748b'; }
                              else if (type === 'hardware') { fill = '#dbeafe'; stroke = '#2563eb'; }
                              else if (type === 'puerta')   { fill = '#fef9c3'; stroke = '#ca8a04'; }

                            ctx.shadowBlur   = el.id === this.selectedId ? 15 : 4;
                            ctx.shadowColor  = el.id === this.selectedId ? 'rgba(79,70,229,0.4)' : 'rgba(0,0,0,0.1)';
                            ctx.shadowOffsetY = 2;
                            ctx.lineWidth    = el.id === this.selectedId ? 4 : 2.5;
                            ctx.strokeStyle  = el.id === this.selectedId ? '#fbbf24' : stroke;
                            ctx.fillStyle    = fill;

                            this.drawRoundedRect(el.x, el.y, el.w, el.h, type === 'hardware' ? 20 : 6);
                            ctx.fill(); ctx.stroke();

                            ctx.shadowBlur = 0; ctx.shadowOffsetY = 0;

                            if (type === 'hardware')   this.drawHardwareSymbol(el);
                            else if (type === 'puerta') this.drawDoorSymbol(el);
                            else {
                                if (this.layers.furniture)                        this.drawFurnitureIcons(el);
                                if (this.layers.network || this.layers.power)    this.drawServiceIcons(el);
                            }

                            ctx.fillStyle = type === 'hardware' ? '#2563eb' : '#1e293b';
                            ctx.font      = 'bold 10px Inter, Arial';
                            ctx.textAlign = 'center';
                            const displayName = (el.name || subtype || type || 'Sin Nombre').toUpperCase();
                            ctx.fillText(displayName, el.x + el.w / 2, type === 'hardware' ? el.y - 10 : el.y + 18);
                            ctx.restore();
                        });
                    },

                    drawRoundedRect(x, y, w, h, r) {
                        ctx.beginPath();
                        ctx.moveTo(x + r, y);
                        ctx.lineTo(x + w - r, y); ctx.quadraticCurveTo(x + w, y, x + w, y + r);
                        ctx.lineTo(x + w, y + h - r); ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
                        ctx.lineTo(x + r, y + h); ctx.quadraticCurveTo(x, y + h, x, y + h - r);
                        ctx.lineTo(x, y + r); ctx.quadraticCurveTo(x, y, x + r, y);
                        ctx.closePath();
                    },

                    drawStreetBase(lw, lh) {
                        if (this.geoLat === null || this.geoLng === null) return;
                        
                        const LAT = this.geoLat;
                        const LNG = this.geoLng;
                        const TILE_ZOOM = 19;   /* Max OSM native zoom */
                        const SIM_ZOOM = parseFloat(this.tileZoom);  /* Zoom simulado controlable */
                        const SCALE = Math.pow(2, SIM_ZOOM - TILE_ZOOM);
                        const T = 256;
                        
                        /* Simplest WebMercator Projector at TILE_ZOOM */
                        const latRad = LAT * Math.PI / 180;
                        const n = Math.pow(2, TILE_ZOOM);
                        const xTileExact = (LNG + 180) / 360 * n;
                        const yTileExact = (1.0 - Math.log(Math.tan(latRad) + (1 / Math.cos(latRad))) / Math.PI) / 2.0 * n;

                        /* Center of canvas in logical zoom 19 coordinates */
                        const cx = xTileExact * T;
                        const cy = yTileExact * T;

                        /* Range of tiles needed for logical canvas size, adjusted by SCALE */
                        const txMin = Math.floor((cx - lw / (2 * SCALE)) / T);
                        const tyMin = Math.floor((cy - lh / (2 * SCALE)) / T);
                        const txMax = Math.ceil ((cx + lw / (2 * SCALE)) / T);
                        const tyMax = Math.ceil ((cy + lh / (2 * SCALE)) / T);

                        console.log(`[OSM] Overzoom ${SCALE}x. Drawing ${txMax - txMin + 1}x${tyMax - tyMin + 1} tiles around ${LAT}, ${LNG}`);

                        const subs = ['a', 'b', 'c'];

                        for (let tx = txMin; tx <= txMax; tx++) {
                            for (let ty = tyMin; ty <= tyMax; ty++) {
                                const sub = subs[(tx + ty) % 3];
                                const url = `https://${sub}.tile.openstreetmap.org/${TILE_ZOOM}/${tx}/${ty}.png`;

                                /* Top left pixel of this tile scaled and centered */
                                const dx = (tx * T - cx) * SCALE + lw / 2;
                                const dy = (ty * T - cy) * SCALE + lh / 2;
                                const size = T * SCALE;

                                if (this.tileCache[url] instanceof HTMLImageElement && this.tileCache[url].complete) {
                                    ctx.save();
                                    ctx.globalAlpha = this.tileOpacity;
                                    ctx.drawImage(this.tileCache[url], dx, dy, size, size);
                                    ctx.restore();
                                } else if (!this.tileCache[url]) {
                                    this.tileCache[url] = 'loading';
                                    const img = new Image();
                                    img.crossOrigin = 'anonymous';
                                    img.onload = () => { 
                                        this.tileCache[url] = img; 
                                        this.draw(); /* trigger redraw when loaded */
                                    };
                                    img.onerror = () => { 
                                        console.error('[OSM] Failed to load tile:', url);
                                        this.tileCache[url] = null; 
                                    };
                                    img.src = url;
                                }
                            }
                        }

                        /* Marcador GPS: círculo rojo en canvas center */
                        ctx.save();
                        ctx.beginPath();
                        ctx.arc(lw / 2, lh / 2, 8, 0, Math.PI * 2);
                        ctx.fillStyle   = 'rgba(239,68,68,0.85)';
                        ctx.strokeStyle = 'white';
                        ctx.lineWidth   = 3;
                        ctx.fill(); ctx.stroke();
                        ctx.fillStyle = 'white';
                        ctx.font      = 'bold 9px Inter,Arial';
                        ctx.textAlign = 'center';
                        ctx.fillText('GPS', lw / 2, lh / 2 + 3.5);
                        ctx.restore();
                    },

                    drawConnections() {
                        if (!ctx) return;
                        ctx.strokeStyle = '#3b82f6'; ctx.setLineDash([5, 5]); ctx.lineWidth = 2;
                        this.connections.forEach(conn => {
                            const el1 = this.elements.find(e => e.id === conn.from);
                            const el2 = this.elements.find(e => e.id === conn.to);
                            if (el1 && el2) {
                                ctx.beginPath();
                                ctx.moveTo(el1.x + el1.w / 2, el1.y + el1.h / 2);
                                ctx.lineTo(el2.x + el2.w / 2, el2.y + el2.h / 2);
                                ctx.stroke();
                            }
                        });
                        if (this.isConnecting && this.connectionStart) {
                            const startEl = this.elements.find(e => e.id === this.connectionStart);
                            if (startEl) {
                                const rect = canvas.getBoundingClientRect();
                                const cx = this._lastMouseClientX - rect.left;
                                const cy = this._lastMouseClientY - rect.top;
                                ctx.beginPath();
                                ctx.moveTo(startEl.x + startEl.w / 2, startEl.y + startEl.h / 2);
                                ctx.lineTo(cx, cy);
                                ctx.stroke();
                            }
                        }
                        ctx.setLineDash([]);
                    },

                    /* ─── Furniture icons per subtype ─── */
                    drawFurnitureIcons(el) {
                        ctx.strokeStyle = '#94a3b8'; ctx.lineWidth = 1;
                        const cx = el.x + el.w / 2, cy = el.y + el.h / 2;
                        const subtype = (el.subtype || '').toLowerCase();
                        switch (subtype) {
                            case 'consultorio':
                                /* desk */
                                ctx.strokeRect(el.x + 10, el.y + 30, el.w - 20, 22);
                                /* chair circle */
                                ctx.beginPath(); ctx.arc(cx, el.y + 68, 6, 0, Math.PI * 2); ctx.stroke();
                                break;
                            case 'emergencias':
                                /* stretcher */
                                ctx.strokeRect(cx - 20, cy - 8, 40, 16);
                                ctx.beginPath(); ctx.arc(cx + 20, cy, 8, -Math.PI/2, Math.PI/2); ctx.stroke();
                                break;
                            case 'quirofano':
                                /* operating table */
                                ctx.strokeRect(cx - 25, cy - 6, 50, 12);
                                /* overhead lamp cross */
                                ctx.beginPath(); ctx.moveTo(cx, cy - 20); ctx.lineTo(cx, cy - 10); ctx.stroke();
                                ctx.beginPath(); ctx.moveTo(cx - 5, cy - 15); ctx.lineTo(cx + 5, cy - 15); ctx.stroke();
                                break;
                            case 'administracion':
                                /* L-desk */
                                ctx.strokeRect(el.x + 8, el.y + 28, el.w - 30, 18);
                                ctx.strokeRect(el.x + el.w - 28, el.y + 28, 20, 30);
                                break;
                            case 'baño':
                                /* toilet oval */
                                ctx.beginPath(); ctx.ellipse(cx, cy, 10, 14, 0, 0, Math.PI * 2); ctx.stroke();
                                ctx.strokeRect(cx - 10, el.y + 28, 20, 10);
                                break;
                            default:
                                /* generic table */
                                ctx.strokeRect(el.x + 10, el.y + 30, el.w - 20, el.h - 50);
                        }
                    },

                    drawServiceIcons(el) {
                        let px = el.x + 10;
                        const py = el.y + el.h - 15;
                        if (el.attrs?.wifi && this.layers.network) {
                            ctx.fillStyle = '#2563eb';
                            ctx.beginPath(); ctx.arc(px, py, 6, 0, Math.PI * 2); ctx.fill();
                            ctx.strokeStyle = 'white'; ctx.lineWidth = 1.5; ctx.stroke();
                            px += 18;
                        }
                        if (el.attrs?.light && this.layers.power) {
                            ctx.fillStyle = '#d97706';
                            ctx.beginPath(); ctx.arc(px, py, 6, 0, Math.PI * 2); ctx.fill();
                            ctx.strokeStyle = 'white'; ctx.lineWidth = 1.5; ctx.stroke();
                        }
                    },

                    drawHardwareSymbol(el) {
                        ctx.strokeStyle = '#2563eb'; ctx.lineWidth = 2.5;
                        const cx = el.x + el.w / 2, cy = el.y + el.h / 2;
                        switch ((el.subtype || '').toLowerCase()) {
                            case 'router':
                                ctx.strokeRect(cx - 15, cy - 8, 30, 16);
                                ctx.beginPath(); ctx.moveTo(cx - 10, cy - 8); ctx.lineTo(cx - 12, cy - 18); ctx.stroke();
                                ctx.beginPath(); ctx.moveTo(cx + 10, cy - 8); ctx.lineTo(cx + 12, cy - 18); ctx.stroke();
                                break;
                            case 'ap':
                                ctx.beginPath(); ctx.arc(cx, cy, 10, 0, Math.PI * 2); ctx.stroke();
                                [12, 17, 22].forEach(r => {
                                    ctx.beginPath(); ctx.arc(cx, cy, r, -Math.PI / 4, Math.PI / 4); ctx.stroke();
                                });
                                ctx.beginPath(); ctx.arc(cx, cy, 4, 0, Math.PI * 2);
                                ctx.fillStyle = '#2563eb'; ctx.fill();
                                break;
                            case 'switch':
                                ctx.strokeRect(cx - 20, cy - 6, 40, 12);
                                for (let i = 0; i < 4; i++) {
                                    ctx.beginPath(); ctx.moveTo(cx - 14 + i * 10, cy - 6); ctx.lineTo(cx - 14 + i * 10, cy - 12); ctx.stroke();
                                }
                                break;
                            default:
                                /* generic box */
                                ctx.strokeRect(cx - 12, cy - 8, 24, 16);
                        }
                    },

                    drawDoorSymbol(el) {
                        ctx.strokeStyle = '#475569'; ctx.lineWidth = 2;
                        ctx.beginPath(); ctx.moveTo(el.x, el.y); ctx.lineTo(el.x + el.w, el.y); ctx.stroke();
                        ctx.beginPath(); ctx.setLineDash([2, 2]);
                        ctx.arc(el.x, el.y, el.w, 0, Math.PI / 2); ctx.stroke();
                        ctx.setLineDash([]);
                        ctx.beginPath(); ctx.moveTo(el.x, el.y); ctx.lineTo(el.x, el.y + el.w); ctx.stroke();
                    },

                    /* ─── Event helpers ─── */
                    _getEventCoords(e) {
                        const rect = canvas.getBoundingClientRect();
                        if (e.touches) {
                            return {
                                x: e.touches[0].clientX - rect.left,
                                y: e.touches[0].clientY - rect.top,
                                clientX: e.touches[0].clientX,
                                clientY: e.touches[0].clientY
                            };
                        }
                        return {
                            x: e.clientX - rect.left,
                            y: e.clientY - rect.top,
                            clientX: e.clientX,
                            clientY: e.clientY
                        };
                    },

                    _lastMouseClientX: 0,
                    _lastMouseClientY: 0,

                    handleMouseDown(e) { this._startInteraction(this._getEventCoords(e)); },
                    handleTouchStart(e) { e.preventDefault(); this._startInteraction(this._getEventCoords(e)); },

                    _startInteraction({ x, y }) {
                        if (this.tool === 'red') {
                            const clicked = this.elements.find(el => x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h);
                            if (clicked) { this.isConnecting = true; this.connectionStart = clicked.id; return; }
                        }
                        for (let i = this.elements.length - 1; i >= 0; i--) {
                            const el = this.elements[i];
                            if (x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h) {
                                this.selectedId = el.id;
                                isDragging  = true; dragTarget = el;
                                offset.x = x - el.x; offset.y = y - el.y;
                                this.draw(); return;
                            }
                        }
                        this.selectedId = null; this.draw();
                    },

                    handleMouseMove(e) { this._moveInteraction(this._getEventCoords(e)); },
                    handleTouchMove(e) { e.preventDefault(); this._moveInteraction(this._getEventCoords(e)); },

                    _moveInteraction({ x, y, clientX, clientY }) {
                        const containerRect = document.getElementById('canvas-container').getBoundingClientRect();
                        this.mouseX = clientX - containerRect.left;
                        this.mouseY = clientY - containerRect.top;
                        this._lastMouseClientX = clientX;
                        this._lastMouseClientY = clientY;

                        this.checkHover(x, y);

                        if (this.isConnecting) { this.draw(); return; }
                        if (isDragging && dragTarget) {
                            dragTarget.x = Math.round((x - offset.x) / GRID) * GRID;
                            dragTarget.y = Math.round((y - offset.y) / GRID) * GRID;
                            this.draw();
                        }
                    },

                    handleMouseUp(e) { this._endInteraction(this._getEventCoords(e)); },
                    handleTouchEnd(e) {
                        e.preventDefault();
                        const coords = e.changedTouches
                            ? (() => {
                                const rect = canvas.getBoundingClientRect();
                                return { x: e.changedTouches[0].clientX - rect.left, y: e.changedTouches[0].clientY - rect.top };
                              })()
                            : { x: 0, y: 0 };
                        this._endInteraction(coords);
                    },

                    _endInteraction({ x, y }) {
                        if (this.isConnecting && this.connectionStart) {
                            const endEl = this.elements.find(el => x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h);
                            if (endEl && endEl.id !== this.connectionStart) {
                                /* No duplicate connections */
                                const already = this.connections.some(c =>
                                    (c.from === this.connectionStart && c.to === endEl.id) ||
                                    (c.from === endEl.id && c.to === this.connectionStart)
                                );
                                if (!already) {
                                    this._snapshot();
                                    this.connections.push({ from: this.connectionStart, to: endEl.id });
                                }
                            }
                            this.isConnecting = false; this.connectionStart = null; this.draw();
                        }
                        if (isDragging) this._snapshot();
                        isDragging = false; dragTarget = null;
                    },

                    /* ─── Element operations ─── */
                    resizeSelected(dw, dh) {
                        const el = this.elements.find(e => e.id === this.selectedId);
                        if (el) {
                            this._snapshot();
                            el.w = Math.max(20, el.w + dw);
                            el.h = Math.max(20, el.h + dh);
                            this.draw();
                        }
                    },

                    rotateSelected() {
                        const el = this.elements.find(e => e.id === this.selectedId);
                        if (el) { this._snapshot(); el.rot = (el.rot + 90) % 360; this.draw(); }
                    },

                    deleteSelected() {
                        this._snapshot();
                        this.elements    = this.elements.filter(e => e.id !== this.selectedId);
                        this.connections = this.connections.filter(c => c.from !== this.selectedId && c.to !== this.selectedId);
                        this.selectedId  = null; this.draw();
                    },

                    async confirmDelete(e) {
                        if (!this.selectedId) return;
                        const result = await Swal.fire({
                            title: '¿Eliminar elemento?',
                            text: 'Esta acción se puede deshacer con Ctrl+Z.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        });
                        if (result.isConfirmed) this.deleteSelected();
                    },

                    /* ─── Save ─── */
                    async saveData() {
                        if (this.isSaving) return;
                        this.isSaving = true;
                        try {
                            const res = await fetch("{{ route('usuario.monitoreo.infraestructura-3d.store', $acta->id) }}", {
                                method:  'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body:    JSON.stringify({ contenido: { elementos: this.elements, conexiones: this.connections } })
                            });
                            if (res.ok) {
                                Swal.fire({ title: '¡Guardado!', text: 'El croquis se ha actualizado con éxito.', icon: 'success', confirmButtonColor: '#4f46e5', timer: 2000, showConfirmButton: false });
                            } else {
                                let msg = `Error del servidor (${res.status})`;
                                try { const body = await res.json(); msg = body.message || msg; } catch (_) {}
                                Swal.fire('Error al guardar', msg, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error de red', e.message, 'error');
                        } finally {
                            this.isSaving = false;
                        }
                    }
                };
            });
        });
    </script>

    <div id="tablet-editor-container"
         class="h-screen flex flex-col bg-slate-100 overflow-hidden font-sans"
         x-data="tabletEditor"
         @keydown.ctrl.z.window="undo()"
         @keydown.ctrl.y.window="redo()"
         @keydown.meta.z.window="undo()"
         @keydown.delete.window="selectedId && confirmDelete($event)">

        <!-- Barra Superior -->
        <div x-show="panelVisible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-y-full opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="-translate-y-full opacity-0"
             class="bg-white border-b border-slate-200 px-4 py-3 flex flex-wrap items-center justify-between gap-4 shadow-sm z-30">

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center text-slate-600 transition-colors">
                        <i :data-lucide="sidebarOpen ? 'panel-left-close' : 'panel-left-open'" class="w-4 h-4"></i>
                    </button>
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
                        <i data-lucide="layout" class="w-5 h-5"></i>
                    </div>
                    <h1 class="text-sm font-black text-slate-800 uppercase tracking-tighter">Planificador <span class="text-indigo-600">Pro</span></h1>
                </div>

                <div class="h-6 w-px bg-slate-200"></div>

                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                    <button @click="tool = 'ambiente'" :class="tool === 'ambiente' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-2">
                        <i data-lucide="square" class="w-4 h-4"></i> Ambiente
                    </button>
                    <button @click="tool = 'hardware'" :class="tool === 'hardware' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                            class="px-4 py-2 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-2">
                        <i data-lucide="cpu" class="w-4 h-4"></i> Equipamiento TI
                    </button>
                    <button @click="tool = 'puerta'" :class="tool === 'puerta' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                            class="px-4 py-2 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-2">
                        <i data-lucide="door-open" class="w-4 h-4"></i> Puerta
                    </button>
                    <button @click="tool = 'red'" :class="tool === 'red' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-2">
                        <i data-lucide="share-2" class="w-4 h-4"></i> Cableado
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <!-- Undo / Redo -->
                <div class="flex items-center gap-1">
                    <button @click="undo()" :disabled="!history.length"
                            class="undo-redo-btn w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center text-slate-600 transition-colors"
                            title="Deshacer (Ctrl+Z)">
                        <i data-lucide="undo-2" class="w-4 h-4"></i>
                    </button>
                    <button @click="redo()" :disabled="!future.length"
                            class="undo-redo-btn w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center text-slate-600 transition-colors"
                            title="Rehacer (Ctrl+Y)">
                        <i data-lucide="redo-2" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Capas Toggle -->
                <div class="flex items-center gap-4 px-4 py-2 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" x-model="layers.furniture" @change="draw()" class="rounded text-indigo-600">
                        <span class="text-[8px] font-black uppercase text-slate-500 group-hover:text-indigo-600 transition-colors">Mobiliario</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" x-model="layers.network" @change="draw()" class="rounded text-blue-600">
                        <span class="text-[8px] font-black uppercase text-slate-500 group-hover:text-blue-600 transition-colors">Internet</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" x-model="layers.power" @change="draw()" class="rounded text-amber-500">
                        <span class="text-[8px] font-black uppercase text-slate-500 group-hover:text-amber-500 transition-colors">Energía</span>
                    </label>
                    @if($hasCoords)
                    <div class="h-4 w-px bg-slate-200"></div>
                    <label class="flex items-center gap-2 cursor-pointer group" title="Muestra las calles reales del establecimiento como fondo">
                        <input type="checkbox" x-model="layers.calles" @change="draw()" class="rounded text-emerald-600">
                        <span class="text-[8px] font-black uppercase text-emerald-600 group-hover:text-emerald-800 transition-colors">🗺️ Calles</span>
                    </label>
                    <div x-show="layers.calles" class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[7px] text-slate-400 font-bold uppercase">Opac.</span>
                            <input type="range" min="0.2" max="1" step="0.05"
                                   x-model="tileOpacity" @input="draw()"
                                   class="w-14 h-1 accent-emerald-600">
                        </div>
                        <div class="flex items-center gap-1.5 border-l border-slate-200 pl-3">
                            <span class="text-[7px] text-slate-400 font-bold uppercase" title="Acercar/Alejar mapa base">Zoom</span>
                            <input type="range" min="19" max="23" step="0.1"
                                   x-model="tileZoom" @input="draw()"
                                   class="w-16 h-1 accent-emerald-600">
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <button @click="toggleFullscreen()"
                            :class="isFullscreen ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-600'"
                            class="px-4 py-2 hover:bg-indigo-200 rounded-xl text-[10px] font-black uppercase transition-all flex items-center gap-2"
                            title="Pantalla Completa">
                        <i :data-lucide="isFullscreen ? 'minimize' : 'maximize'" class="w-4 h-4"></i>
                        <span x-text="isFullscreen ? 'Salir' : 'Pantalla Completa'"></span>
                    </button>
                    <button @click="saveData()" :class="isSaving ? 'btn-saving' : ''"
                            class="px-6 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 transition-all flex items-center gap-2 shadow-lg shadow-slate-200">
                        <i :data-lucide="isSaving ? 'loader' : 'save'" :class="isSaving ? 'animate-spin' : ''" class="w-4 h-4"></i>
                        <span x-text="isSaving ? 'Guardando…' : 'Guardar'"></span>
                    </button>
                    <button @click="panelVisible = false"
                            class="w-10 h-10 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center transition-all"
                            title="Minimizar herramientas">
                        <i data-lucide="minimize-2" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 flex overflow-hidden bg-slate-100 relative">
            <div class="flex-1 bg-[#f1f5f9] overflow-hidden relative flex flex-col p-0"
                 id="canvas-container"
                 @click.self="selectedId = null; draw()">

                <!-- Botón Flotante Restaurar -->
                <button x-show="!panelVisible"
                        @click="panelVisible = true"
                        x-transition:enter="transition ease-out duration-500 delay-350"
                        x-transition:enter-start="scale-0 rotate-180"
                        x-transition:enter-end="scale-100 rotate-0"
                        class="absolute top-6 left-6 w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-2xl z-50 hover:bg-indigo-700 hover:scale-110 active:scale-95 transition-all group">
                    <i data-lucide="layout" class="w-6 h-6"></i>
                    <div class="absolute left-full ml-4 px-3 py-1 bg-slate-900 text-white text-[10px] font-black uppercase rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap shadow-xl">
                        Restaurar Herramientas
                    </div>
                </button>

                <!-- Panel Lateral Flotante -->
                <div x-show="sidebarOpen && panelVisible"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="-translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="-translate-x-full opacity-0"
                     class="absolute top-4 left-4 w-72 bg-white/90 backdrop-blur-xl border border-white/20 flex flex-col p-5 shadow-2xl z-40 rounded-3xl overflow-y-auto"
                     :style="panelVisible ? 'max-height: calc(100vh - 120px)' : 'max-height: calc(100vh - 40px)'">

                    <div class="flex flex-col gap-6" id="tools-content">

                        <template x-if="tool === 'ambiente'">
                            <div class="bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100">
                                <h2 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i data-lucide="plus-circle" class="w-3 h-3"></i> Nuevo Ambiente
                                </h2>
                                <div class="space-y-4">
                                    <select x-model="roomSubtype" class="w-full bg-white border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                        <option value="consultorio">🏥 CONSULTORIO</option>
                                        <option value="quirofano">🔪 QUIRÓFANO</option>
                                        <option value="emergencias">🚨 EMERGENCIAS</option>
                                        <option value="administracion">📁 ADMINISTRACIÓN</option>
                                        <option value="baño">🚻 BAÑO</option>
                                    </select>
                                    <input type="text" x-model="name" placeholder="NOMBRE…"
                                           class="w-full bg-white border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-300">
                                    <div class="grid grid-cols-2 gap-2">
                                        <button @click="attrs.wifi = !attrs.wifi" :class="attrs.wifi ? 'bg-blue-600 text-white shadow-indigo-200' : 'bg-white text-slate-400'"
                                                class="p-4 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm group">
                                            <i data-lucide="wifi" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                            <span class="text-[8px] font-black uppercase">Wifi</span>
                                        </button>
                                        <button @click="attrs.light = !attrs.light" :class="attrs.light ? 'bg-amber-500 text-white shadow-amber-200' : 'bg-white text-slate-400'"
                                                class="p-4 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm group">
                                            <i data-lucide="zap" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                            <span class="text-[8px] font-black uppercase">Luz</span>
                                        </button>
                                    </div>
                                    <button @click="addElement('ambiente')" class="w-full py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                        Añadir al Plano
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-if="tool === 'hardware'">
                            <div class="bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100">
                                <h2 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i data-lucide="cpu" class="w-3 h-3"></i> Equipamiento TI
                                </h2>
                                <div class="grid grid-cols-3 gap-2 mb-4">
                                    <button @click="hwType = 'router'" :class="hwType === 'router' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-400'"
                                            class="p-3 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm">
                                        <i data-lucide="router" class="w-5 h-5"></i>
                                        <span class="text-[8px] font-black uppercase">Router</span>
                                    </button>
                                    <button @click="hwType = 'ap'" :class="hwType === 'ap' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-400'"
                                            class="p-3 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm">
                                        <i data-lucide="rss" class="w-5 h-5"></i>
                                        <span class="text-[8px] font-black uppercase">AP</span>
                                    </button>
                                    <button @click="hwType = 'switch'" :class="hwType === 'switch' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-400'"
                                            class="p-3 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm">
                                        <i data-lucide="layers" class="w-5 h-5"></i>
                                        <span class="text-[8px] font-black uppercase">Switch</span>
                                    </button>
                                </div>
                                <button @click="addElement('hardware')" class="w-full py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                    Colocar Equipo
                                </button>
                            </div>
                        </template>

                        <template x-if="tool === 'puerta'">
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i data-lucide="door-open" class="w-3 h-3"></i> Colocar Puerta
                                </h2>
                                <p class="text-[9px] text-slate-500 mb-4">Las puertas se dibujan con su arco de apertura. Haz clic para añadir una al plano.</p>
                                <button @click="addElement('puerta')" class="w-full py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase hover:bg-slate-800 transition-all shadow-lg">
                                    Añadir Puerta
                                </button>
                            </div>
                        </template>

                        <template x-if="tool === 'red'">
                            <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
                                <h2 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i data-lucide="share-2" class="w-3 h-3"></i> Cableado de Red
                                </h2>
                                <div class="space-y-3">
                                    <p class="text-[9px] text-slate-500">Haz clic en un equipo o ambiente y arrastra hacia otro para crear una conexión de red.</p>
                                    <div class="p-3 bg-white rounded-xl border border-blue-100 flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                        <span class="text-[9px] font-bold text-slate-600 uppercase">Modo Conexión Activo</span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedId">
                            <div class="mt-6 pt-6 border-t border-slate-100">
                                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Editar Selección</h2>
                                
                                <div class="mb-4">
                                    <input type="text" x-model="selectedElName" placeholder="Nombre..."
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-300">
                                </div>

                                <div class="grid grid-cols-2 gap-2 mb-4">
                                    <button @click="resizeSelected(20, 0)"  class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">+ Ancho</button>
                                    <button @click="resizeSelected(-20, 0)" class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">- Ancho</button>
                                    <button @click="resizeSelected(0, 20)"  class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">+ Largo</button>
                                    <button @click="resizeSelected(0, -20)" class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">- Largo</button>
                                </div>
                                <button @click="rotateSelected()" class="w-full py-3 mb-2 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase flex items-center justify-center gap-2 hover:bg-slate-800 transition-all">
                                    <i data-lucide="rotate-cw" class="w-4 h-4"></i> Rotar 90°
                                </button>
                                <button @click="confirmDelete($event)" class="w-full py-3 bg-rose-50 text-rose-600 rounded-2xl text-[10px] font-black uppercase flex items-center justify-center gap-2 hover:bg-rose-100 transition-all">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                                </button>
                            </div>
                        </template>

                    </div><!-- /tools-content -->
                </div><!-- /sidebar -->

                <canvas id="blueprint-canvas"
                        @mousedown="handleMouseDown"
                        @mousemove="handleMouseMove"
                        @mouseup="handleMouseUp"
                        @touchstart="handleTouchStart"
                        @touchmove="handleTouchMove"
                        @touchend="handleTouchEnd"
                        @contextmenu.prevent="confirmDelete($event)">
                </canvas>

                <!-- Tooltip flotante -->
                <div x-show="hoveredEl"
                     :style="`left: ${mouseX + 20}px; top: ${mouseY + 20}px`"
                     class="absolute z-50 bg-white/90 backdrop-blur shadow-2xl border border-slate-200 rounded-2xl p-4 pointer-events-none transition-all w-48">
                    <template x-if="hoveredEl">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9px] font-black uppercase text-indigo-600" x-text="hoveredEl.subtype || hoveredEl.type"></span>
                                <div class="flex gap-1" x-show="layers.network || layers.power">
                                    <div x-show="hoveredEl.attrs?.wifi  && layers.network" class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    <div x-show="hoveredEl.attrs?.light && layers.power"   class="w-2 h-2 rounded-full bg-amber-500"></div>
                                </div>
                            </div>
                            <h3 class="text-xs font-bold text-slate-800 mb-1" x-text="hoveredEl.name || 'Sin nombre'"></h3>
                        </div>
                    </template>
                </div>

                <!-- ══════════════════════════════════════════════ -->
                <!-- Mini-Mapa de Referencia (Leaflet)             -->
                <!-- ══════════════════════════════════════════════ -->

                <div id="minimap-panel"
                     class="fixed bottom-5 right-5 w-72 bg-white/95 backdrop-blur-xl border border-slate-200 rounded-2xl shadow-2xl z-[9999] overflow-hidden">

                    <!-- Header del mapa -->
                    <div id="minimap-header"
                         class="flex items-center justify-between px-4 py-2.5 bg-white cursor-pointer select-none border-b border-slate-100"
                         onclick="toggleMinimap()">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase text-slate-400 leading-none">Mapa de Referencia</p>
                                <p class="text-[10px] font-bold text-slate-700 truncate max-w-[160px]">{{ $nombreEstab }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($hasCoords)
                                <span class="text-[8px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">GPS ✓</span>
                            @else
                                <span class="text-[8px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Sin coords</span>
                            @endif
                            <svg id="minimap-chevron" class="w-4 h-4 text-slate-400 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Cuerpo del mapa -->
                    @if($hasCoords)
                        <div id="minimap-container" style="height:220px;width:100%;"></div>
                        <!-- Footer dinámico -->
                        <div class="px-3 py-2 bg-slate-50 border-t border-slate-100">
                            <!-- Fila coordenadas + acciones -->
                            <div class="flex items-center justify-between">
                                <span id="minimap-coords" class="text-[8px] font-mono text-slate-400">
                                    {{ number_format($lat, 6) }}, {{ number_format($lng, 6) }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <button id="btn-edit-location"
                                            onclick="toggleEditMode()"
                                            class="text-[8px] font-black uppercase text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        Editar
                                    </button>
                                    <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}"
                                       target="_blank"
                                       class="text-[8px] font-black uppercase text-slate-400 hover:text-slate-600 transition-colors">
                                        Maps →
                                    </a>
                                </div>
                            </div>
                            <!-- Banner de confirmación (oculto por defecto) -->
                            <div id="minimap-save-banner" class="hidden mt-2 p-2 bg-indigo-50 rounded-xl border border-indigo-100">
                                <p class="text-[8px] text-indigo-700 font-bold mb-1.5">📍 Haz clic en el mapa para mover el marcador</p>
                                <div class="flex gap-1.5">
                                    <button onclick="saveNewCoords()"
                                            class="flex-1 py-1.5 bg-indigo-600 text-white rounded-lg text-[8px] font-black uppercase hover:bg-indigo-700 transition-all">
                                        Guardar
                                    </button>
                                    <button onclick="cancelEditMode()"
                                            class="flex-1 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-[8px] font-black uppercase hover:bg-slate-200 transition-all">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                            <div class="w-10 h-10 bg-amber-50 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                                </svg>
                            </div>
                            <p class="text-[10px] font-bold text-slate-600 mb-1">Sin coordenadas registradas</p>
                            <p class="text-[9px] text-slate-400">Registra latitud y longitud del establecimiento para ver el mapa.</p>
                        </div>
                    @endif
                </div>

            </div><!-- /canvas-container -->
        </div>
    </div>

<!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        /* ─── Mini-mapa Leaflet ─── */
        @if($hasCoords)
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const LAT  = {{ $lat }};
                const LNG  = {{ $lng }};
                const NAME = @json($nombreEstab);
                const ESTAB_ID = {{ $acta->establecimiento->id }};
                const CSRF = '{{ csrf_token() }}';

                let editMode  = false;
                let pendingLat = LAT;
                let pendingLng = LNG;

                const map = L.map('minimap-container', {
                    center:          [LAT, LNG],
                    zoom:            17,
                    zoomControl:     false,
                    attributionControl: false,
                    dragging:        true,
                    scrollWheelZoom: false,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '\u00a9 OpenStreetMap'
                }).addTo(map);

                /* Marcador tipo pin */
                const icon = L.divIcon({
                    html: `<div style="
                        width:24px;height:24px;background:#4f46e5;
                        border-radius:50% 50% 50% 0;transform:rotate(-45deg);
                        border:3px solid white;box-shadow:0 3px 10px rgba(79,70,229,0.5);
                    "></div>`,
                    iconSize:   [24, 24],
                    iconAnchor: [12, 24],
                    className:  ''
                });

                const marker = L.marker([LAT, LNG], { icon, draggable: false }).addTo(map);
                marker.bindPopup(
                    `<strong style="font-size:11px">${NAME}</strong><br>
                    <span style="color:#64748b;font-size:10px">${LAT.toFixed(6)}, ${LNG.toFixed(6)}</span>`,
                    { offset: [0, -8] }
                ).openPopup();

                /* Clic en el mapa → mover marcador en modo edición */
                map.on('click', function (e) {
                    if (!editMode) return;
                    pendingLat = e.latlng.lat;
                    pendingLng = e.latlng.lng;
                    marker.setLatLng([pendingLat, pendingLng]);
                    marker.getPopup()
                        .setContent(`<strong style="font-size:11px">${NAME}</strong><br>
                            <span style="color:#e11d48;font-size:10px">📍 Nueva: ${pendingLat.toFixed(6)}, ${pendingLng.toFixed(6)}</span>`)
                        .update();
                    document.getElementById('minimap-coords').textContent =
                        `${pendingLat.toFixed(6)}, ${pendingLng.toFixed(6)}`;
                });

                /* Redibujar al expandir/colapsar */
                document.getElementById('minimap-panel').addEventListener('transitionend', () => {
                    map.invalidateSize();
                });

                window._minimapInstance = map;

                /* — Funciones globales de edición — */
                window.toggleEditMode = function () {
                    editMode = !editMode;
                    const banner = document.getElementById('minimap-save-banner');
                    const btn    = document.getElementById('btn-edit-location');
                    if (editMode) {
                        banner.classList.remove('hidden');
                        btn.classList.add('text-rose-500');
                        btn.classList.remove('text-indigo-600');
                        btn.textContent = '✕ Editar';
                        map.getContainer().style.cursor = 'crosshair';
                        map.scrollWheelZoom.enable();
                    } else {
                        cancelEditMode();
                    }
                };

                window.cancelEditMode = function () {
                    editMode = false;
                    pendingLat = LAT; pendingLng = LNG;
                    marker.setLatLng([LAT, LNG]);
                    marker.getPopup()
                        .setContent(`<strong style="font-size:11px">${NAME}</strong><br>
                            <span style="color:#64748b;font-size:10px">${LAT.toFixed(6)}, ${LNG.toFixed(6)}</span>`)
                        .update();
                    document.getElementById('minimap-coords').textContent = `${LAT.toFixed(6)}, ${LNG.toFixed(6)}`;
                    document.getElementById('minimap-save-banner').classList.add('hidden');
                    const btn = document.getElementById('btn-edit-location');
                    btn.classList.remove('text-rose-500');
                    btn.classList.add('text-indigo-600');
                    btn.textContent = 'Editar';
                    map.getContainer().style.cursor = '';
                    map.scrollWheelZoom.disable();
                };

                window.saveNewCoords = async function () {
                    const btn = document.querySelector('#minimap-save-banner button:first-child');
                    btn.textContent = 'Guardando…'; btn.disabled = true;
                    try {
                        const res = await fetch(`/establecimientos/${ESTAB_ID}/coordenadas`, {
                            method:  'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                            body:    JSON.stringify({ latitud: pendingLat, longitud: pendingLng }),
                        });
                        const data = await res.json();
                        if (data.ok) {
                            Swal.fire({ title: '¡Ubicación actualizada!', text: data.mensaje, icon: 'success',
                                confirmButtonColor: '#4f46e5', timer: 2500, showConfirmButton: false });
                            /* Actualizar LAT/LNG locales para cancelar correctamente después */
                            cancelEditMode();
                            /* Reiniciar el popup con las coords definitivas */
                            marker.getPopup()
                                .setContent(`<strong style="font-size:11px">${NAME}</strong><br>
                                    <span style="color:#64748b;font-size:10px">${pendingLat.toFixed(6)}, ${pendingLng.toFixed(6)}</span>`)
                                .update();
                        } else {
                            Swal.fire('Error', res.statusText, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error de red', e.message, 'error');
                    } finally {
                        btn.textContent = 'Guardar'; btn.disabled = false;
                    }
                };

            }, 100);
        });
        @endif

        /* ─── Toggle colapso del mini-mapa ─── */
        function toggleMinimap() {
            const panel   = document.getElementById('minimap-panel');
            const chevron = document.getElementById('minimap-chevron');
            panel.classList.toggle('collapsed');
            chevron.style.transform = panel.classList.contains('collapsed') ? 'rotate(-90deg)' : '';
        }
    </script>

@endsection
