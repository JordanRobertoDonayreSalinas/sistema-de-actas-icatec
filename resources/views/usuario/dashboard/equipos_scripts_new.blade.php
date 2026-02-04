<script>
    // ============================================
    // DASHBOARD DE EQUIPOS DE CÓMPUTO - CHART.JS
    // ============================================

    console.log('Dashboard Equipos - Script cargado');

    // Variables globales para los gráficos
    let chartEstado = null;
    let chartTipo = null;
    let chartModulo = null;
    let chartDescripcion = null;
    let chartEstablecimiento = null;
    let debounceTimer = null;

    // Configuración de colores
    const COLORS = {
        estado: {
            'OPERATIVO': 'rgb(34, 197, 94)',      // green-500
            'REGULAR': 'rgb(251, 146, 60)',       // orange-400
            'INOPERATIVO': 'rgb(239, 68, 68)',    // red-500
            'Sin Estado': 'rgb(148, 163, 184)'    // slate-400
        },
        tipo: ['rgb(99, 102, 241)', 'rgb(168, 85, 247)'], // indigo-500, purple-500
        modulo: 'rgb(139, 92, 246)',  // violet-500
        descripcion: 'rgb(245, 158, 11)' // amber-500
    };

    // ============================================
    // FUNCIÓN PRINCIPAL: CARGAR ESTADÍSTICAS
    // ============================================
    function cargarEstadisticas() {
        // Implementar debouncing para evitar llamadas repetidas
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            console.log('Cargando estadísticas...');

            // Mostrar estado de carga
            mostrarCargando(true);


            // Obtener valores de filtros
            const mes = document.getElementById('eq_mes')?.value || '';
            const anio = document.getElementById('eq_anio')?.value || '{{ now()->year }}';
            const tipo = document.getElementById('eq_tipo')?.value || '';
            const provincia = document.getElementById('eq_provincia')?.value || '';
            const establecimientoId = document.getElementById('eq_establecimiento')?.value || '';
            const descripcion = document.getElementById('eq_descripcion')?.value || '';

            // Obtener módulos seleccionados desde checkboxes
            const checkboxes = document.querySelectorAll('.eq_modulo_checkbox:checked');
            const modulosSeleccionados = Array.from(checkboxes).map(cb => cb.value);

            // Construir URL con parámetros
            const params = new URLSearchParams({
                mes: mes,
                anio: anio,
                tipo: tipo,
                provincia: provincia,
                establecimiento_id: establecimientoId,
                descripcion: descripcion
            });

            // Agregar módulos como parámetros múltiples (modulos[]=valor1&modulos[]=valor2)
            modulosSeleccionados.forEach(modulo => {
                if (modulo) {
                    params.append('modulos[]', modulo);
                }
            });

            const url = '{{ route("usuario.dashboard.ajax.equipos.stats") }}?' + params.toString();
            console.log('Fetching:', url);

            // Realizar petición AJAX
            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);

                    // Actualizar UI con los datos
                    actualizarDashboard(data);

                    // Ocultar estado de carga
                    mostrarCargando(false);
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('Error al cargar las estadísticas. Por favor, intenta nuevamente.');
                    mostrarCargando(false);
                });
        }, 500); // Debounce de 500ms
    }

    // ============================================
    // ACTUALIZAR DASHBOARD CON DATOS
    // ============================================
    function actualizarDashboard(data) {
        // 1. Actualizar total de equipos
        const totalElement = document.getElementById('eq_totalEquipos');
        if (totalElement) {
            totalElement.textContent = data.totalEquipos || '0';
        }

        // 2. Actualizar período
        const periodoElement = document.getElementById('eq_periodoTexto');
        if (periodoElement) {
            periodoElement.textContent = data.periodoTexto || 'Sin período';
        }

        // 3. Renderizar gráficos con protección de errores
        try {
            console.log('Datos Estado:', data.equiposPorEstado);
            renderizarGraficoEstado(data.equiposPorEstado || {});
        } catch (e) { console.error('Error renderizarGraficoEstado', e); }

        try {
            renderizarGraficoTipo(data.equiposPorTipo || {});
        } catch (e) { console.error('Error renderizarGraficoTipo', e); }

        try {
            renderizarGraficoModulo(data.equiposPorModulo || {});
        } catch (e) { console.error('Error renderizarGraficoModulo', e); }

        try {
            renderizarGraficoDescripcion(data.topDescripciones || {});
        } catch (e) { console.error('Error renderizarGraficoDescripcion', e); }

        try {
            renderizarGraficoEstablecimiento(data.equiposPorEstablecimiento || {});
        } catch (e) { console.error('Error renderizarGraficoEstablecimiento', e); }

        console.log('Dashboard actualizado con', data.totalEquipos, 'equipos');
    }

    // ============================================
    // GRÁFICO 1: EQUIPOS POR ESTADO (Dona)
    // ============================================
    function renderizarGraficoEstado(datos) {
        const ctx = document.getElementById('chartEstado');
        if (!ctx) return;

        // Destruir gráfico anterior si existe
        if (chartEstado) {
            chartEstado.destroy();
        }

        // Convertir objeto a arrays
        const labels = Object.keys(datos);
        const values = Object.values(datos);
        const colors = labels.map(label => COLORS.estado[label] || COLORS.estado['Sin Estado']);

        chartEstado = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '600' },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ============================================
    // GRÁFICO 2: EQUIPOS POR TIPO (Barras)
    // ============================================
    function renderizarGraficoTipo(datos) {
        const ctx = document.getElementById('chartTipo');
        if (!ctx) return;

        if (chartTipo) {
            chartTipo.destroy();
        }

        const labels = Object.keys(datos);
        const values = Object.values(datos);

        chartTipo = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad de Equipos',
                    data: values,
                    backgroundColor: COLORS.tipo,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `Equipos: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ============================================
    // GRÁFICO 3: EQUIPOS POR MÓDULO (Barras Horizontales)
    // ============================================
    function renderizarGraficoModulo(datos) {
        const ctx = document.getElementById('chartModulo');
        if (!ctx) return;

        if (chartModulo) {
            chartModulo.destroy();
        }

        const labels = Object.keys(datos);
        const values = Object.values(datos);

        chartModulo = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad de Equipos',
                    data: values,
                    backgroundColor: COLORS.modulo,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y', // Barras horizontales
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `Equipos: ${context.parsed.x}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ============================================
    // GRÁFICO 4: TOP DESCRIPCIONES (Barras)
    // ============================================
    function renderizarGraficoDescripcion(datos) {
        const ctx = document.getElementById('chartDescripcion');
        if (!ctx) return;

        if (chartDescripcion) {
            chartDescripcion.destroy();
        }

        const labels = Object.keys(datos);
        const values = Object.values(datos);

        chartDescripcion = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad',
                    data: values,
                    backgroundColor: COLORS.descripcion,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `Cantidad: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ============================================
    // FUNCIONES DE UI
    // ============================================
    function mostrarCargando(mostrar) {
        // Puedes agregar un spinner o indicador de carga aquí
        const btnAplicar = document.getElementById('btnAplicarFiltrosEquipos');
        if (btnAplicar) {
            if (mostrar) {
                btnAplicar.disabled = true;
                btnAplicar.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline-block mr-2 animate-spin"></i> Cargando...';
            } else {
                btnAplicar.disabled = false;
                btnAplicar.innerHTML = '<i data-lucide="search" class="w-4 h-4 inline-block mr-2"></i> Aplicar Filtros';
            }
            // Refrescar iconos de Lucide
            if (window.refreshLucide) window.refreshLucide();
        }
    }

    /**
     * Renderizar gráfico de establecimientos (Top 15)
     */
    function renderizarGraficoEstablecimiento(data) {
        const ctx = document.getElementById('chartEstablecimiento');
        if (!ctx) {
            console.error('Canvas chartEstablecimiento no encontrado');
            return;
        }

        // Destruir gráfico anterior
        if (chartEstablecimiento) {
            chartEstablecimiento.destroy();
        }

        // Convertir objeto a array y ordenar por cantidad
        const dataArray = Object.entries(data).map(([nombre, cantidad]) => ({
            nombre,
            cantidad
        }));

        // Ordenar de mayor a menor
        dataArray.sort((a, b) => b.cantidad - a.cantidad);

        // NO aplicar slice para mostrar todos, como solicitó el usuario
        const datosGrafico = dataArray;

        const labels = datosGrafico.map(item => item.nombre);
        const valores = datosGrafico.map(item => item.cantidad);

        // Ajustar altura del contenedor si hay muchos datos
        const container = ctx.parentNode;
        if (datosGrafico.length > 15) {
            container.style.height = `${datosGrafico.length * 25}px`; // 25px por barra aprox
        } else {
            container.style.height = '320px'; // h-80 default
        }

        // Crear gráfico de barras horizontales
        chartEstablecimiento = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Equipos',
                    data: valores,
                    backgroundColor: 'rgba(6, 182, 212, 0.8)', // cyan-500
                    borderColor: 'rgb(6, 182, 212)',
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(8, 145, 178, 0.9)' // cyan-600
                }]
            },
            options: {
                indexAxis: 'y', // Barras horizontales
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return `${context.parsed.x} equipos`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                            drawBorder: false
                        }
                    },
                    y: {
                        ticks: {
                            color: '#475569',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            // Truncar nombres largos
                            callback: function (value, index, values) {
                                const label = this.getLabelForValue(value);
                                return label.length > 30 ? label.substring(0, 27) + '...' : label;
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // ============================================
    // FILTROS EN CASCADA (DEPENDENT FILTERS)
    // ============================================

    let filtrosDebounceTimer = null;

    /**
     * Actualizar opciones de filtros según selección actual
     */
    function actualizarFiltros() {
        // Debouncing para evitar múltiples llamadas
        clearTimeout(filtrosDebounceTimer);

        filtrosDebounceTimer = setTimeout(() => {
            console.log('Actualizando opciones de filtros...');

            // Obtener valores actuales
            const mes = document.getElementById('eq_mes')?.value || '';
            const anio = document.getElementById('eq_anio')?.value || '{{ now()->year }}';
            const tipo = document.getElementById('eq_tipo')?.value || '';
            const provincia = document.getElementById('eq_provincia')?.value || '';
            const establecimientoId = document.getElementById('eq_establecimiento')?.value || '';

            // Obtener módulos seleccionados
            const checkboxes = document.querySelectorAll('.eq_modulo_checkbox:checked');
            const modulosSeleccionados = Array.from(checkboxes).map(cb => cb.value);

            // Construir parámetros
            const params = new URLSearchParams({
                mes: mes,
                anio: anio,
                tipo: tipo,
                provincia: provincia,
                establecimiento_id: establecimientoId
            });

            // Agregar módulos
            modulosSeleccionados.forEach(modulo => {
                if (modulo) {
                    params.append('modulos[]', modulo);
                }
            });

            const url = '{{ route("usuario.dashboard.ajax.equipos.filter-options") }}?' + params.toString();

            // Llamar endpoint
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta de filtros:', data);

                    if (data.success) {
                        console.log(`Actualizando: ${data.provincias.length} provincias, ${data.establecimientos.length} establecimientos, ${data.modulos.length} módulos, ${data.descripciones.length} descripciones`);

                        actualizarSelectProvincias(data.provincias);
                        actualizarSelectEstablecimientos(data.establecimientos);
                        actualizarCheckboxesModulos(data.modulos);
                        actualizarSelectDescripciones(data.descripciones);
                    } else {
                        console.error('Error al obtener opciones de filtros:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error en actualizarFiltros:', error);
                });
        }, 300); // 300ms de debounce
    }

    /**
     * Actualizar select de provincias
     */
    function actualizarSelectProvincias(provincias) {
        const select = document.getElementById('eq_provincia');
        if (!select) return;

        const valorActual = select.value;
        select.innerHTML = '<option value="">Todas</option>';

        provincias.forEach(provincia => {
            const option = document.createElement('option');
            option.value = provincia;
            option.textContent = provincia;
            if (provincia === valorActual) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    /**
     * Actualizar select de establecimientos
     */
    function actualizarSelectEstablecimientos(establecimientos) {
        const select = document.getElementById('eq_establecimiento');
        if (!select) return;

        const valorActual = select.value;
        select.innerHTML = '<option value="">Todos</option>';

        establecimientos.forEach(est => {
            const option = document.createElement('option');
            option.value = est.id;
            option.textContent = est.nombre;
            if (est.id == valorActual) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    /**
     * Actualizar checkboxes de módulos
     */
    function actualizarCheckboxesModulos(modulos) {
        // Buscar el contenedor del grid de módulos
        const container = document.querySelector('.eq_modulo_checkbox')?.closest('.grid');

        // Si no encuentra por checkbox, buscar directamente el grid dentro del contenedor de módulos
        const containerAlt = document.querySelector('input[name="modulos[]"]')?.closest('.grid');
        const gridContainer = container || containerAlt;

        if (!gridContainer) {
            console.error('No se encontró el contenedor de módulos');
            return;
        }

        // Obtener módulos actualmente seleccionados
        const seleccionados = Array.from(document.querySelectorAll('.eq_modulo_checkbox:checked'))
            .map(cb => cb.value);

        // Limpiar contenedor
        gridContainer.innerHTML = '';

        // Agregar checkboxes
        modulos.forEach(mod => {
            // Detectar si es módulo especializado
            const esEspecializado = mod.valor.endsWith('_esp') || mod.valor.startsWith('sm_');

            const label = document.createElement('label');
            label.className = `flex items-center gap-2 p-2 rounded-lg hover:bg-white transition-all cursor-pointer group ${esEspecializado ? 'border-l-2 border-purple-400' : ''}`;

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'modulos[]';
            checkbox.value = mod.valor;
            // Color diferente para especializados: púrpura
            checkbox.className = esEspecializado
                ? 'eq_modulo_checkbox w-4 h-4 text-purple-600 border-purple-300 rounded focus:ring-2 focus:ring-purple-500 cursor-pointer'
                : 'eq_modulo_checkbox w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 cursor-pointer';

            // Preservar selección si el módulo estaba seleccionado
            if (seleccionados.includes(mod.valor)) {
                checkbox.checked = true;
            }

            // Event listener para actualizar contador
            checkbox.addEventListener('change', function () {
                // Solo actualizar contador, NO actualizar filtros automáticamente
                // Esto permite seleccionar múltiples módulos antes de aplicar filtros
                actualizarContador();
            });

            const span = document.createElement('span');
            span.className = esEspecializado
                ? 'text-sm text-purple-700 group-hover:text-purple-600 transition-colors font-medium'
                : 'text-sm text-slate-700 group-hover:text-indigo-600 transition-colors';
            span.textContent = mod.nombre;

            // Agregar badge "ESP" para módulos especializados
            if (esEspecializado) {
                const badge = document.createElement('span');
                badge.className = 'ml-auto text-xs px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full font-semibold';
                badge.textContent = 'ESP';

                label.appendChild(checkbox);
                label.appendChild(span);
                label.appendChild(badge);
            } else {
                label.appendChild(checkbox);
                label.appendChild(span);
            }

            gridContainer.appendChild(label);
        });

        // Actualizar contador
        actualizarContador();

        console.log(`Módulos actualizados: ${modulos.length} opciones disponibles`);
    }

    /**
     * Actualizar select de descripciones
     */
    function actualizarSelectDescripciones(descripciones) {
        const select = document.getElementById('eq_descripcion');
        if (!select) return;

        const valorActual = select.value;
        select.innerHTML = '<option value="">Todas</option>';

        descripciones.forEach(desc => {
            const option = document.createElement('option');
            option.value = desc;
            option.textContent = desc;
            if (desc === valorActual) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    // ============================================
    // FUNCIONES AUXILIARES
    // ============================================

    /**
     * Mostrar error al usuario
     */
    function mostrarError(mensaje) {
        console.error(mensaje);
        // Puedes agregar una notificación visual aquí
        alert(mensaje);
    }

    // ============================================
    // INICIALIZACIÓN
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM Ready - Inicializando dashboard de equipos...');

        // Cargar estadísticas iniciales
        cargarEstadisticas();

        // Event listener para botón aplicar filtros
        const btnAplicar = document.getElementById('btnAplicarFiltrosEquipos');
        if (btnAplicar) {
            btnAplicar.addEventListener('click', function (e) {
                e.preventDefault();
                cargarEstadisticas();
            });
            console.log('Event listener agregado al botón');
        }

        // ============================================
        // FUNCIONALIDAD DE CHECKBOXES DE MÓDULOS
        // ============================================

        // Función para actualizar contador
        function actualizarContador() {
            const checkboxes = document.querySelectorAll('.eq_modulo_checkbox:checked');
            const contador = document.getElementById('contadorModulos');
            if (contador) {
                const count = checkboxes.length;
                contador.textContent = count === 0 ? '0 seleccionados' :
                    count === 1 ? '1 seleccionado' :
                        `${count} seleccionados`;
            }
        }

        // Event listener para cada checkbox
        const checkboxes = document.querySelectorAll('.eq_modulo_checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', actualizarContador);
        });

        // Botón "Seleccionar Todos"
        const btnSeleccionarTodos = document.getElementById('btnSeleccionarTodos');
        if (btnSeleccionarTodos) {
            btnSeleccionarTodos.addEventListener('click', function () {
                checkboxes.forEach(cb => cb.checked = true);
                actualizarContador();
            });
        }

        // Botón "Limpiar"
        const btnLimpiarModulos = document.getElementById('btnLimpiarModulos');
        if (btnLimpiarModulos) {
            btnLimpiarModulos.addEventListener('click', function () {
                checkboxes.forEach(cb => cb.checked = false);
                actualizarContador();
            });
        }

        // Actualizar contador inicial
        actualizarContador();

        // ============================================
        // EVENT LISTENERS PARA FILTROS EN CASCADA
        // ============================================

        // Actualizar filtros cuando cambie cualquier filtro PRINCIPAL
        // (NO incluir módulos para permitir selección múltiple)
        const filtrosIds = ['eq_mes', 'eq_anio', 'eq_tipo', 'eq_provincia', 'eq_establecimiento'];
        filtrosIds.forEach(filtroId => {
            const filtro = document.getElementById(filtroId);
            if (filtro) {
                filtro.addEventListener('change', function () {
                    console.log(`Filtro ${filtroId} cambiado, actualizando opciones...`);
                    actualizarFiltros();
                });
            }
        });

        console.log('Event listeners de filtros en cascada agregados');
        console.log('NOTA: Los módulos NO actualizan filtros automáticamente, usa "Aplicar Filtros" para ver resultados');

        // Event listeners para filtros (opcional: aplicar automáticamente)
        // const filtros = ['eq_mes', 'eq_anio', 'eq_tipo', 'eq_provincia', 'eq_establecimiento', 'eq_descripcion'];
        // filtros.forEach(filtroId => {
        //     const filtro = document.getElementById(filtroId);
        //     if (filtro) {
        //         filtro.addEventListener('change', cargarEstadisticas);
        //     }
        // });
    });
</script>