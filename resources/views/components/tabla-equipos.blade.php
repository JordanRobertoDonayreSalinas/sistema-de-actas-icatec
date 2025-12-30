@props(['equipos' => [], 'modulo' => '', 'esHistorico' => false])

<div class="bg-white border {{ $esHistorico ? 'border-amber-200 shadow-amber-50' : 'border-slate-200 shadow-sm' }} rounded-[2.5rem] overflow-hidden transition-all hover:shadow-lg group/container">
    
    {{-- AVISO DE DATOS HISTÓRICOS (Se activa solo si esHistorico es true y hay datos) --}}
    @if($esHistorico && count($equipos) > 0)
        <div class="bg-amber-50 border-b border-amber-100 px-8 py-3 flex items-center justify-between animate-pulse-slow">
            <div class="flex items-center gap-3">
                <div class="h-6 w-6 rounded-full bg-amber-500 flex items-center justify-center text-white">
                    <i data-lucide="history" class="w-3.5 h-3.5"></i>
                </div>
                <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">
                    Sugerencia: Se cargaron equipos del monitoreo anterior. Verifique y guarde para confirmar.
                </p>
            </div>
            <span class="text-[9px] font-bold text-amber-500 bg-white px-3 py-1 rounded-full border border-amber-200 uppercase">Referencia</span>
        </div>
    @endif

    {{-- CABECERA --}}
    <div class="bg-slate-50 border-b border-slate-100 px-8 py-5 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="h-10 w-10 rounded-2xl bg-white shadow-sm flex items-center justify-center text-indigo-600 border border-slate-100">
                <i data-lucide="monitor-speaker" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] block leading-none">Inventario de Equipamiento</span>
                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Gestión de activos tecnológicos</p>
            </div>
        </div>
        
        <button type="button" onclick="addEquipRow('{{$modulo}}')" 
                class="group flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg active:scale-95">
            <i data-lucide="plus-circle" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300"></i> 
            Añadir Equipo
        </button>
    </div>

    {{-- DATALIST DE SUGERENCIAS --}}
    <datalist id="list_equipos_master">
        <option value="CPU">
        <option value="MONITOR">
        <option value="TECLADO">
        <option value="MOUSE">
        <option value="LECTOR DNIe">
        <option value="IMPRESORA">
        <option value="LAPTOP">
        <option value="TICKETERA">
    </datalist>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/30">
                    <th class="px-8 py-4 text-left">Descripción del Hardware</th>
                    <th class="px-4 py-4 text-left">N° de Serie</th>
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
                                   class="input-table-text {{ $esHistorico ? 'text-amber-600 font-bold border-amber-100' : '' }}" 
                                   required list="list_equipos_master" placeholder="Seleccione o escriba...">
                        </td>
                        <td class="px-4 py-4">
                            <input type="text" name="equipos[{{ $index }}][nro_serie]" value="{{ $eq->nro_serie }}" 
                                   class="input-table-text {{ $esHistorico ? 'border-amber-100' : '' }}" placeholder="S/N">
                        </td>
                        <td class="px-4 py-4 text-center">
                            <input type="hidden" name="equipos[{{ $index }}][cantidad]" value="1">
                            <span class="text-xs font-black {{ $esHistorico ? 'text-amber-500' : 'text-slate-400' }}">1</span>
                        </td>
                        <td class="px-4 py-4">
                            <select name="equipos[{{ $index }}][estado]" class="select-table-custom {{ $esHistorico ? 'border-amber-200 bg-amber-50/30' : '' }}">
                                <option value="BUENO" {{ $eq->estado == 'BUENO' ? 'selected' : '' }}>🟢 BUENO</option>
                                <option value="REGULAR" {{ $eq->estado == 'REGULAR' ? 'selected' : '' }}>🟡 REGULAR</option>
                                <option value="MALO" {{ $eq->estado == 'MALO' ? 'selected' : '' }}>🔴 MALO</option>
                            </select>
                        </td>
                        <td class="px-4 py-4">
                            <select name="equipos[{{ $index }}][propio]" class="select-table-custom {{ $esHistorico ? 'border-amber-200 bg-amber-50/30' : '' }}">
                                <option value="SI" {{ $eq->propio == 'SI' ? 'selected' : '' }}>SI</option>
                                <option value="NO" {{ $eq->propio == 'NO' ? 'selected' : '' }}>NO</option>
                            </select>
                        </td>
                        <td class="px-4 py-4">
                            <input type="text" name="equipos[{{ $index }}][observacion]" value="{{ $eq->observacion }}" 
                                   class="input-table-text text-[10px] {{ $esHistorico ? 'border-amber-100' : '' }}" placeholder="...">
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" onclick="removeRow(this, '{{ $modulo }}')" 
                                    class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover/row:opacity-100">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr id="no_data_{{ $modulo }}">
                        <td colspan="7" class="py-16 text-center text-slate-400 text-xs font-bold uppercase italic tracking-widest">
                            Sin registros de hardware
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function addEquipRow(modulo) {
        const body = document.getElementById('body_equipos_' + modulo);
        const noData = document.getElementById('no_data_' + modulo);
        if (noData) noData.remove();

        // Calcular index basado en la cantidad de filas reales
        const index = body.querySelectorAll('tr').length;
        const row = document.createElement('tr');
        row.className = "hover:bg-slate-50/50 transition-colors group/row";
        
        row.innerHTML = `
            <td class="px-8 py-4">
                <input type="text" name="equipos[${index}][descripcion]" class="input-table-text" required list="list_equipos_master" placeholder="Seleccione o escriba...">
            </td>
            <td class="px-4 py-4">
                <input type="text" name="equipos[${index}][nro_serie]" class="input-table-text" placeholder="S/N">
            </td>
            <td class="px-4 py-4 text-center">
                <input type="hidden" name="equipos[${index}][cantidad]" value="1">
                <span class="text-xs font-black text-slate-400">1</span>
            </td>
            <td class="px-4 py-4">
                <select name="equipos[${index}][estado]" class="select-table-custom">
                    <option value="BUENO">🟢 BUENO</option>
                    <option value="REGULAR">🟡 REGULAR</option>
                    <option value="MALO">🔴 MALO</option>
                </select>
            </td>
            <td class="px-4 py-4">
                <select name="equipos[${index}][propio]" class="select-table-custom">
                    <option value="SI">INSTITUCIONAL</option>
                    <option value="NO">PERSONAL</option>
                </select>
            </td>
            <td class="px-4 py-4">
                <input type="text" name="equipos[${index}][observacion]" class="input-table-text text-[10px]" placeholder="...">
            </td>
            <td class="px-6 py-4 text-right">
                <button type="button" onclick="removeRow(this, '${modulo}')" 
                        class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 transition-all opacity-0 group-hover/row:opacity-100">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        body.appendChild(row);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeRow(btn, modulo) {
        btn.closest('tr').remove();
        const body = document.getElementById('body_equipos_' + modulo);
        if (body.querySelectorAll('tr').length === 0) {
            body.innerHTML = `<tr id="no_data_${modulo}"><td colspan="7" class="py-16 text-center text-slate-400 text-xs font-bold uppercase italic tracking-widest">Sin registros de hardware</td></tr>`;
        }
    }
</script>

<style>
    .input-table-text {
        width: 100%;
        background-color: transparent;
        border: none;
        border-bottom: 2px solid #f1f5f9;
        padding: 4px 0;
        font-size: 0.75rem;
        font-weight: 700;
        color: #334155;
        outline: none;
        transition: all 0.3s;
    }
    .input-table-text:focus {
        border-bottom-color: #6366f1;
    }
    .select-table-custom {
        width: 100%;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 4px 8px;
        font-size: 10px;
        font-weight: 800;
        color: #475569;
        outline: none;
        cursor: pointer;
    }
    @keyframes pulse-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    .animate-pulse-slow { animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
</style>