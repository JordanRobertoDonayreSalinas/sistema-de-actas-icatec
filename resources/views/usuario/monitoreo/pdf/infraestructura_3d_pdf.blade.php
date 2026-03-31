<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo 19: Infraestructura 3D - Acta {{ $acta->numero_acta }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm 2.5cm 1.5cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }

        /* ENCABEZADO */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #7c3aed; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 15px; text-transform: uppercase; color: #7c3aed; font-weight: bold; }
        .acta-info { font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-top: 5px; }

        /* TÍTULOS DE SECCIÓN */
        .section-title {
            background-color: #ede9fe;
            padding: 6px 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-left: 4px solid #7c3aed;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 10px;
            color: #1e293b;
        }

        /* TABLAS */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f5f3ff; color: #4c1d95; font-size: 8.5px; text-transform: uppercase; }

        /* COLUMNA ETIQUETA */
        .bg-label { background-color: #f8fafc; font-weight: bold; width: 35%; text-transform: uppercase; color: #334155; font-size: 9px; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* BADGES DE TIPO */
        .badge { display: inline-block; padding: 2px 7px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-ambiente  { background: #bbf7d0; color: #065f46; }
        .badge-pasillo   { background: #e2e8f0; color: #334155; }
        .badge-hardware  { background: #dbeafe; color: #1e40af; }
        .badge-puerta    { background: #fef9c3; color: #92400e; }
        .badge-calle     { background: #d1d5db; color: #374151; }
        .badge-sistema   { background: #ede9fe; color: #4c1d95; }

        /* RESUMEN ESTADÍSTICO */
        .stats-grid { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .stats-grid td { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: center; vertical-align: middle; width: 16.66%; }
        .stat-number { font-size: 18px; font-weight: bold; color: #7c3aed; display: block; }
        .stat-label  { font-size: 8px; text-transform: uppercase; color: #64748b; }

        /* FIRMA */
        .firma-section { margin-top: 30px; page-break-inside: avoid; }
        .firma-table { width: 80%; margin: 0 auto; border-collapse: collapse; }
        .firma-cell { width: 50%; text-align: center; padding: 0 20px; vertical-align: bottom; }
        .firma-linea { border-bottom: 1px solid #000; height: 70px; margin-bottom: 5px; }
        .firma-nombre { font-weight: bold; font-size: 10px; text-transform: uppercase; }
        .firma-label  { font-size: 9px; color: #64748b; margin-top: 2px; }

        /* LEYENDA */
        .leyenda-box { border: 1px solid #e2e8f0; padding: 8px 12px; background: #fafafa; margin-top: 10px; font-size: 9px; color: #475569; }
    </style>
</head>
<body>

    @php $n = 1; @endphp

    {{-- ENCABEZADO --}}
    <div class="header">
        <h1>Módulo 19: Infraestructura y Croquis 3D</h1>
        <div class="acta-info">
            ACTA N° {{ str_pad($acta->numero_acta, 3, '0', STR_PAD_LEFT) }} |
            {{ $acta->establecimiento->codigo ?? 'S/C' }} - {{ strtoupper($acta->establecimiento->nombre) }} |
            FECHA: {{ \Carbon\Carbon::parse($modulo->updated_at)->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- 1. DATOS DEL ESTABLECIMIENTO --}}
    <div class="section-title">{{ $n++ }}. Datos del Establecimiento</div>
    <table>
        <tbody>
            <tr>
                <td class="bg-label">Código</td>
                <td class="uppercase">{{ $acta->establecimiento->codigo ?? '---' }}</td>
                <td class="bg-label">Nombre</td>
                <td class="uppercase">{{ $acta->establecimiento->nombre ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Provincia</td>
                <td class="uppercase">{{ $acta->establecimiento->provincia ?? '---' }}</td>
                <td class="bg-label">Distrito</td>
                <td class="uppercase">{{ $acta->establecimiento->distrito ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Red</td>
                <td class="uppercase">{{ $acta->establecimiento->red ?? '---' }}</td>
                <td class="bg-label">Microred</td>
                <td class="uppercase">{{ $acta->establecimiento->microred ?? '---' }}</td>
            </tr>
            <tr>
                <td class="bg-label">Responsable</td>
                <td colspan="3" class="uppercase">{{ $acta->responsable ?? $acta->establecimiento->responsable ?? '---' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 2. RESUMEN DEL CROQUIS --}}
    <div class="section-title">{{ $n++ }}. Resumen del Croquis</div>

    @php
        $totalElementos  = count($elementos);
        $totalConexiones = count($conexiones);
        $conteoPorTipo   = [];
        foreach ($elementos as $el) {
            $t = strtolower($el['type'] ?? 'otro');
            $conteoPorTipo[$t] = ($conteoPorTipo[$t] ?? 0) + 1;
        }
        $tiposLabels = [
            'ambiente'  => 'Ambientes',
            'pasillo'   => 'Pasillos',
            'hardware'  => 'Hardware',
            'puerta'    => 'Puertas',
            'calle'     => 'Calles',
            'sistema'   => 'Sistemas',
        ];
    @endphp

    <table class="stats-grid">
        <tr>
            <td>
                <span class="stat-number">{{ $totalElementos }}</span>
                <span class="stat-label">Total Elementos</span>
            </td>
            @foreach($tiposLabels as $tipo => $label)
            <td>
                <span class="stat-number" style="color: {{ $tipo === 'ambiente' ? '#16a34a' : ($tipo === 'hardware' ? '#2563eb' : ($tipo === 'sistema' ? '#7c3aed' : ($tipo === 'puerta' ? '#ca8a04' : '#475569'))) }}">
                    {{ $conteoPorTipo[$tipo] ?? 0 }}
                </span>
                <span class="stat-label">{{ $label }}</span>
            </td>
            @endforeach
        </tr>
    </table>

    {{-- 3. REPRESENTACIÓN GRÁFICA --}}
    @if(!empty($contenido['imagen_path']))
        <div class="section-title">{{ $n++ }}. Representación Gráfica del Croquis</div>
        <div style="text-align: center; margin-top: 5px; margin-bottom: 10px;">
            @php
                $path_rel = $contenido['imagen_path'] ?? '';
                $possible_paths = [
                    storage_path('app/public/' . $path_rel),
                    public_path('storage/' . $path_rel),
                    base_path('storage/app/public/' . $path_rel),
                ];
                
                $img_final = null;
                foreach($possible_paths as $p) {
                    if(!empty($path_rel) && file_exists($p)) {
                        $img_final = $p;
                        break;
                    }
                }

                $base64 = null;
                if($img_final) {
                    $type = pathinfo($img_final, PATHINFO_EXTENSION);
                    $data = file_get_contents($img_final);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            @endphp
            @if($base64)
                <img src="{{ $base64 }}" style="width: 100%; max-width: 700px; border: 1px solid #e2e8f0; border-radius: 5px;">
            @else
                <div style="padding: 15px; border: 1px dashed #cbd5e1; color: #94a3b8; font-style: italic; background: #f8fafc;">
                    [ La imagen del croquis no pudo renderizarse o no existe en el servidor ]
                </div>
            @endif
        </div>
    @endif

    {{-- 3. DETALLE POR TIPO --}}
    @if(count($elementos) > 0)
    <div class="section-title">{{ $n++ }}. Detalle de Elementos del Croquis</div>

    @php
        $tipoBadgeClass = [
            'ambiente'  => 'badge-ambiente',
            'pasillo'   => 'badge-pasillo',
            'hardware'  => 'badge-hardware',
            'puerta'    => 'badge-puerta',
            'calle'     => 'badge-calle',
            'sistema'   => 'badge-sistema',
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="18%">Tipo</th>
                <th width="18%">Subtipo</th>
                <th width="29%">Nombre / Etiqueta</th>
                <th width="15%" class="text-center">Dimensiones (px)</th>
                <th width="15%" class="text-center">Atributos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($elementos as $i => $el)
            @php
                $tipo    = strtolower($el['type']    ?? 'otro');
                $subtype = strtolower($el['subtype'] ?? '---');
                $nombre  = strtoupper($el['name']    ?? $subtype);
                $attrs   = $el['attrs'] ?? [];
                $attrStr = [];
                if (!empty($attrs['wifi']))  $attrStr[] = 'WiFi';
                if (!empty($attrs['light'])) $attrStr[] = 'Luz';
                $badgeClass = $tipoBadgeClass[$tipo] ?? '';
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $tipo }}</span></td>
                <td class="uppercase">{{ $subtype }}</td>
                <td class="uppercase font-bold">{{ $nombre }}</td>
                <td class="text-center">{{ round($el['w'] ?? 0) }} × {{ round($el['h'] ?? 0) }}</td>
                <td class="text-center">{{ implode(', ', $attrStr) ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($totalConexiones > 0)
    <div class="leyenda-box">
        <strong>Conexiones de Red:</strong> {{ $totalConexiones }} conexión(es) registradas entre elementos de hardware.
    </div>
    @endif

    @else
    <div style="color: #94a3b8; font-style: italic; padding: 12px; border: 1px solid #e2e8f0; text-align: center;">
        El croquis no contiene elementos registrados.
    </div>
    @endif

    {{-- 4. LEYENDA --}}
    <div class="section-title">{{ $n++ }}. Leyenda de Tipos</div>
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Subtipo(s) posibles</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-ambiente">Ambiente</span></td>
                <td>Espacio físico de atención o servicio</td>
                <td class="uppercase" style="font-size:9px">Consultorio, Emergencias, Quirófano, Administración, Baño</td>
            </tr>
            <tr>
                <td><span class="badge badge-pasillo">Pasillo</span></td>
                <td>Corredor o área de circulación</td>
                <td>—</td>
            </tr>
            <tr>
                <td><span class="badge badge-hardware">Hardware</span></td>
                <td>Equipo de red o infraestructura eléctrica</td>
                <td class="uppercase" style="font-size:9px">Router, AP, Switch, Pozo a tierra</td>
            </tr>
            <tr>
                <td><span class="badge badge-puerta">Puerta</span></td>
                <td>Acceso entre ambientes</td>
                <td class="uppercase" style="font-size:9px">Interna, Externa</td>
            </tr>
            <tr>
                <td><span class="badge badge-calle">Calle</span></td>
                <td>Vía pública de referencia</td>
                <td class="uppercase" style="font-size:9px">Avenida, Jirón, Pasaje</td>
            </tr>
            <tr>
                <td><span class="badge badge-sistema">Sistema</span></td>
                <td>Software de salud implementado</td>
                <td class="uppercase" style="font-size:9px">TUA, SIHCE, SISMED, HISMINSA</td>
            </tr>
        </tbody>
    </table>

    {{-- 5. FIRMA --}}
    <div class="firma-section">
        <div class="section-title">{{ $n++ }}. Firma</div>
        <table class="firma-table">
            <tr>
                <td class="firma-cell">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre">{{ $acta->responsable ?? '____________________________' }}</div>
                    <div class="firma-label">Jefe / Responsable del Establecimiento</div>
                </td>
                <td class="firma-cell">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre">{{ $monitor['nombre'] }}</div>
                    <div class="firma-label">Monitor ICATEC</div>
                    <div class="firma-label">{{ $monitor['tipo_doc'] }}: {{ $monitor['documento'] }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
