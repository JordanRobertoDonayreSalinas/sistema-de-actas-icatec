@props(['num', 'contenido', 'color' => 'indigo'])

<div class="monitoreo-section bg-white rounded-[2rem] p-8 shadow-lg border border-slate-100">
    <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
        @if(trim($num ?? '') !== '')
        <span
            class="section-number bg-{{ $color }}-600 text-white w-8 h-8 flex items-center justify-center rounded-full font-black text-sm">{{ $num }}</span>
        @endif
        <h3 class="text-{{ $color }}-900 font-black text-lg uppercase tracking-tight">TIPO DE CONECTIVIDAD</h3>
    </div>

    <input type="hidden" name="contenido[tipo_conectividad]" id="tipo_conectividad_input"
        value="{{ $contenido['tipo_conectividad'] ?? '' }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- TARJETA WIFI --}}
        <div id="card_wifi" onclick="selectConectividad('WIFI')"
            class="cursor-pointer border-2 rounded-2xl p-6 flex items-center gap-4 transition-all hover:shadow-md {{ ($contenido['tipo_conectividad'] ?? '') == 'WIFI' ? 'border-' . $color . '-600 bg-' . $color . '-50' : 'border-slate-200 bg-white' }}">
            <div
                class="h-12 w-12 rounded-xl bg-{{ $color }}-100 flex items-center justify-center text-{{ $color }}-600">
                <i data-lucide="wifi" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-sm font-black text-slate-800 uppercase">WIFI</h4>
                <span
                    class="text-[10px] font-bold text-{{ $color }}-500 bg-{{ $color }}-100 px-2 py-0.5 rounded uppercase">Inalámbrico</span>
            </div>
        </div>

        {{-- TARJETA CABLEADO --}}
        <div id="card_cableado" onclick="selectConectividad('CABLEADO')"
            class="cursor-pointer border-2 rounded-2xl p-6 flex items-center gap-4 transition-all hover:shadow-md {{ ($contenido['tipo_conectividad'] ?? '') == 'CABLEADO' ? 'border-' . $color . '-600 bg-' . $color . '-50' : 'border-slate-200 bg-white' }}">
            <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                <i data-lucide="cable" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-sm font-black text-slate-800 uppercase">CABLEADO</h4>
                <span
                    class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded uppercase">Ethernet</span>
            </div>
        </div>
    </div>

    {{-- SUB-OPCIÓN: WIFI DEL ESTABLECIMIENTO O PERSONAL --}}
    <input type="hidden" name="contenido[wifi_fuente]" id="wifi_fuente_input"
        value="{{ $contenido['wifi_fuente'] ?? '' }}">
    <div id="bloque_wifi_fuente"
        class="mt-6 bg-slate-50 rounded-2xl p-6 border border-slate-200 {{ ($contenido['tipo_conectividad'] ?? '') == 'WIFI' ? '' : 'hidden' }}">
        <label class="block text-{{ $color }}-600 text-[10px] font-black uppercase tracking-widest mb-4">¿De dónde
            proviene el WiFi?</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- ESTABLECIMIENTO --}}
            <div id="card_wifi_establecimiento" onclick="selectWifiFuente('ESTABLECIMIENTO')"
                class="cursor-pointer border-2 rounded-xl p-4 flex items-center gap-3 transition-all hover:shadow-md {{ ($contenido['wifi_fuente'] ?? '') == 'ESTABLECIMIENTO' ? 'border-' . $color . '-600 bg-' . $color . '-50' : 'border-slate-200 bg-white' }}">
                <div
                    class="h-10 w-10 rounded-lg bg-{{ $color }}-100 flex items-center justify-center text-{{ $color }}-600">
                    <i data-lucide="building-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase">Establecimiento</h4>
                    <span class="text-[9px] font-bold text-{{ $color }}-400">Red del EESS</span>
                </div>
            </div>
            {{-- PERSONAL --}}
            <div id="card_wifi_personal" onclick="selectWifiFuente('PERSONAL')"
                class="cursor-pointer border-2 rounded-xl p-4 flex items-center gap-3 transition-all hover:shadow-md {{ ($contenido['wifi_fuente'] ?? '') == 'PERSONAL' ? 'border-' . $color . '-600 bg-' . $color . '-50' : 'border-slate-200 bg-white' }}">
                <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase">Personal</h4>
                    <span class="text-[9px] font-bold text-slate-400">Hotspot / Propio</span>
                </div>
            </div>
        </div>
    </div>

    {{-- OPERADOR DE SERVICIO --}}
    <div class="mt-6">
        <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2">Operador de
            Servicio</label>
        <select name="contenido[operador_servicio]"
            class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl font-bold text-sm outline-none focus:border-{{ $color }}-500 transition-all uppercase cursor-pointer">
            <option value="" selected disabled>-- SELECCIONE --</option>
            @foreach(['WOW', 'MOVISTAR', 'ENTEL', 'CLARO', 'BITEL', 'OTROS'] as $op)
                <option value="{{ $op }}" {{ ($contenido['operador_servicio'] ?? '') == $op ? 'selected' : '' }}>
                    {{ $op }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<script>
    // Variables globales para el componente (usando el color pasado por props)
    const colorTheme = "{{ $color }}";

    function selectConectividad(tipo) {
        const input = document.getElementById('tipo_conectividad_input');
        const cardWifi = document.getElementById('card_wifi');
        const cardCableado = document.getElementById('card_cableado');
        const bloqueWifiFuente = document.getElementById('bloque_wifi_fuente');

        if (!input) return;

        input.value = tipo;

        if (tipo === 'WIFI') {
            // Activar Wifi
            cardWifi.classList.add(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardWifi.classList.remove('border-slate-200', 'bg-white');
            // Desactivar Cableado
            cardCableado.classList.remove(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardCableado.classList.add('border-slate-200', 'bg-white');

            bloqueWifiFuente.classList.remove('hidden');
        } else {
            // Activar Cableado
            cardCableado.classList.add(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardCableado.classList.remove('border-slate-200', 'bg-white');
            // Desactivar Wifi
            cardWifi.classList.remove(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardWifi.classList.add('border-slate-200', 'bg-white');

            bloqueWifiFuente.classList.add('hidden');

            // Limpiar selección de fuente WiFi
            document.getElementById('wifi_fuente_input').value = '';

            // Rosetear estilos de fuentes wifi
            const cardEst = document.getElementById('card_wifi_establecimiento');
            const cardPers = document.getElementById('card_wifi_personal');

            if (cardEst) {
                cardEst.classList.remove(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
                cardEst.classList.add('border-slate-200', 'bg-white');
            }
            if (cardPers) {
                cardPers.classList.remove(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
                cardPers.classList.add('border-slate-200', 'bg-white');
            }
        }
    }

    function selectWifiFuente(fuente) {
        const input = document.getElementById('wifi_fuente_input');
        const cardEstablecimiento = document.getElementById('card_wifi_establecimiento');
        const cardPersonal = document.getElementById('card_wifi_personal');

        if (!input) return;
        input.value = fuente;

        if (fuente === 'ESTABLECIMIENTO') {
            cardEstablecimiento.classList.add(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardEstablecimiento.classList.remove('border-slate-200', 'bg-white');
            cardPersonal.classList.remove(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardPersonal.classList.add('border-slate-200', 'bg-white');
        } else {
            cardPersonal.classList.add(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardPersonal.classList.remove('border-slate-200', 'bg-white');
            cardEstablecimiento.classList.remove(`border-${colorTheme}-600`, `bg-${colorTheme}-50`);
            cardEstablecimiento.classList.add('border-slate-200', 'bg-white');
        }
    }

    // Inicialización automática
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar conectividad si tiene valor guardado
        const conectividadVal = document.getElementById('tipo_conectividad_input')?.value;
        if (conectividadVal) selectConectividad(conectividadVal);

        // Inicializar fuente WiFi si tiene valor guardado
        const wifiFuenteVal = document.getElementById('wifi_fuente_input')?.value;
        if (wifiFuenteVal) selectWifiFuente(wifiFuenteVal);

        // Reinicializar iconos si es necesario
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>