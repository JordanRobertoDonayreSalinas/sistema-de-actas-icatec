
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
        card.classList.remove('border-emerald-500', 'border-amber-500', 'shadow-emerald-200/40', 'shadow-amber-200/40', 'shadow-2xl');
        line.classList.remove('bg-emerald-500', 'bg-amber-500');
        badge.classList.remove('text-emerald-600', 'text-amber-600');
        
        if(mode === 'success') {
            card.classList.add('border-emerald-500', 'shadow-2xl', 'shadow-emerald-100/50');
            line.classList.add('bg-emerald-500');
            badge.classList.add('text-emerald-600');
            badge.innerText = 'Profesional Cargado';
            subText.innerHTML = '<span class="text-emerald-500 font-bold">●</span> Puede editar los campos para actualizar el maestro global';
            iconBg.innerHTML = '<i data-lucide="user-check" class="w-8 h-8 text-emerald-500 animate-bounce-short"></i>';
        } else if(mode === 'new') {
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
        
        if(doc.length < 8) {
            Swal.fire({
                title: 'N° DOC Inválido',
                text: 'Ingrese un N° DOC válido.',
                icon: 'error',
                confirmButtonColor: '#0f172a'
            });
            return;
        }

        Swal.fire({
            title: 'Buscando...',
            text: 'Consultando base de datos local',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        // 1er paso: Buscar localmente
        fetch(`/usuario/monitoreo/profesional/buscar/${doc}?local_only=1`)
            .then(res => res.json())
            .then(data => {
                if(data.exists) {
                    Swal.close();
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
                        select.value = 'OTROS';
                        manualInput.value = cargoDb;
                    } else {
                        select.value = '';
                        manualInput.value = '';
                    }
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
                    // No está en la BD Local. Procedemos a buscar en RENIEC (API Externa) si es DNI (8 dígitos)
                    if (doc.length === 8) {
                        Swal.fire({
                            title: 'DNI No Registrado Localmente',
                            html: '<div class="text-sm text-slate-500 mb-4 font-bold uppercase tracking-widest mt-2">Consultando servicio web RENIEC...</div>' +
                                  '<div class="w-full bg-slate-100 rounded-full h-1.5 mb-2 overflow-hidden">' +
                                  '<div class="bg-indigo-600 h-1.5 rounded-full animate-pulse" style="width: 100%"></div>' +
                                  '</div>',
                            allowOutsideClick: false,
                            showConfirmButton: false
                        });
                        
                        // 2do paso: Buscar en la API externa (ya sin local_only)
                        fetch(`/usuario/monitoreo/profesional/buscar/${doc}`)
                            .then(res => res.json())
                            .then(dataExt => {
                                Swal.close();
                                if (dataExt.exists_external) {
                                    // MODO NUEVO EXTERNO (DNI ENCONTRADO EN API)
                                    updateIdentityUI(prefix, 'new');
                                    
                                    document.getElementById('nombres_' + prefix).value = dataExt.nombres;
                                    document.getElementById('paterno_' + prefix).value = dataExt.apellido_paterno;
                                    document.getElementById('materno_' + prefix).value = dataExt.apellido_materno;
                                    document.getElementById('tipo_' + prefix).value = dataExt.tipo_doc || 'DNI';
                                    
                                    // Limpiar y preparar cargo
                                    document.getElementById('email_' + prefix).value = '';
                                    document.getElementById('tel_' + prefix).value = '';
                                    document.getElementById('cargo_select_' + prefix).value = '';
                                    syncCargo(prefix);
                                    
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 4000,
                                        timerProgressBar: true
                                    });
                                    Toast.fire({
                                        icon: 'info',
                                        title: 'Nombres encontrados en RENIEC. Complete los demás datos.'
                                    });

                                } else {
                                    promptNuevoManual(prefix);
                                }
                            })
                            .catch(error => {
                                console.error('Error externo:', error);
                                Swal.fire('Error', 'No se pudo conectar con el servidor externo', 'error')
                                    .then(() => promptNuevoManual(prefix));
                            });
                            
                    } else {
                        // Si no es DNI 8 dígitos, simplemente no existe
                        Swal.close();
                        promptNuevoManual(prefix);
                    }
                }
            })
            .catch(error => {
                console.error('Error local:', error);
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            });
    }

    /**
     * Helper para preguntar si se registra manual cuando no se encuentra en ningún lado
     */
    function promptNuevoManual(prefix) {
        updateIdentityUI(prefix, 'new');
        Swal.fire({
            title: 'Sin Resultados',
            text: 'El profesional no figura en el maestro ni en línea. ¿Desea registrarlo como nuevo manual?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, Registrar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0f172a',
            customClass: { popup: 'rounded-[2rem]' }
        }).then(r => { 
            if(!r.isConfirmed) {
                nuevoProfesional(prefix);
            } else {
                document.getElementById('nombres_' + prefix).focus();
            }
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
            if(input) input.value = '';
        });
        document.getElementById('tipo_' + prefix).value = 'DNI';
        document.getElementById('cargo_select_' + prefix).value = '';
        
        syncCargo(prefix); // Actualizar estado del cargo
        
        document.getElementById('doc_' + prefix).focus();
    }

