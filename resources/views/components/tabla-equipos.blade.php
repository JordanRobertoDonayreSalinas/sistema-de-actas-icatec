@props(['equipos' => [], 'modulo' => '', 'esHistorico' => false])

<div class="bg-white border {{ $esHistorico ? 'border-amber-200 shadow-amber-50' : 'border-slate-200 shadow-sm' }} rounded-[2.5rem] overflow-hidden transition-all hover:shadow-lg group/container">
    {{-- CABECERA --}}
    <div class="bg-slate-50 border-b border-slate-100 px-8 py-5 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="h-10 w-10 rounded-2xl bg-white shadow-sm flex items-center justify-center text-indigo-600 border border-slate-100">
                <i data-lucide="monitor-speaker" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] block leading-none">Inventario de Equipamiento</span>
                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 tracking-tighter">Gestión de activos tecnológicos</p>
            </div>
        </div>
        
        <button type="button" onclick="addEquipRow('{{$modulo}}')" 
                class="group flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg active:scale-95">
            <i data-lucide="plus-circle" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300"></i> 
            Añadir Equipo
        </button>
    </div>

    <div class="overflow-x-auto custom-scroll">
        <table class="w-full border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/30">
                    <th class="px-8 py-4 text-left">Descripción del Hardware</th>
                    <th class="px-4 py-4 text-left">N° de Serie / QR</th>
                    <th class="px-4 py-4 text-center" width="80">Cant.</th>
                    <th class="px-4 py-4 text-left" width="150">Estado</th>
                    <th class="px-4 py-4 text-left" width="130">Propiedad</th>
                    <th class="px-4 py-4 text-left">Observaciones</th>
                    <th class="px-6 py-4 text-right" width="60"></th>
                </tr>
            </thead>
            <tbody id="body_equipos_{{ $modulo }}" class="divide-y divide-slate-50">
                @forelse($equipos as $index => $eq)
                    <tr class="hover:bg-slate-50/50 transition-colors group/row">
                        <td class="px-8 py-4">
                            <input type="text" name="equipos[{{ $index }}][descripcion]" value="{{ $eq->descripcion }}" 
                                   class="input-table-text" required list="list_equipos_master" placeholder="Seleccione...">
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <input type="text" id="serie_{{ $modulo }}_{{ $index }}" name="equipos[{{ $index }}][nro_serie]" value="{{ $eq->nro_serie }}" 
                                       class="input-table-text font-mono text-indigo-600 font-bold" placeholder="S/N o QR">
                                <button type="button" onclick="openScanner('serie_{{ $modulo }}_{{ $index }}')" 
                                        class="h-9 w-9 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                    <i data-lucide="scan-line" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <input type="hidden" name="equipos[{{ $index }}][cantidad]" value="1">
                            <span class="text-xs font-black text-slate-400">1</span>
                        </td>
                        <td class="px-4 py-4">
                            <select name="equipos[{{ $index }}][estado]" class="input-table-select">
                                <option value="OPERATIVO" {{ $eq->estado == 'OPERATIVO' ? 'selected' : '' }}>OPERATIVO</option>
                                <option value="REGULAR" {{ $eq->estado == 'REGULAR' ? 'selected' : '' }}>REGULAR</option>
                                <option value="INOPERATIVO" {{ $eq->estado == 'INOPERATIVO' ? 'selected' : '' }}>INOPERATIVO</option>
                            </select>
                        </td>
                        <td class="px-4 py-4">
                            {{-- CORRECCIÓN: 'propio' en minúsculas para coincidir con el controlador --}}
                            <select name="equipos[{{ $index }}][propio]" class="input-table-select">
                                <option value="ESTABLECIMIENTO" {{ $eq->propio == 'ESTABLECIMIENTO' ? 'selected' : '' }}>ESTABLECIMIENTO</option>
                                <option value="PERSONAL" {{ $eq->propio == 'PERSONAL' ? 'selected' : '' }}>PERSONAL</option>
                                <option value="SERVICIO" {{ $eq->propio == 'SERVICIO' ? 'selected' : '' }}>SERVICIO</option>
                            </select>
                        </td>
                        <td class="px-4 py-4">
                            <input type="text" name="equipos[{{ $index }}][observaciones]" value="{{ $eq->observaciones }}" 
                                   class="input-table-text uppercase">
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" onclick="removeRow(this, '{{ $modulo }}')" class="text-slate-300 hover:text-red-500 transition-all opacity-0 group-hover/row:opacity-100">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr id="no_data_{{ $modulo }}">
                        <td colspan="7" class="py-16 text-center text-slate-400 text-xs font-bold uppercase italic tracking-widest">Sin registros de hardware</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- DATALIST PARA SUGERENCIAS --}}
<datalist id="list_equipos_master">
    <option value="CPU">
    <option value="IMPRESORA">
    <option value="LAPTOP">
    <option value="LECTOR DE DNIe">
    <option value="MONITOR">
    <option value="MOUSE">
    <option value="SCANNER">   
    <option value="TABLET">
    <option value="TECLADO">
    <option value="TICKETERA">
</datalist>

{{-- MODAL SCANNER --}}
<div id="modal_scanner" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/95 backdrop-blur-md p-4">
    <div class="bg-white rounded-[3rem] w-full max-w-md overflow-hidden shadow-2xl">
        <div class="p-6 flex justify-between items-center bg-slate-50 border-b">
            <h3 class="text-xs font-black uppercase tracking-widest text-slate-700">Scanner Inteligente</h3>
            <button type="button" onclick="stopScanner()" class="text-slate-400 hover:text-red-500 transition-colors">
                <i data-lucide="x-circle" class="w-7 h-7"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="reader" style="width: 100%;" class="rounded-[2rem] overflow-hidden bg-black aspect-square"></div>
            <p class="mt-6 text-[10px] font-black text-slate-400 text-center uppercase tracking-[0.2em]">Enfoque el código con la cámara</p>
        </div>
    </div>
</div>

<script>
    let html5QrCode = null;
    let currentInputId = null;

    async function openScanner(id) {
        currentInputId = id;
        const modal = document.getElementById('modal_scanner');
        modal.classList.remove('hidden');

        if (html5QrCode) {
            try { await html5QrCode.stop(); } catch (e) {}
            html5QrCode = null;
        }

        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 20, qrbox: { width: 250, height: 180 }, aspectRatio: 1.0 };

        try {
            await html5QrCode.start(
                { facingMode: "environment" }, 
                config,
                (decodedText) => {
                    document.getElementById(currentInputId).value = decodedText.trim().toUpperCase();
                    if (navigator.vibrate) navigator.vibrate(100);
                    stopScanner();
                }
            );
        } catch (err) {
            alert("Error: Active los permisos de cámara o use HTTPS.");
            modal.classList.add('hidden');
        }
    }

    async function stopScanner() {
        document.getElementById('modal_scanner').classList.add('hidden');
        if (html5QrCode) {
            try {
                if (html5QrCode.isScanning) await html5QrCode.stop();
                html5QrCode.clear();
            } catch (err) {}
        }
    }

    function addEquipRow(modulo) {
        const body = document.getElementById('body_equipos_' + modulo);
        const noData = document.getElementById('no_data_' + modulo);
        if (noData) noData.remove();
        const index = body.querySelectorAll('tr').length;
        const rowId = `serie_${modulo}_${Date.now()}`;
        const row = document.createElement('tr');
        row.className = "hover:bg-slate-50/50 transition-colors group/row";
        row.innerHTML = `
            <td class="px-8 py-4"><input type="text" name="equipos[${index}][descripcion]" class="input-table-text" required list="list_equipos_master" placeholder="Seleccione..."></td>
            <td class="px-4 py-4">
                <div class="flex items-center gap-2">
                    <input type="text" id="${rowId}" name="equipos[${index}][nro_serie]" class="input-table-text font-mono font-bold" placeholder="S/N o QR">
                    <button type="button" onclick="openScanner('${rowId}')" class="h-9 w-9 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                        <i data-lucide="scan-line" class="w-5 h-5"></i>
                    </button>
                </div>
            </td>
            <td class="px-4 py-4 text-center"><span class="text-xs font-black text-slate-400">1</span></td>
            <td class="px-4 py-4">
                <select name="equipos[${index}][estado]" class="input-table-select">
                    <option value="OPERATIVO">OPERATIVO</option>
                    <option value="REGULAR">REGULAR</option>
                    <option value="INOPERATIVO">INOPERATIVO</option>
                </select>
            </td>
            <td class="px-4 py-4">
                {{-- CORRECCIÓN: 'propio' en minúsculas en el JS también --}}
                <select name="equipos[${index}][propio]" class="input-table-select">
                    <option value="ESTABLECIMIENTO">ESTABLECIMIENTO</option>
                    <option value="PERSONAL">PERSONAL</option>
                    <option value="SERVICIO">SERVICIO</option>
                </select>
            </td>
            <td class="px-4 py-4"><input type="text" name="equipos[${index}][observaciones]" class="input-table-text uppercase"></td>
            <td class="px-6 py-4 text-right">
                <button type="button" onclick="removeRow(this, '${modulo}')" class="h-8 w-8 text-slate-300 hover:text-red-500 transition-all opacity-0 group-hover/row:opacity-100">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        body.appendChild(row);
        if (window.lucide) window.lucide.createIcons();
    }

    function removeRow(btn, modulo) {
        btn.closest('tr').remove();
    }
</script>

<style>
    .input-table-text {
        width: 100%; background: transparent; border: none; border-bottom: 2px solid #f1f5f9;
        padding: 4px 6px; font-size: 0.75rem; font-weight: 700; color: #334155;
        outline: none; transition: all 0.3s;
    }
    .input-table-text:focus { border-bottom-color: #6366f1; }
    
    .input-table-select {
        width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;
        padding: 4px 8px; font-size: 0.7rem; font-weight: 800; color: #475569;
        outline: none; cursor: pointer; transition: all 0.3s;
    }
    .input-table-select:focus { border-color: #6366f1; background: white; }

    #reader { position: relative; }
    #reader::after {
        content: ""; position: absolute; top: 50%; left: 0; width: 100%; height: 2px;
        background: rgba(239, 68, 68, 0.7); box-shadow: 0 0 8px red; z-index: 10;
        animation: scanLine 2s linear infinite;
    }
    @keyframes scanLine {
        0% { top: 20%; opacity: 0; }
        50% { top: 50%; opacity: 1; }
        100% { top: 80%; opacity: 0; }
    }
</style>