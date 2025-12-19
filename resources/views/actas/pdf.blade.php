@extends('layouts.panel')

{{-- 1. ESTILOS ESPECÍFICOS DEL DOCUMENTO (Solo CSS visual) --}}
@push('styles')
    <style>
        /* Ocultar el Footer global (Copyright) solo en esta vista */
        footer { display: none !important; }

        /* Estilos tipo "Papel" para el documento */
        .acta-documento {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12px;
            color: #2e3a46;
            line-height: 1.45;
        }

        .acta-documento h1 {
            text-align: center;
            color: #0d47a1;
            font-size: 22px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 3px solid #1976d2;
            padding-bottom: 6px;
        }

        .acta-documento h3 {
            margin-top: 26px;
            margin-bottom: 10px;
            color: #0d47a1;
            font-size: 15px;
            font-weight: bold;
            border-left: 4px solid #1976d2;
            padding-left: 8px;
        }

        .info-card {
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            padding: 10px 12px;
            margin-top: 8px;
            background: #f9fbfd;
        }

        .info-general { width: 100%; border-collapse: collapse; font-size: 12px; }
        .info-general tr { border-bottom: 1px solid #eef3f7; }
        .info-general td { padding: 8px 10px; vertical-align: top; }
        .info-general td.label { width: 30%; font-weight: bold; color: #0d47a1; }
        .info-general td.value { color: #24303a; }

        /* TABLA PARTICIPANTES (CORREGIDA: CENTRADO) */
        table.participantes { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 12px; }
        table.participantes thead th {
            background: #1976d2; 
            color: #ffffff; 
            padding: 7px 8px; 
            font-weight: bold; 
            font-size: 12px;
            text-align: center; /* Centrar encabezados */
        }
        table.participantes tbody td {
            border: 1px solid #e6ecf3; 
            padding: 8px; 
            color: #24303a; 
            font-size: 11px;
            text-align: center; /* Centrar contenido de celdas */
            vertical-align: middle;
        }
        /* Opcional: Si quieres que los Nombres/Apellidos sigan a la izquierda, descomenta esto:
        table.participantes tbody td:nth-child(2),
        table.participantes tbody td:nth-child(3) { text-align: left; padding-left: 12px; } 
        */
        
        table.participantes tbody tr:nth-child(even) td { background: #f4f8fc; }

        .acta-documento ul { margin: 6px 0 0 20px; padding: 0; font-size: 12px; }
        .acta-documento li { margin-bottom: 6px; color: #24303a; }

        /* Firmas */
        table.firmas { width: 100%; border-collapse: collapse; margin-top: 25px; }
        table.firmas td {
            width: 50%; text-align: center; vertical-align: bottom;
            padding: 35px 10px 15px 10px; border: 1px solid #cfd8dc;
        }
        table.firmas .linea {
            display: block; border-top: 1px solid #000;
            margin: 40px auto 8px auto; width: 80%; height: 30px;
        }
        table.firmas b { display: block; margin-top: 4px; font-size: 13px; color: #0d47a1; }
        table.firmas small { font-size: 11px; color: #555; }

        /* Impresión */
        @media print { 
            nav, aside, footer, .no-print { display: none !important; }
            .py-10 { padding: 0 !important; margin: 0 !important; }
            .max-w-4xl { max-width: 100% !important; box-shadow: none !important; border: none !important; }
            body, .bg-gradient-to-r { background: white !important; }
        }
    </style>
@endpush

{{-- 2. CONTENIDO PRINCIPAL --}}
@section('content')
    {{-- FONDO IDÉNTICO A create.blade.php --}}
    <div class="py-10 bg-gradient-to-r from-blue-50 to-indigo-50 min-h-screen">
        
        {{-- Barra Superior de Botones ELIMINADA --}}

        {{-- Hoja del Documento --}}
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-200">
            <div class="p-12 acta-documento">
                
                <h1>ACTA DE ASISTENCIA TÉCNICA - {{ $acta->id }}</h1>

                <h3>📑 Información General</h3>
                <div class="info-card">
                    <table class="info-general">
                        <tbody>
                            <tr><td class="label">Fecha</td><td class="value">{{ \Carbon\Carbon::parse($acta->fecha)->format('d/m/Y') }}</td></tr>
                            <tr><td class="label">Establecimiento</td><td class="value">{{ $acta->establecimiento->nombre ?? '---' }}</td></tr>
                            <tr><td class="label">Jefe del Establecimiento</td><td class="value">{{ $acta->responsable }}</td></tr>
                            <tr><td class="label">Tema</td><td class="value">{{ $acta->tema }}</td></tr>
                            <tr><td class="label">Modalidad</td><td class="value">{{ $acta->modalidad }}</td></tr>
                            <tr><td class="label">Implementador</td><td class="value">{{ $acta->implementador }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3>👥 Participantes</h3>
                <table class="participantes">
                    <thead>
                        <tr><th>DNI</th><th>Apellidos</th><th>Nombres</th><th>Cargo</th><th>Módulo</th></tr>
                    </thead>
                    <tbody>
                        @php
                            $participantesTabla = $acta->participantes->filter(fn($p) => !empty($p->modulo));
                        @endphp

                        @forelse($participantesTabla as $p)
                            <tr>
                                <td>{{ $p->dni }}</td>
                                <td>{{ $p->apellidos }}</td>
                                <td>{{ $p->nombres }}</td>
                                <td>{{ $p->cargo }}</td>
                                <td>{{ $p->modulo }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; color:#999;">Sin participantes con módulo registrado</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h3>📌 Actividades</h3>
                <ul>
                    @forelse($acta->actividades as $a)
                        <li>{{ $a->descripcion }}</li>
                    @empty
                        <li style="color:#999;">Sin actividades registradas</li>
                    @endforelse
                </ul>

                <h3>🤝 Acuerdos</h3>
                <ul>
                    @forelse($acta->acuerdos as $a)
                        <li>{{ $a->descripcion }}</li>
                    @empty
                        <li style="color:#999;">Sin acuerdos registrados</li>
                    @endforelse
                </ul>

                <h3>📝 Observaciones</h3>
                <ul>
                    @forelse($acta->observaciones as $o)
                        <li>{{ $o->descripcion }}</li>
                    @empty
                        <li style="color:#999;">Sin observaciones registradas</li>
                    @endforelse
                </ul>

                {{-- Evidencias --}}
                @php
                    $imageFields = ['imagen1','imagen2','imagen3','imagen4','imagen5'];
                    $imagenesSrc = [];
                    foreach ($imageFields as $field) {
                        $val = data_get($acta, $field);
                        if (empty($val)) continue;
                        // Limpieza de ruta para compatibilidad
                        $normalized = preg_replace('#^(storage/app/public/|/storage/app/public/|storage/|/storage/|public/|/public/)#i', '', $val);
                        $normalized = ltrim($normalized, '/');
                        
                        // Si existe en disco (modo local/server)
                        $diskPath = storage_path('app/public/' . $normalized);
                        if (file_exists($diskPath)) {
                            $mime = @mime_content_type($diskPath) ?: 'image/jpeg';
                            $data = base64_encode(file_get_contents($diskPath));
                            $imagenesSrc[] = "data:{$mime};base64,{$data}";
                        } else {
                            // Fallback a URL pública
                            $imagenesSrc[] = asset('storage/' . $normalized);
                        }
                    }
                @endphp

                @if(count($imagenesSrc) > 0)
                    <h3 style="text-align:center; color:#0d47a1; font-size:16px; margin-top:25px;">
                        📷 Evidencias Fotográficas
                    </h3>
                    <hr style="border:0; border-top:2px solid #1976d2; width:60%; margin:8px auto 18px auto;">
                    
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px;">
                        @foreach($imagenesSrc as $idx => $src)
                            <div style="width: 45%; background:#fff; border:1px solid #e0e6ed; border-radius:8px; padding:10px; box-shadow:0 2px 6px rgba(0,0,0,0.08); text-align:center;">
                                <img src="{{ $src }}" alt="Evidencia {{ $idx + 1 }}" style="max-width: 100%; height: auto; border-radius:6px; margin-bottom:6px;">
                                <div style="font-size:11px; color:#555; font-weight:bold;">Evidencia {{ $idx + 1 }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:#999; text-align:center; margin-top: 20px;">⚠ No hay evidencias fotográficas disponibles</p>
                @endif

                {{-- Firmas --}}
                <h3>✍ Firmas</h3>
                <table class="firmas">
                    <tr>
                        <td>
                            <span class="linea"></span>
                            <b>Implementador(a)</b><br>
                            {{ $acta->implementador }}
                        </td>
                        <td>
                            <span class="linea"></span>
                            <b>Jefe del Establecimiento</b><br>
                            {{ $acta->responsable }}
                        </td>
                    </tr>
                    @foreach($acta->participantes as $index => $p)
                        @php
                            $etiqueta = !empty($p->modulo) ? 'Módulo' : (!empty($p->unidad_ejecutora) ? 'Unidad Ejecutora' : '');
                            $valor = !empty($p->modulo) ? $p->modulo : (!empty($p->unidad_ejecutora) ? $p->unidad_ejecutora : '');
                        @endphp
                        @if($index % 2 == 0)<tr>@endif
                        <td>
                            <span class="linea"></span>
                            {{ $p->nombres }} {{ $p->apellidos }}
                            @if($valor)<br><small><b>{{ $etiqueta }}:</b> {{ $valor }}</small>@endif
                        </td>
                        @if($index % 2 == 1)</tr>@endif
                    @endforeach
                    @if(count($acta->participantes) % 2 != 0)
                        <td></td></tr>
                    @endif
                </table>

            </div>
        </div>
    </div>
@endsection