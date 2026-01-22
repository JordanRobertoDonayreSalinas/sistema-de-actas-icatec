<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte Citas CSMC</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #0f766e; padding-bottom: 10px; }
        .header .title { font-weight: bold; font-size: 16px; color: #0f766e; margin-bottom: 5px; text-transform: uppercase; }
        .header .sub { font-size: 12px; color: #555; font-weight: bold; text-transform: uppercase; }
        .header .date { font-size: 10px; color: #777; margin-top: 5px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; vertical-align: top; }
        
        /* Estilos específicos CSMC (Teal/Esmeralda) */
        th { background-color: #f0fdfa; color: #0f766e; font-size: 10px; text-transform: uppercase; }
        
        .check { color: #059669; font-weight: bold; font-size: 10px; text-transform: uppercase; } /* Green-600 */
        .cross { color: #dc2626; font-weight: bold; font-size: 10px; text-transform: uppercase; } /* Red-600 */
        
        .obs-text { font-size: 10px; color: #475569; font-style: italic; }
        
        .evidence-box { margin-top: 20px; border: 1px solid #e2e8f0; padding: 10px; text-align: center; page-break-inside: avoid; }
        .evidence-title { font-weight: bold; color: #0f766e; margin-bottom: 10px; font-size: 12px; text-align: left; text-transform: uppercase; border-bottom: 1px solid #f0fdfa; padding-bottom: 5px; }
    </style>
</head>
<body>
    {{-- ENCABEZADO --}}
    <div class="header">
        <div class="title">MÓDULO DE ADMISIÓN Y CITAS - CSMC</div>
        <div class="sub">{{ $monitoreo->establecimiento->nombre }}</div>
        <div class="date">Fecha de Monitoreo: {{ \Carbon\Carbon::parse($monitoreo->fecha)->format('d/m/Y') }}</div>
    </div>

    {{-- TABLA DE CRITERIOS --}}
    <table>
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">#</th>
                <th width="50%">Criterio de Evaluación</th>
                <th width="15%" style="text-align: center;">Estado</th>
                <th width="30%">Observaciones / Hallazgos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">01</td>
                <td>¿El CSMC cuenta con un sistema de admisión diferenciado que garantice la confidencialidad y el trato humanizado?</td>
                <td style="text-align: center;">
                    @if(isset($data['criterio_1']) && $data['criterio_1'] == '1') 
                        <span class="check">CUMPLE</span>
                    @else 
                        <span class="cross">NO CUMPLE</span> 
                    @endif
                </td>
                <td class="obs-text">{{ $data['obs_1'] ?? 'Sin observaciones' }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">02</td>
                <td>¿Se ofertan turnos para psiquiatría, psicología y terapia en horarios diferenciados (Mañana/Tarde)?</td>
                <td style="text-align: center;">
                    @if(isset($data['criterio_2']) && $data['criterio_2'] == '1') 
                        <span class="check">CUMPLE</span>
                    @else 
                        <span class="cross">NO CUMPLE</span> 
                    @endif
                </td>
                <td class="obs-text">{{ $data['obs_2'] ?? 'Sin observaciones' }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">03</td>
                <td>¿Existe mecanismo activo de rescate de pacientes (Visitas/Llamadas) para continuidad de cuidados?</td>
                <td style="text-align: center;">
                    @if(isset($data['criterio_3']) && $data['criterio_3'] == '1') 
                        <span class="check">CUMPLE</span>
                    @else 
                        <span class="cross">NO CUMPLE</span> 
                    @endif
                </td>
                <td class="obs-text">{{ $data['obs_3'] ?? 'Sin observaciones' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- EVIDENCIA FOTOGRÁFICA --}}
    @if(!empty($data['foto_evidencia']))
        <div class="evidence-box">
            <div class="evidence-title">EVIDENCIA FOTOGRÁFICA REGISTRADA</div>
            {{-- Usamos public_path para asegurar que DomPDF encuentre la imagen en el disco --}}
            <img src="{{ public_path('storage/' . $data['foto_evidencia']) }}" style="max-width: 100%; max-height: 450px; object-fit: contain;">
        </div>
    @endif
</body>
</html>