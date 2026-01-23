@props(['prefix', 'detalle'])

<div id="card_{{ $prefix }}"
    class="bg-white border border-slate-200 rounded-[3rem] overflow-hidden shadow-xl shadow-slate-200/40 transition-all duration-700 mb-10 group/card relative">
    {{-- BARRA DE ESTADO LATERAL IZQUIERDA --}}
    <div id="status_line_{{ $prefix }}"
        class="absolute left-0 top-0 w-2 h-full bg-slate-100 transition-colors duration-700"></div>

    {{-- HEADER PROFESIONAL --}}
    <div id="header_{{ $prefix }}"
        class="bg-slate-50/50 border-b border-slate-100 px-10 py-6 flex flex-col lg:flex-row justify-between items-center gap-6 transition-all duration-700">
        <div class="flex items-center gap-5">
            <div id="status_icon_bg_{{ $prefix }}"
                class="h-14 w-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-slate-400 border border-slate-100 transition-all duration-700">
                <div id="status_icon_{{ $prefix }}">
                    <i data-lucide="user-search" class="w-7 h-7"></i>
                </div>
            </div>
            <div>
                <span id="badge_text_{{ $prefix }}"
                    class="text-[10px] font-black text-slate-500 uppercase tracking-[0.25em] block mb-1 leading-none">Módulo
                    de Identidad</span>
                <p id="sub_text_{{ $prefix }}"
                    class="text-[11px] text-slate-400 font-bold uppercase tracking-tight italic">Validación de datos del
                    profesional</p>
            </div>
        </div>

        <div class="flex items-center bg-white p-1.5 rounded-2xl border border-slate-100 shadow-inner gap-2">
            {{-- BOTÓN VALIDAR --}}
            <button type="button" onclick="buscarMaster('{{ $prefix }}')"
                class="group flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-black hover:shadow-lg hover:shadow-slate-900/30 active:scale-95 transition-all outline-none">
                <i data-lucide="shield-check"
                    class="w-4 h-4 text-indigo-400 group-hover:text-white transition-colors"></i>
                Validar Doc
            </button>

            <button type="button" onclick="nuevoProfesional('{{ $prefix }}')"
                class="group flex items-center gap-2 px-6 py-3 bg-white text-slate-500 rounded-xl text-[11px] font-black uppercase tracking-widest hover:text-orange-600 hover:bg-orange-50 transition-all active:scale-95 outline-none">
                <i data-lucide="user-plus" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                Nuevo
            </button>
        </div>
    </div>

    {{-- CUERPO DEL FORMULARIO --}}
    <div class="p-10 pl-16">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-8 gap-y-6">

            {{-- N° IDENTIDAD --}}
            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">N°
                    Identidad</label>
                <div class="relative group/input">
                    <input type="text" name="contenido[{{ $prefix }}][doc]" id="doc_{{ $prefix }}"
                        value="{{ $detalle->contenido[$prefix]['doc'] ?? '' }}"
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-black text-slate-700 tracking-widest text-sm shadow-sm">
                    <i data-lucide="fingerprint"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-200 group-focus-within/input:text-indigo-400 transition-colors"></i>
                </div>
            </div>

            {{-- TIPO DOC --}}
            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">Tipo
                    Doc.</label>
                <div class="relative">
                    <select name="contenido[{{ $prefix }}][tipo_doc]" id="tipo_{{ $prefix }}"
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-600 text-sm cursor-pointer appearance-none shadow-sm">
                        @php $tDoc = $detalle->contenido[$prefix]['tipo_doc'] ?? 'DNI'; @endphp
                        <option value="DNI" {{ $tDoc == 'DNI' ? 'selected' : '' }}>DNI</option>
                        <option value="CE" {{ $tDoc == 'CE' ? 'selected' : '' }}>C.E.</option>
                    </select>
                    <i data-lucide="chevron-down"
                        class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300 pointer-events-none"></i>
                </div>
            </div>

            {{-- NOMBRES --}}
            <div class="md:col-span-6">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">Nombres
                    Completos</label>
                <input type="text" name="contenido[{{ $prefix }}][nombres]" id="nombres_{{ $prefix }}"
                    value="{{ $detalle->contenido[$prefix]['nombres'] ?? '' }}"
                    class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-700 uppercase text-sm shadow-sm">
            </div>

            {{-- APELLIDO PATERNO --}}
            <div class="md:col-span-4">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">Apellido
                    Paterno</label>
                <input type="text" name="contenido[{{ $prefix }}][apellido_paterno]"
                    id="paterno_{{ $prefix }}"
                    value="{{ $detalle->contenido[$prefix]['apellido_paterno'] ?? '' }}"
                    class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-700 uppercase text-sm shadow-sm">
            </div>

            {{-- APELLIDO MATERNO --}}
            <div class="md:col-span-4">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">Apellido
                    Materno</label>
                <input type="text" name="contenido[{{ $prefix }}][apellido_materno]"
                    id="materno_{{ $prefix }}"
                    value="{{ $detalle->contenido[$prefix]['apellido_materno'] ?? '' }}"
                    class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-700 uppercase text-sm shadow-sm">
            </div>

            {{-- EMAIL --}}
            <div class="md:col-span-4">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">Email</label>
                <div class="relative group/input">
                    <input type="email" name="contenido[{{ $prefix }}][email]" id="email_{{ $prefix }}"
                        value="{{ $detalle->contenido[$prefix]['email'] ?? '' }}"
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-indigo-600 text-sm shadow-sm">
                    <i data-lucide="mail"
                        class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-200 group-focus-within/input:text-indigo-400 transition-colors"></i>
                </div>
            </div>

            {{-- TELÉFONO --}}
            <div class="md:col-span-4">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">Teléfono /
                    Celular</label>
                <div class="relative group/input">
                    <input type="text" name="contenido[{{ $prefix }}][telefono]" id="tel_{{ $prefix }}"
                        value="{{ $detalle->contenido[$prefix]['telefono'] ?? '' }}"
                        class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-700 text-sm shadow-sm">
                    <i data-lucide="smartphone"
                        class="absolute left-5 top-1/2 -translate-x-1/2 w-4 h-4 text-slate-200 group-focus-within/input:text-indigo-400 transition-colors"></i>
                </div>
            </div>

            {{-- CARGO / PROFESIÓN --}}
            <div class="md:col-span-8">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 ml-1">Profesión</label>

                {{-- INPUT HIDDEN QUE GUARDA EL VALOR FINAL --}}
                <input type="hidden" name="contenido[{{ $prefix }}][cargo]"
                    id="cargo_final_{{ $prefix }}" value="{{ $detalle->contenido[$prefix]['cargo'] ?? '' }}">

                <div class="flex gap-2">
                    {{-- SELECT DE CARGOS --}}
                    <div class="relative w-full">
                        @php
                            $cargos = [
                                'MEDICO',
                                'ODONTOLOGO(A)',
                                'ENFERMERO(A)',
                                'TECNICO(A) ENFERMERIA',
                                'TECNICO(A) LABORATORIO',
                                'BIOLOGO(A)',
                                'QUIMICO FARMACEUTICO(A)',
                                'NUTRICIONISTA',
                                'PSICOLOGO(A)',
                                'OBSTETRA',
                                'OTROS',
                            ];
                            $valorActual = $detalle->contenido[$prefix]['cargo'] ?? '';
                            // Si el valor actual no está en la lista estándar y no está vacío, asumimos que es "OTROS"
                            $seleccion = in_array($valorActual, $cargos) ? $valorActual : ($valorActual ? 'OTROS' : '');
                        @endphp

                        <select id="cargo_select_{{ $prefix }}" onchange="syncCargo('{{ $prefix }}')"
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-bold text-slate-600 text-sm cursor-pointer appearance-none shadow-sm uppercase">
                            <option value="">-- SELECCIONE --</option>
                            @foreach ($cargos as $cargo)
                                <option value="{{ $cargo }}" {{ $seleccion == $cargo ? 'selected' : '' }}>
                                    {{ $cargo }}</option>
                            @endforeach
                        </select>
                        <i data-lucide="briefcase"
                            class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300 pointer-events-none"></i>
                    </div>

                    {{-- INPUT MANUAL (Se muestra si selecciona OTROS) --}}
                    <div id="div_cargo_manual_{{ $prefix }}"
                        class="{{ $seleccion == 'OTROS' ? '' : 'hidden' }} w-full relative animate-fade-in-right">
                        <input type="text" id="cargo_manual_{{ $prefix }}"
                            oninput="syncCargo('{{ $prefix }}')"
                            value="{{ $seleccion == 'OTROS' ? $valorActual : '' }}" placeholder="DIGITE LA PROFESION"
                            class="w-full px-5 py-4 bg-indigo-50 border-2 border-indigo-200 rounded-2xl focus:border-indigo-500 transition-all outline-none font-black text-indigo-600 uppercase text-sm shadow-sm">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@once
    <script>
        /**
         * Sincroniza el valor del Select y el Input Manual hacia el Input Hidden final
         */
        function syncCargo(prefix) {
            const select = document.getElementById('cargo_select_' + prefix);
            const manualDiv = document.getElementById('div_cargo_manual_' + prefix);
            const manualInput = document.getElementById('cargo_manual_' + prefix);
            const finalInput = document.getElementById('cargo_final_' + prefix);

            if (select.value === 'OTROS') {
                manualDiv.classList.remove('hidden');
                manualDiv.classList.add('flex');
                // El valor final es lo que escriba en el input manual
                finalInput.value = manualInput.value.trim().toUpperCase();
            } else {
                manualDiv.classList.add('hidden');
                manualDiv.classList.remove('flex');
                // El valor final es la opción del select
                finalInput.value = select.value;
            }
        }

        /**
         * Actualiza la interfaz visual según el estado del profesional
         */
        function updateIdentityUI(prefix, mode) {
            const card = document.getElementById('card_' + prefix);
            const iconBg = document.getElementById('status_icon_bg_' + prefix);
            const badge = document.getElementById('badge_text_' + prefix);
            const line = document.getElementById('status_line_' + prefix);
            const subText = document.getElementById('sub_text_' + prefix);

            // Limpiar estados previos
            card.classList.remove('border-emerald-500', 'border-amber-500', 'shadow-emerald-200/40', 'shadow-amber-200/40',
                'shadow-2xl');
            line.classList.remove('bg-emerald-500', 'bg-amber-500');
            badge.classList.remove('text-emerald-600', 'text-amber-600');

            if (mode === 'success') {
                card.classList.add('border-emerald-500', 'shadow-2xl', 'shadow-emerald-100/50');
                line.classList.add('bg-emerald-500');
                badge.classList.add('text-emerald-600');
                badge.innerText = 'Profesional Cargado';
                subText.innerHTML =
                    '<span class="text-emerald-500 font-bold">●</span> Puede editar los campos para actualizar el maestro global';
                iconBg.innerHTML = '<i data-lucide="user-check" class="w-8 h-8 text-emerald-500 animate-bounce-short"></i>';
            } else if (mode === 'new') {
                card.classList.add('border-amber-500', 'shadow-2xl', 'shadow-amber-100/50');
                line.classList.add('bg-amber-500');
                badge.classList.add('text-amber-600');
                badge.innerText = 'Modo: Nuevo Registro';
                subText.innerText = 'Este DOC no existe. Se creará un nuevo registro al guardar.';
                iconBg.innerHTML = '<i data-lucide="user-plus" class="w-8 h-8 text-amber-500"></i>';
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        /**
         * Busca profesional en la DB y rellena campos, incluyendo la lógica del cargo
         */
        function buscarMaster(prefix) {
            const docInput = document.getElementById('doc_' + prefix);
            const doc = docInput.value.trim();

            if (doc.length < 8) {
                Swal.fire({
                    title: 'N° DOC Inválido',
                    text: 'Ingrese un N° DOC válido.',
                    icon: 'error',
                    confirmButtonColor: '#0f172a'
                });
                return;
            }

            fetch(`/usuario/monitoreo/profesional/buscar/${doc}`)
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        // MODO ACTUALIZACIÓN
                        updateIdentityUI(prefix, 'success');
                        document.getElementById('nombres_' + prefix).value = data.nombres;
                        document.getElementById('paterno_' + prefix).value = data.apellido_paterno;
                        document.getElementById('materno_' + prefix).value = data.apellido_materno;
                        document.getElementById('email_' + prefix).value = data.email || '';
                        document.getElementById('tel_' + prefix).value = data.telefono || '';
                        document.getElementById('tipo_' + prefix).value = data.tipo_doc;

                        // --- Lógica para poblar el Cargo/Profesión ---
                        const cargoDb = (data.cargo || '').toUpperCase();
                        const select = document.getElementById('cargo_select_' + prefix);
                        const manualInput = document.getElementById('cargo_manual_' + prefix);

                        // Verificar si el cargo de la BD existe en las opciones del select
                        let existsInSelect = false;
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value === cargoDb) {
                                existsInSelect = true;
                                break;
                            }
                        }

                        if (existsInSelect) {
                            select.value = cargoDb;
                            manualInput.value = '';
                        } else if (cargoDb !== '') {
                            // Si tiene cargo pero no está en la lista, es "OTROS"
                            select.value = 'OTROS';
                            manualInput.value = cargoDb;
                        } else {
                            // Si no tiene cargo
                            select.value = '';
                            manualInput.value = '';
                        }
                        // Ejecutar sincronización para ocultar/mostrar input manual
                        syncCargo(prefix);

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Profesional encontrado. Datos cargados.'
                        });

                    } else {
                        // MODO NUEVO
                        updateIdentityUI(prefix, 'new');
                        Swal.fire({
                            title: 'Sin Resultados',
                            text: 'El profesional no figura en el maestro. ¿Desea registrarlo como nuevo?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, Registrar',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#0f172a',
                            customClass: {
                                popup: 'rounded-[2rem]'
                            }
                        }).then(r => {
                            if (!r.isConfirmed) {
                                nuevoProfesional(prefix);
                            } else {
                                document.getElementById('nombres_' + prefix).focus();
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                });
        }

        /**
         * Limpia los campos para un nuevo registro
         */
        function nuevoProfesional(prefix) {
            updateIdentityUI(prefix, 'new');
            const fields = ['nombres_', 'paterno_', 'materno_', 'email_', 'tel_', 'cargo_manual_'];
            fields.forEach(f => {
                const input = document.getElementById(f + prefix);
                if (input) input.value = '';
            });
            document.getElementById('tipo_' + prefix).value = 'DNI';
            document.getElementById('cargo_select_' + prefix).value = '';

            syncCargo(prefix); // Actualizar estado del cargo

            document.getElementById('doc_' + prefix).focus();
        }
    </script>

    <style>
        @keyframes bounce-short {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .animate-bounce-short {
            animation: bounce-short 0.5s ease-in-out 2;
        }

        @keyframes fade-in-right {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-right {
            animation: fade-in-right 0.3s ease-out forwards;
        }
    </style>
@endonce
