@extends('layouts.usuario')

@section('title', 'Croquis de Infraestructura 2D | ' . $acta->establecimiento->nombre)

@push('styles')
    <style>
        #blueprint-canvas {
            background-color: #f8fafc; /* Paper-like background */
            background-image: 
                linear-gradient(rgba(226, 232, 240, 0.4) 1px, transparent 1px),
                linear-gradient(90, rgba(226, 232, 240, 0.4) 1px, transparent 1px);
            background-size: 40px 40px;
            cursor: crosshair;
            touch-action: none;
            border-radius: 0.5rem;
            border: 2px solid #e2e8f0;
        }

        .tool-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tool-btn:hover {
            transform: translateY(-2px);
        }

        .tool-btn.active {
            background-color: #4f46e5;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .blueprint-container {
            position: relative;
            user-select: none;
        }

        .recording-dot {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }
    </style>
@endpush

@section('content')
    <script>
        /** 
         * Tablet Editor Module
         * Encapsulates croquis logic for infrastructure management.
         * Defined here to ensure it's available before Alpine.js initial parsing.
         */
        window.tabletEditor = function() {
            let canvas, ctx;
            let isDragging = false;
            let dragTarget = null;
            let offset = { x: 0, y: 0 };

            return {
                elements: @json($contenido['elementos'] ?? []),
                connections: @json($contenido['conexiones'] ?? []),
                tool: 'ambiente',
                hwType: 'router',
                layers: { furniture: true, network: true, power: true },
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

                init() {
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                        canvas = document.getElementById('blueprint-canvas');
                        if (!canvas) {
                            console.error('Canvas element "blueprint-canvas" not found.');
                            return;
                        }
                        ctx = canvas.getContext('2d');
                        this.resizeCanvas();
                        this.draw();
                        window.addEventListener('resize', () => this.resizeCanvas());
                    });
                    
                    this.$watch('sidebarOpen', () => {
                        this.$nextTick(() => {
                            setTimeout(() => {
                                this.resizeCanvas();
                                if (window.lucide) window.lucide.createIcons();
                            }, 350);
                        });
                    });
                    
                    this.$watch('tool', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));
                    this.$watch('selectedId', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));
                },

                resizeCanvas() {
                    const container = document.getElementById('canvas-container');
                    if (!container || !canvas) return;
                    const w = container.clientWidth;
                    const h = container.clientHeight;
                    canvas.width = w * (window.devicePixelRatio || 1);
                    canvas.height = h * (window.devicePixelRatio || 1);
                    canvas.style.width = `${w}px`;
                    canvas.style.height = `${h}px`;
                    ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    this.draw();
                },

                checkHover(x, y) {
                    this.hoveredEl = this.elements.find(el => 
                        x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h
                    ) || null;
                },

                addElement(type = this.tool) {
                    const newEl = {
                        id: Date.now(),
                        type: type,
                        subtype: type === 'ambiente' ? this.roomSubtype : (type === 'hardware' ? this.hwType : null),
                        name: this.name || (type === 'hardware' ? this.hwType.toUpperCase() : (this.roomSubtype?.toUpperCase() || type.toUpperCase())),
                        x: 100, y: 100,
                        w: type === 'hardware' ? 50 : (type === 'pasillo' ? 300 : (type === 'puerta' ? 40 : 120)),
                        h: type === 'hardware' ? 40 : (type === 'pasillo' ? 60 : (type === 'puerta' ? 40 : 100)),
                        rot: 0,
                        attrs: {...this.attrs}
                    };
                    this.elements.push(newEl);
                    this.selectedId = newEl.id;
                    this.name = '';
                    this.draw();
                },

                draw() {
                    if (!ctx || !canvas) return;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    // Grid Rendering
                    ctx.strokeStyle = '#f1f5f9';
                    ctx.lineWidth = 1;
                    for (let x = 0; x < canvas.width; x += 40) {
                        ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, canvas.height); ctx.stroke();
                    }
                    for (let y = 0; y < canvas.height; y += 40) {
                        ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(canvas.width, y); ctx.stroke();
                    }

                    if (this.layers.network) this.drawConnections();

                    this.elements.forEach(el => {
                        ctx.save();
                        ctx.translate(el.x + el.w / 2, el.y + el.h / 2);
                        ctx.rotate((el.rot || 0) * Math.PI / 180);
                        ctx.translate(-(el.x + el.w / 2), -(el.y + el.h / 2));

                        let fill = '#ffffff', stroke = '#475569';
                        const type = (el.type || '').toLowerCase();
                        const subtype = (el.subtype || '').toLowerCase();

                        if (type === 'ambiente') {
                            switch(subtype) {
                                case 'consultorio': fill = '#bbf7d0'; stroke = '#16a34a'; break;
                                case 'emergencias': fill = '#fecaca'; stroke = '#dc2626'; break;
                                case 'quirofano': fill = '#bae6fd'; stroke = '#0284c7'; break;
                                case 'administracion': fill = '#e9d5ff'; stroke = '#9333ea'; break;
                                case 'baño': fill = '#cffafe'; stroke = '#0891b2'; break;
                                default: fill = '#f1f5f9'; stroke = '#94a3b8'; break;
                            }
                        } else if (type === 'pasillo') { fill = '#f8fafc'; stroke = '#64748b'; }
                        else if (type === 'hardware') { fill = '#dbeafe'; stroke = '#2563eb'; }

                        ctx.shadowBlur = el.id === this.selectedId ? 15 : 4;
                        ctx.shadowColor = el.id === this.selectedId ? 'rgba(79, 70, 229, 0.4)' : 'rgba(0,0,0,0.1)';
                        ctx.shadowOffsetY = 2;

                        ctx.lineWidth = el.id === this.selectedId ? 4 : 2.5;
                        ctx.strokeStyle = el.id === this.selectedId ? '#fbbf24' : stroke;
                        ctx.fillStyle = fill;

                        this.drawRoundedRect(el.x, el.y, el.w, el.h, type === 'hardware' ? 20 : 6);
                        ctx.fill(); ctx.stroke();
                        
                        ctx.shadowBlur = 0; ctx.shadowOffsetY = 0;

                        if (type === 'hardware') this.drawHardwareSymbol(el);
                        else if (type === 'puerta') this.drawDoorSymbol(el);
                        else {
                            if (this.layers.furniture) this.drawFurnitureIcons(el);
                            if (this.layers.network || this.layers.power) this.drawServiceIcons(el);
                        }

                        ctx.fillStyle = type === 'hardware' ? '#2563eb' : '#1e293b';
                        ctx.font = 'bold 10px Inter, Arial'; ctx.textAlign = 'center';
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

                drawConnections() {
                    if (!ctx) return;
                    ctx.strokeStyle = '#3b82f6'; ctx.setLineDash([5, 5]); ctx.lineWidth = 2;
                    this.connections.forEach(conn => {
                        const el1 = this.elements.find(e => e.id === conn.from);
                        const el2 = this.elements.find(e => e.id === conn.to);
                        if (el1 && el2) {
                            ctx.beginPath(); ctx.moveTo(el1.x+el1.w/2, el1.y+el1.h/2); ctx.lineTo(el2.x+el2.w/2, el2.y+el2.h/2); ctx.stroke();
                        }
                    });
                    if (this.isConnecting && this.connectionStart) {
                        const startEl = this.elements.find(e => e.id === this.connectionStart);
                        if (startEl) {
                            const canvasX = this.mouseX - (canvas.offsetLeft || 0);
                            const canvasY = this.mouseY - (canvas.offsetTop || 0);
                            ctx.beginPath(); ctx.moveTo(startEl.x+startEl.w/2, startEl.y+startEl.h/2); ctx.lineTo(canvasX, canvasY); ctx.stroke();
                        }
                    }
                    ctx.setLineDash([]);
                },

                drawFurnitureIcons(el) {
                    ctx.strokeStyle = '#94a3b8'; ctx.lineWidth = 1;
                    if (el.subtype === 'consultorio') {
                        ctx.strokeRect(el.x + 10, el.y + 30, el.w - 20, 25);
                        ctx.beginPath(); ctx.arc(el.x + el.w/2, el.y + 70, 5, 0, Math.PI*2); ctx.stroke();
                    }
                },

                drawServiceIcons(el) {
                    let currentX = el.x + 10; const currentY = el.y + el.h - 15;
                    if (el.attrs?.wifi && this.layers.network) { 
                        ctx.fillStyle = '#2563eb'; ctx.beginPath(); ctx.arc(currentX, currentY, 6, 0, Math.PI*2); ctx.fill(); 
                        ctx.strokeStyle = 'white'; ctx.lineWidth = 1.5; ctx.stroke();
                        currentX += 18; 
                    }
                    if (el.attrs?.light && this.layers.power) { 
                        ctx.fillStyle = '#d97706'; ctx.beginPath(); ctx.arc(currentX, currentY, 6, 0, Math.PI*2); ctx.fill(); 
                        ctx.strokeStyle = 'white'; ctx.lineWidth = 1.5; ctx.stroke();
                    }
                },

                drawHardwareSymbol(el) {
                    ctx.strokeStyle = '#2563eb'; ctx.lineWidth = 2.5; const cx = el.x + el.w/2; const cy = el.y + el.h/2;
                    if (el.subtype === 'router') { 
                        ctx.strokeRect(cx - 15, cy - 8, 30, 16); 
                        ctx.beginPath(); ctx.moveTo(cx-10, cy-8); ctx.lineTo(cx-12, cy-18); ctx.stroke(); 
                        ctx.beginPath(); ctx.moveTo(cx+10, cy-8); ctx.lineTo(cx+12, cy-18); ctx.stroke(); 
                    }
                    else if (el.subtype === 'ap') { 
                        ctx.beginPath(); ctx.arc(cx, cy, 10, 0, Math.PI*2); ctx.stroke(); 
                        ctx.beginPath(); ctx.arc(cx, cy, 12, -Math.PI/4, Math.PI/4); ctx.stroke();
                        ctx.beginPath(); ctx.arc(cx, cy, 15, -Math.PI/4, Math.PI/4); ctx.stroke();
                        ctx.beginPath(); ctx.arc(cx, cy, 5, 0, Math.PI*2); ctx.fill(); 
                    }
                },

                drawDoorSymbol(el) {
                    ctx.strokeStyle = '#475569'; ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(el.x, el.y); ctx.lineTo(el.x + el.w, el.y);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.setLineDash([2, 2]);
                    ctx.arc(el.x, el.y, el.w, 0, Math.PI/2);
                    ctx.stroke();
                    ctx.setLineDash([]);
                    ctx.beginPath();
                    ctx.moveTo(el.x, el.y); ctx.lineTo(el.x, el.y + el.w);
                    ctx.stroke();
                },

                handleMouseDown(e) {
                    const rect = canvas.getBoundingClientRect();
                    const x = (e.clientX - rect.left);
                    const y = (e.clientY - rect.top);
                    if (this.tool === 'red') {
                        const clickedEl = this.elements.find(el => x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h);
                        if (clickedEl) { this.isConnecting = true; this.connectionStart = clickedEl.id; return; }
                    }
                    for (let i = this.elements.length - 1; i >= 0; i--) {
                        const el = this.elements[i];
                        if (x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h) {
                            this.selectedId = el.id; 
                            isDragging = true; dragTarget = el; offset.x = x - el.x; offset.y = y - el.y; 
                            this.$nextTick(() => window.lucide && window.lucide.createIcons());
                            this.draw(); return;
                        }
                    }
                    this.selectedId = null; this.draw();
                },

                handleMouseMove(e) {
                    const canvasRect = canvas ? canvas.getBoundingClientRect() : null;
                    const containerRect = document.getElementById('canvas-container').getBoundingClientRect();
                    
                    this.mouseX = e.clientX - containerRect.left;
                    this.mouseY = e.clientY - containerRect.top;

                    if (canvasRect) {
                        const canvasX = e.clientX - canvasRect.left;
                        const canvasY = e.clientY - canvasRect.top;
                        this.checkHover(canvasX, canvasY);

                        if (this.isConnecting) { this.draw(); return; }
                        if (isDragging && dragTarget) {
                            dragTarget.x = Math.round((canvasX - offset.x) / 10) * 10;
                            dragTarget.y = Math.round((canvasY - offset.y) / 10) * 10;
                            this.draw();
                        }
                    }
                },

                handleMouseUp(e) {
                    if (this.isConnecting && this.connectionStart) {
                        const rect = canvas.getBoundingClientRect();
                        const x = (e.clientX - rect.left);
                        const y = (e.clientY - rect.top);
                        const endEl = this.elements.find(el => x >= el.x && x <= el.x + el.w && y >= el.y && y <= el.y + el.h);
                        if (endEl && endEl.id !== this.connectionStart) { this.connections.push({ from: this.connectionStart, to: endEl.id }); }
                        this.isConnecting = false; this.connectionStart = null; this.draw();
                    }
                    isDragging = false; dragTarget = null;
                },

                resizeSelected(dw, dh) {
                    const el = this.elements.find(e => e.id === this.selectedId);
                    if (el) { el.w = Math.max(20, el.w + dw); el.h = Math.max(20, el.h + dh); this.draw(); }
                },

                rotateSelected() {
                    const el = this.elements.find(e => e.id === this.selectedId);
                    if (el) { el.rot = (el.rot + 90) % 360; this.draw(); }
                },

                deleteSelected() {
                    this.elements = this.elements.filter(e => e.id !== this.selectedId);
                    this.connections = this.connections.filter(c => c.from !== this.selectedId && c.to !== this.selectedId);
                    this.selectedId = null; this.draw();
                },

                async saveData() {
                    try {
                        const res = await fetch("{{ route('usuario.monitoreo.infraestructura-3d.store', $acta->id) }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ contenido: { elementos: this.elements, conexiones: this.connections } })
                        });
                        if (res.ok) { Swal.fire({ title: '¡Proyecto Guardado!', text: 'El croquis se ha actualizado con éxito.', icon: 'success', confirmButtonColor: '#4f46e5' }); }
                    } catch (e) { Swal.fire('Error', 'No se pudo guardar: ' + e.message, 'error'); }
                }
            };
        };
    </script>

    <div class="h-screen flex flex-col bg-slate-100 overflow-hidden font-sans" x-data="tabletEditor()">
        <!-- Barra Superior Tipo Tablet -->
        <div x-show="panelVisible" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-y-full opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="-translate-y-full opacity-0"
             class="bg-white border-b border-slate-200 px-4 py-2 flex items-center justify-between shadow-sm z-30">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center text-slate-600 transition-colors">
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
                <!-- Capas Toggle -->
                <div class="flex items-center gap-4 px-4 py-2 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" x-model="layers.furniture" class="rounded text-indigo-600">
                        <span class="text-[8px] font-black uppercase text-slate-500 group-hover:text-indigo-600 transition-colors">Mobiliario</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" x-model="layers.network" class="rounded text-blue-600">
                        <span class="text-[8px] font-black uppercase text-slate-500 group-hover:text-blue-600 transition-colors">Internet</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" x-model="layers.power" class="rounded text-amber-500">
                        <span class="text-[8px] font-black uppercase text-slate-500 group-hover:text-amber-500 transition-colors">Energía</span>
                    </label>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="saveData()" class="px-6 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 transition-all flex items-center gap-2 shadow-lg shadow-slate-200">
                        <i data-lucide="save" class="w-4 h-4"></i> Guardar
                    </button>
                    <button @click="panelVisible = false" class="w-10 h-10 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center transition-all" title="Minimizar herramientas">
                        <i data-lucide="minimize-2" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 flex overflow-hidden bg-slate-100 relative">
            <div class="flex-1 bg-[#f1f5f9] overflow-hidden relative flex flex-col p-0" id="canvas-container" @click.self="selectedId = null; draw()">
                
                <!-- Botón Flotante para Restaurar Herramientas -->
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

                <!-- Panel de Propiedades Flotante (Overlays Canvas) -->
                <div x-show="sidebarOpen && panelVisible" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="-translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="-translate-x-full opacity-0"
                     class="absolute top-4 left-4 w-72 bg-white/90 backdrop-blur-xl border border-white/20 flex flex-col p-5 shadow-2xl z-40 rounded-3xl overflow-y-auto max-h-[calc(100vh-120px)]">
                    
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

                                <input type="text" x-model="name" placeholder="NOMBRE..." 
                                       class="w-full bg-white border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-300">
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click="attrs.wifi = !attrs.wifi" :class="attrs.wifi ? 'bg-blue-600 text-white shadow-indigo-200' : 'bg-white text-slate-400'" class="p-4 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm group">
                                        <i data-lucide="wifi" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-[8px] font-black uppercase">Wifi</span>
                                    </button>
                                    <button @click="attrs.light = !attrs.light" :class="attrs.light ? 'bg-amber-500 text-white shadow-amber-200' : 'bg-white text-slate-400'" class="p-4 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm group">
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
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <button @click="hwType = 'router'" :class="hwType === 'router' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-400'" class="p-4 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm">
                                    <i data-lucide="router" class="w-5 h-5"></i>
                                    <span class="text-[8px] font-black uppercase">Router</span>
                                </button>
                                <button @click="hwType = 'ap'" :class="hwType === 'ap' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-400'" class="p-4 rounded-2xl flex flex-col items-center gap-2 transition-all shadow-sm">
                                    <i data-lucide="rss" class="w-5 h-5"></i>
                                    <span class="text-[8px] font-black uppercase">Access Point</span>
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
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <button @click="resizeSelected(20, 0)" class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">+ Ancho</button>
                                <button @click="resizeSelected(-20, 0)" class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">- Ancho</button>
                                <button @click="resizeSelected(0, 20)" class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">+ Largo</button>
                                <button @click="resizeSelected(0, -20)" class="p-3 bg-slate-100 rounded-2xl text-[8px] font-black uppercase hover:bg-slate-200 transition-all">- Largo</button>
                            </div>
                            <button @click="rotateSelected()" class="w-full py-3 mb-2 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase flex items-center justify-center gap-2 hover:bg-slate-800 transition-all">
                                <i data-lucide="rotate-cw" class="w-4 h-4"></i> Rotar 90°
                            </button>
                            <button @click="deleteSelected()" class="w-full py-3 bg-rose-50 text-rose-600 rounded-2xl text-[10px] font-black uppercase flex items-center justify-center gap-2 hover:bg-rose-100 transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                            </button>
                        </div>
                    </template>
                    </div> <!-- End tools-content -->
                </div> <!-- End sidebarOpen Panel -->
                
                <canvas id="blueprint-canvas" class="mx-auto block shadow-xl bg-white rounded-lg transition-all z-20"
                        @mousedown="handleMouseDown" @mousemove="handleMouseMove" @mouseup="handleMouseUp"
                        @contextmenu.prevent="deleteSelected()"></canvas>

                <!-- Floating Info Panel (Tooltip) -->
                <div x-show="hoveredEl" 
                     :style="`left: ${mouseX + 20}px; top: ${mouseY + 20}px`"
                     class="absolute z-50 bg-white/90 backdrop-blur shadow-2xl border border-slate-200 rounded-2xl p-4 pointer-events-none transition-all w-48">
                    <template x-if="hoveredEl">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9px] font-black uppercase text-indigo-600" x-text="hoveredEl.subtype || hoveredEl.type"></span>
                                <div class="flex gap-1" x-show="layers.network || layers.power">
                                    <div x-show="hoveredEl.attrs?.wifi && layers.network" class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    <div x-show="hoveredEl.attrs?.light && layers.power" class="w-2 h-2 rounded-full bg-amber-500"></div>
                                </div>
                            </div>
                            <h3 class="text-xs font-bold text-slate-800 mb-1" x-text="hoveredEl.name || 'Sin nombre'"></h3>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

@endsection
