@php
    $stampData = $firma ?? null;
    $isDigital = isset($stampData['tipo']) && $stampData['tipo'] === 'DIGITAL';
    $hasManual = isset($stampData['is_manual']) && $stampData['is_manual'];
@endphp

@if($stampData)
    <div style="position: relative; width: 100%; text-align: center;">
        @if($hasManual)
            {{-- Signature Manual (PNG) --}}
            <div style="height: 60px; margin-bottom: -15px;">
                <img src="{{ $stampData['url'] }}" style="max-height: 60px; max-width: 140px; object-fit: contain;">
            </div>
        @elseif($isDigital)
            {{-- Digital Stamp Style (ReFirma Replicated) --}}
            <div style="border: 1px solid #14b8a6; background-color: #f0fdfa; padding: 4px; padding-left: 35px; text-align: left; margin: 0 auto; width: 150px; position: relative;">
                <div style="position: absolute; left: 4px; top: 50%; transform: translateY(-50%); width: 25px; height: 30px; opacity: 0.8;">
                    <img src="{{ public_path('images/logo_minsa_cc.png') }}" style="width: 100%; height: auto;">
                </div>
                <div style="font-size: 6px; color: #115e59; font-weight: bold; line-height: 1;">FIRMA DIGITAL</div>
                <div style="font-size: 5px; color: #134e4a; margin-top: 1px;">
                    Firmado digitalmente por:<br>
                    <span style="font-weight: bold;">{{ strtoupper($stampData['profesional']) }}</span><br>
                    DNI: {{ $stampData['doc'] }}<br>
                    Motivo: En señal de conformidad<br>
                    Fecha: {{ date('d/m/Y H:i') }}
                </div>
            </div>
        @endif
    </div>
@endif
