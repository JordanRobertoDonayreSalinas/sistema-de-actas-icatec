@props(['equipos' => [], 'modulo' => ''])

<div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm transition-all hover:shadow-lg group/container">
    {{-- CABECERA PROFESIONAL --}}
    <div class="bg-slate-50 border-b border-slate-100 px-8 py-5 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="h-10 w-10 rounded-2xl bg-white shadow-sm flex items-center justify-center text-indigo-600 border border-slate-100">
                <i data-lucide="monitor-speaker" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] block leading-none">Inventario de Equipamiento</span>
                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Gestión de activos tecnológicos por área de evaluación</p>
            </div>
        </div>
        
        <button type="button" onclick="addEquipRow('{{$modulo}}')" 
                class="group flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95">
            <i data-lucide="plus-circle" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300"></i> 
            Añadir Equipo
        </button>
    </div>

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
                {{-- CARGA DE EQUIPOS EXISTENTES --}}
                @forelse($equipos as $index => $eq)
                    <tr class="hover:bg-slate-50/50 transition-colors group/row">
                        <td class="px-8 py-4">
                            <input type="text" name="equipos[{{ $index }}][descripcion]" value="{{ $eq->descripcion }}" 
                                   class="input-table-text" required list="list_equipos">
                        </td>
                        <td class="px-4 py-4">
                            <input type="text" name="equipos[{{ $index }}][nro_serie]" value="{{ $eq->nro_serie }}" 
                                   class="input-table-text placeholder:text-slate-300">
                        </td>
                        <td class="px-4 py-4">
                            <input type="number" name="equipos[{{ $index }}][cantidad]" value="{{ $eq->cantidad }}" 
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-2 py-2 text-center font-black text-slate-700 text-xs focus:border-indigo-500 outline-none transition-all" min="1">
                        </td>
                        <td class="px-4 py-4">
                            <select name="equipos[{{ $index }}][estado]" 
                                    class="select-table-custom">
                                <option value="BUENO" {{ $eq->estado == 'BUENO' ? 'selected' : '' }}>🟢 BUENO</option>
                                <option value="REGULAR" {{ $eq->estado == 'REGULAR' ? 'selected' : '' }}>🟡 REGULAR</option>
                                <option value="MALO" {{ $eq->estado == 'MALO' ? 'selected' : '' }}>🔴 MALO</option>
                            </select>
                        </td>
                        <td class="px-4 py-4">
                            <select name="equipos[{{ $index }}][propio]" 
                                    class="select-table-custom">
                                <option value="SI" {{ $eq->propio == 'SI' ? 'selected' : '' }}>INSTITUCIONAL</option>
                                <option value="NO" {{ $eq->propio == 'NO' ? 'selected' : '' }}>PERSONAL</option>
                            </select>
                        </td>
                        <td class="px-4 py-4">
                            <input type="text" name="equipos[{{ $index }}][observacion]" value="{{ $eq->observacion }}" 
                                   class="input-table-text text-[10px]">
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
                        <td colspan="7" class="py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-slate-200 text-slate-300">
                                    <i data-lucide="monitor-off" class="w-8 h-8"></i>
                                </div>
                                <h4 class="text-slate-400 font-black text-xs uppercase tracking-widest">Sin registros de hardware</h4>
                                <p class="text-slate-300 text-[10px] font-bold mt-1 uppercase italic">Presione "Añadir Equipo" para registrar</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .input-table-text {
        width: 100%;
        background: transparent;
        border-bottom: 2px solid transparent;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        outline: none;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .input-table-text:focus { border-color: #6366f1; }
    
    .select-table-custom {
        width: 100%;
        background-color: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 0.75rem;
        padding: 0.5rem 0.75rem;
        font-weight: 700;
        color: #475569;
        font-size: 10px;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .select-table-custom:focus { border-color: #6366f1; background-color: white; }
</style>

@once
<script>
    function addEquipRow(mod) {
        const tbody = document.getElementById('body_equipos_' + mod);
        const noData = document.getElementById('no_data_' + mod);
        
        if (noData) noData.remove();

        const id = Date.now();
        const row = document.createElement('tr');
        row.className = "hover:bg-slate-50/50 transition-colors group/row animate-in fade-in slide-in-from-left-2 duration-500";
        row.innerHTML = `
            <td class="px-8 py-4">
                <input type="text" name="equipos[new_${id}][descripcion]" 
                       class="input-table-text" 
                       placeholder="EJ. IMPRESORA LASER HP" required list="list_equipos">
            </td>
            <td class="px-4 py-4">
                <input type="text" name="equipos[new_${id}][nro_serie]" 
                       class="input-table-text placeholder:text-slate-300" placeholder="S/N">
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" name="equipos[new_${id}][cantidad]" value="1" min="1"
                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-2 py-2 text-center font-black text-slate-700 text-xs focus:border-indigo-500 outline-none transition-all">
            </td>
            <td class="px-4 py-4">
                <select name="equipos[new_${id}][estado]" 
                        class="select-table-custom">
                    <option value="BUENO">🟢 BUENO</option>
                    <option value="REGULAR">🟡 REGULAR</option>
                    <option value="MALO">🔴 MALO</option>
                </select>
            </td>
            <td class="px-4 py-4">
                <select name="equipos[new_${id}][propio]" 
                        class="select-table-custom">
                    <option value="SI">INSTITUCIONAL</option>
                    <option value="NO">PERSONAL</option>
                </select>
            </td>
            <td class="px-4 py-4">
                <input type="text" name="equipos[new_${id}][observacion]" 
                       class="input-table-text text-[10px]" placeholder="...">
            </td>
            <td class="px-6 py-4 text-right">
                <button type="button" onclick="removeRow(this, '${mod}')" 
                        class="h-8 w-8 flex items-center justify-center mx-auto rounded-lg text-red-300 hover:text-red-500 hover:bg-red-50 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeRow(btn, mod) {
        const row = btn.closest('tr');
        row.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => {
            row.remove();
            const tbody = document.getElementById('body_equipos_' + mod);
            if(tbody.children.length === 0) {
               // Opcional: Reinsertar el estado vacío si se desea.
            }
        }, 300);
    }
</script>

<datalist id="list_equipos">
    <option value="MONITOR LED 24 P">
    <option value="CPU CORE I5 12GEN">
    <option value="LAPTOP CORPORATIVA">
    <option value="TECLADO MULTIMEDIA">
    <option value="MOUSE ÓPTICO USB">
    <option value="IMPRESORA MULTIFUNCIONAL">
    <option value="TICKETERA TÉRMICA">
    <option value="LECTOR DE DNI ELECTRÓNICO">
    <option value="ESTABILIZADOR 1000VA">
    <option value="SCANNER DE DOCUMENTOS">
</datalist>
@endonce