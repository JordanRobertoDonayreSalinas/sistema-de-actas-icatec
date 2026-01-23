@extends('layouts.usuario')
@section('title', 'Consulta Externa - Medicina')

@section('content')
    <div class="py-10 bg-[#F8F9FC] min-h-screen">
        <div class="max-w-6xl mx-auto px-4">

            {{-- CABECERA --}}
            <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">02. Triaje</h2>
                    <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                        {{ $acta->establecimiento->nombre }}
                    </p>
                </div>
                <a href="{{ route('usuario.monitoreo.salud_mental_group.index', $acta->id) }}"
                    class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver
                </a>
            </div>

            {{-- FORMULARIO --}}
            {{-- IMPORTANTE: Agregamos el id="formFicha" y el evento onsubmit --}}
            <form id="formFicha" action="{{ route('usuario.monitoreo.sm_medicina_general.store', $acta->id) }}"
                method="POST" enctype="multipart/form-data" onsubmit="sincronizarDatos(event)">
                @csrf

                {{-- CONTENEDOR PARA LOS INPUTS OCULTOS GENERADOS POR JS --}}
                <div id="inputs_ocultos_container"></div>

                <div class="space-y-6">

                    {{-- 1. DETALLE CONSULTORIO (Funciona directo) --}}
                    <x-esp_1_detalleDeConsultorio :detalle="$detalle" />

                    {{-- 2. DATOS PROFESIONAL (Funciona directo) --}}
                    <x-esp_2_datosProfesional :detalle="$detalle" prefix="profesional" />

                    {{-- 3. DNI (Funciona directo) --}}
                    <x-esp_3_detalleDni :detalle="$detalle" color="teal" />

                    {{-- 4. CAPACITACIÓN (NO TIENE NAME - LO ENVOLVEMOS) --}}
                    <div id="wrapper_capacitacion">
                        <x-esp_4_detalleCap :model="$detalle->contenido['capacitacion'] ?? []" />
                    </div>

                    {{-- 5. MATERIALES (NO TIENE NAME - LO ENVOLVEMOS) --}}
                    <div id="wrapper_materiales">
                        {{-- OJO: Asegúrate que el tipo coincida con lo que quieres mostrar, aquí puse odontologia por tu ejemplo anterior --}}
                        <x-materiales :model="$detalle->contenido['materiales'] ?? []" tipo="odontologia" />
                    </div>

                    {{-- 6. EQUIPOS (Envía 'equipos[...]') --}}
                    <x-esp_5_equipos :equipos="$equipos" modulo="medicina" />

                    {{-- 7. SOPORTE (Funciona directo) --}}
                    <x-esp_6_soporte :detalle="$detalle" />

                    {{-- 8. COMENTARIOS --}}
                    @php
                        $comentData = (object) ($detalle->contenido['comentarios'] ?? []);
                        if (isset($comentData->texto)) {
                            $comentData->comentario_esp = $comentData->texto;
                        }
                        if (isset($comentData->foto)) {
                            $comentData->foto_url_esp = $comentData->foto;
                        }
                    @endphp
                    <x-esp_7_comentariosEvid :comentario="$comentData" />

                </div>

                {{-- BOTÓN GUARDAR --}}
                <div class="mt-10 flex justify-end">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                        <i data-lucide="save" class="w-5 h-5 mr-2"></i> GUARDAR FICHA
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPT MAGICO PARA CAPTURAR DATOS SIN NAME --}}
    <script>
        function sincronizarDatos(e) {
            // Función auxiliar para crear inputs hidden
            const createHidden = (name, value) => {
                if (!value) return;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                document.getElementById('inputs_ocultos_container').appendChild(input);
            };

            // Limpiar contenedor previo
            document.getElementById('inputs_ocultos_container').innerHTML = '';

            // ---------------------------------------------------------
            // 1. CAPTURAR CAPACITACIÓN
            // ---------------------------------------------------------
            const capWrap = document.getElementById('wrapper_capacitacion');
            if (capWrap) {
                // Buscar radio seleccionado (SI/NO)
                const radioCap = capWrap.querySelector('input[type="radio"]:checked');
                if (radioCap) createHidden('contenido[capacitacion][recibieron_cap]', radioCap.value);

                // Buscar Select
                const selectCap = capWrap.querySelector('select');
                if (selectCap && selectCap.value) createHidden('contenido[capacitacion][institucion_cap]', selectCap.value);
            }

            // ---------------------------------------------------------
            // 2. CAPTURAR MATERIALES
            // ---------------------------------------------------------
            const matWrap = document.getElementById('wrapper_materiales');
            if (matWrap) {
                // El componente materiales tiene bloques (divs) para cada sección.
                // Los buscamos por su clase de tarjeta
                const tarjetas = matWrap.querySelectorAll('.bg-slate-50.p-5');

                // Tarjeta 1: FUA
                if (tarjetas[0]) {
                    const checked = tarjetas[0].querySelector('input[type="radio"]:checked');
                    if (checked) createHidden('contenido[materiales][fua]', checked.value);
                }

                // Tarjeta 2: REFERENCIA
                if (tarjetas[1]) {
                    const checked = tarjetas[1].querySelector('input[type="radio"]:checked');
                    if (checked) createHidden('contenido[materiales][referencia]', checked.value);
                }

                // Tarjeta 3: RECETA (Si existe)
                if (tarjetas[2]) {
                    const checked = tarjetas[2].querySelector('input[type="radio"]:checked');
                    // Detectamos si es Receta o Lab leyendo el título
                    const titulo = tarjetas[2].querySelector('span').innerText.toUpperCase();
                    if (titulo.includes('RECETA') && checked) {
                        createHidden('contenido[materiales][receta]', checked.value);
                    } else if (titulo.includes('LABORATORIO') && checked) {
                        createHidden('contenido[materiales][orden_lab]', checked.value);
                    }
                }

                // Tarjeta 4: LABORATORIO (Si existe aparte)
                if (tarjetas[3]) {
                    const checked = tarjetas[3].querySelector('input[type="radio"]:checked');
                    if (checked) createHidden('contenido[materiales][orden_lab]', checked.value);
                }
            }
        }
    </script>
@endsection
