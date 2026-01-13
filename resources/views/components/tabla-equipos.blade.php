@props(['equipos' => [], 'modulo' => ''])

<div class="bg-white border border-slate-200 shadow-sm rounded-[2.5rem] overflow-hidden transition-all hover:shadow-lg group/container">
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
                    <th class="px-6 py-4 text-left">Descripción</th>
                    <th class="px-2 py-4 text-center" width="70">Cant.</th>
                    <th class="px-4 py-4 text-left" width="140">Estado</th>
                    <th class="px-4 py-4 text-left" width="130">Propiedad</th>
                    <th class="px-4 py-4 text-left" width="300">N.Serie / C.Pat</th>
                    <th class="px-4 py-4 text-left">Observación</th>
                    <th class="px-4 py-4 text-right" width="50"></th>
                </tr>
            </thead>
            <tbody id="body_equipos_{{ $modulo }}" class="divide-y divide-slate-50">
                @forelse($equipos as $index => $eq)
                    @php
                        // Lógica visual para Nro Serie
                        $fullValue = $eq->nro_serie ?? '';
                        $prefix = 'S'; 
                        $cleanValue = $fullValue;

                        if(str_starts_with($fullValue, 'S:')) {
                            $prefix = 'S';
                            $cleanValue = substr($fullValue, 2);
                        } elseif(str_starts_with($fullValue, 'CP:')) {
                            $prefix = 'CP';
                            $cleanValue = substr($fullValue, 3);
                        }
                    @endphp

                    <tr class="hover:bg-slate-50/50 transition-colors group/row" id="row_{{ $index }}">
                        {{-- 1. DESCRIPCION --}}
                        <td class="px-6 py-4">
                            <input type="text" name="equipos[{{ $index }}][descripcion]" value="{{ $eq->descripcion }}" 
                                   class="input-table-text" required list="list_equipos_master" placeholder="Seleccione...">
                        </td>

                        {{-- 2. CANTIDAD --}}
                        <td class="px-2 py-4 text-center">
                            <input type="number" name="equipos[{{ $index }}][cantidad]" value="{{ $eq->cantidad ?? 1 }}" 
                                   class="input-table-text text-center font-bold" min="1">
                        </td>

                        {{-- 3. ESTADO --}}
                        <td class="px-4 py-4">
                            <select name="equipos[{{ $index }}][estado]" class="input-table-select">
                                <option value="OPERATIVO" {{ $eq->estado == 'OPERATIVO' ? 'selected' : '' }}>OPERATIVO</option>
                                <option value="REGULAR" {{ $eq->estado == 'REGULAR' ? 'selected' : '' }}>REGULAR</option>
                                <option value="INOPERATIVO" {{ $eq->estado == 'INOPERATIVO' ? 'selected' : '' }}>INOPERATIVO</option>
                            </select>
                        </td>

                        {{-- 4. PROPIEDAD --}}
                        <td class="px-4 py-4">
                            <select name="equipos[{{ $index }}][propio]" class="input-table-select">
                                <option value="COMPARTIDO" {{ $eq->propio == 'COMPARTIDO' ? 'selected' : '' }}>COMPARTIDO</option>
                                <option value="EXCLUSIVO" {{ $eq->propio == 'EXCLUSIVO' ? 'selected' : '' }}>EXCLUSIVO</option>
                                <option value="PERSONAL" {{ $eq->propio == 'PERSONAL' ? 'selected' : '' }}>PERSONAL</option>
                            </select>
                        </td>

                        {{-- 5. N.SERIE / C.PAT --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1 bg-slate-100 rounded-xl p-1 border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                                <select id="prefix_{{ $index }}" onchange="updateCompositeSerial('{{ $index }}')" 
                                        class="bg-white text-[10px] font-black text-indigo-600 rounded-lg py-2 px-1 border-none focus:ring-0 cursor-pointer shadow-sm w-14 text-center shrink-0">
                                    <option value="S" {{ $prefix == 'S' ? 'selected' : '' }}>S</option>
                                    <option value="CP" {{ $prefix == 'CP' ? 'selected' : '' }}>CP</option>
                                </select>
                                <input type="text" id="visual_{{ $index }}" value="{{ $cleanValue }}" oninput="updateCompositeSerial('{{ $index }}')"
                                       class="w-full bg-transparent border-none text-xs font-bold text-slate-700 placeholder-slate-400 focus:ring-0 p-1 uppercase min-w-0" 
                                       placeholder="Digite...">
                                <input type="hidden" id="final_{{ $index }}" name="equipos[{{ $index }}][nro_serie]" value="{{ $fullValue }}">
                                <button type="button" onclick="openScannerForComposite('{{ $index }}')" 
                                        class="p-2 text-slate-400 hover:text-indigo-600 transition-colors shrink-0">
                                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>

                        {{-- 6. OBSERVACION (SINGULAR) --}}
                        <td class="px-4 py-4">
                            <input type="text" name="equipos[{{ $index }}][observacion]" value="{{ $eq->observacion }}" 
                                   class="input-table-text uppercase">
                        </td>
                        
                        {{-- ELIMINAR --}}
                        <td class="px-4 py-4 text-right">
                            <button type="button" onclick="removeRow(this)" class="text-slate-300 hover:text-red-500 transition-all opacity-0 group-hover/row:opacity-100">
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

{{-- DATALIST --}}
<datalist id="list_equipos_master">
    <option value="CPU"><option value="IMPRESORA"><option value="LAPTOP"><option value="LECTOR DE DNIe"><option value="MONITOR"><option value="MOUSE"><option value="SCANNER"><option value="TABLET"><option value="TECLADO"><option value="TICKETERA">
</datalist>

{{-- MODAL SCANNER --}}
<div id="modal_scanner" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/95 backdrop-blur-md p-4">
    <div class="bg-white rounded-[3rem] w-full max-w-md overflow-hidden shadow-2xl">
        <div class="p-6 flex justify-between items-center bg-slate-50 border-b">
            <h3 class="text-xs font-black uppercase tracking-widest text-slate-700">Scanner</h3>
            <button type="button" onclick="stopScanner()" class="text-slate-400 hover:text-red-500 transition-colors"><i data-lucide="x-circle" class="w-7 h-7"></i></button>
        </div>
        <div class="p-6">
            <div id="reader" style="width: 100%;" class="rounded-[2rem] overflow-hidden bg-black aspect-square"></div>
            <p class="mt-6 text-[10px] font-black text-slate-400 text-center uppercase tracking-[0.2em]">Enfoque el código</p>
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DE UNIÓN DE PREFIJO + VALOR ---
    function updateCompositeSerial(rowId) {
        const prefix = document.getElementById(`prefix_${rowId}`).value; 
        const visualValue = document.getElementById(`visual_${rowId}`).value.trim().toUpperCase(); 
        const finalInput = document.getElementById(`final_${rowId}`); 

        if (visualValue) {
            finalInput.value = `${prefix}:${visualValue}`;
        } else {
            finalInput.value = '';
        }
    }

    // --- ESCÁNER ---
    let html5QrCode = null;
    let currentRowIdForScan = null;

    async function openScannerForComposite(rowId) {
        currentRowIdForScan = rowId;
        const modal = document.getElementById('modal_scanner');
        modal.classList.remove('hidden');

        if (html5QrCode) { try { await html5QrCode.stop(); } catch (e) {} html5QrCode = null; }

        html5QrCode = new Html5Qrcode("reader");
        try {
            await html5QrCode.start({ facingMode: "environment" }, { fps: 20, qrbox: { width: 250, height: 180 } },
                (decodedText) => {
                    const val = decodedText.trim().toUpperCase();
                    document.getElementById(`visual_${currentRowIdForScan}`).value = val;
                    updateCompositeSerial(currentRowIdForScan); 
                    if (navigator.vibrate) navigator.vibrate(100);
                    stopScanner();
                });
        } catch (err) { alert("Active permisos de cámara."); modal.classList.add('hidden'); }
    }

    async function stopScanner() {
        document.getElementById('modal_scanner').classList.add('hidden');
        if (html5QrCode && html5QrCode.isScanning) { try { await html5QrCode.stop(); html5QrCode.clear(); } catch (err) {} }
    }

    // --- AGREGAR FILA ---
    function addEquipRow(modulo) {
        const body = document.getElementById(`body_equipos_${modulo}`);
        const noDataRow = document.getElementById(`no_data_${modulo}`);
        if (noDataRow) noDataRow.remove();

        const uniqueId = Date.now(); 

        const row = document.createElement('tr');
        row.className = 'group/row hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-none';
        
        row.innerHTML = `
            <td class="px-6 py-4">
                <input type="text" name="equipos[${uniqueId}][descripcion]" class="input-table-text" required list="list_equipos_master" placeholder="Seleccione...">
            </td>
            <td class="px-2 py-4 text-center">
                <input type="number" name="equipos[${uniqueId}][cantidad]" value="1" class="input-table-text text-center font-bold" min="1">
            </td>
            <td class="px-4 py-4">
                <select name="equipos[${uniqueId}][estado]" class="input-table-select">
                    <option value="OPERATIVO">OPERATIVO</option>
                    <option value="REGULAR">REGULAR</option>
                    <option value="INOPERATIVO">INOPERATIVO</option>
                </select>
            </td>
            <td class="px-4 py-4">
                <select name="equipos[${uniqueId}][propio]" class="input-table-select">
                    <option value="EXCLUSIVO">EXCLUSIVO</option>
                    <option value="COMPARTIDO">COMPARTIDO</option>
                    <option value="PERSONAL">PERSONAL</option>
                </select>
            </td>
            <td class="px-4 py-4">
                <div class="flex items-center gap-1 bg-slate-100 rounded-xl p-1 border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                    <select id="prefix_${uniqueId}" onchange="updateCompositeSerial('${uniqueId}')" 
                            class="bg-white text-[10px] font-black text-indigo-600 rounded-lg py-2 px-1 border-none focus:ring-0 cursor-pointer shadow-sm w-14 text-center shrink-0">
                        <option value="S">S</option>
                        <option value="CP">CP</option>
                    </select>
                    <input type="text" id="visual_${uniqueId}" oninput="updateCompositeSerial('${uniqueId}')"
                           class="w-full bg-transparent border-none text-xs font-bold text-slate-700 placeholder-slate-400 focus:ring-0 p-1 uppercase min-w-0" 
                           placeholder="Digite...">
                    <input type="hidden" id="final_${uniqueId}" name="equipos[${uniqueId}][nro_serie]">
                    <button type="button" onclick="openScannerForComposite('${uniqueId}')" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors shrink-0">
                        <i data-lucide="scan-line" class="w-4 h-4"></i>
                    </button>
                </div>
            </td>
            <td class="px-4 py-4">
                {{-- AQUÍ ESTÁ EL CAMBIO CRÍTICO: name="...[observacion]" (SINGULAR) --}}
                <input type="text" name="equipos[${uniqueId}][observacion]" class="input-table-text uppercase">
            </td>
            <td class="px-4 py-4 text-right">
                <button type="button" onclick="removeRow(this)" class="text-slate-300 hover:text-red-500 transition-all opacity-0 group-hover/row:opacity-100">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        
        body.appendChild(row);
        if (window.lucide) window.lucide.createIcons();
        row.querySelector('input[type="text"]').focus();
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        if(row) row.remove();
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