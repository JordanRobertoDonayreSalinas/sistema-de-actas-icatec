@extends('layouts.usuario')

@section('title', 'Responder Incidencia #' . $incidencia->id)

@section('header-content')
    <div class="flex items-center gap-3">
        <a href="{{ route('usuario.mesa-ayuda.index') }}"
            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                Incidencia <span class="text-orange-600">#{{ $incidencia->id }}</span>
            </h1>
            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                <span>Mesa de Ayuda</span>
                <span class="text-slate-300">•</span>
                <span>Responder reporte</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- IZQUIERDA: Datos de la incidencia --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Estado --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Estado actual</p>
                @if($incidencia->estado === 'Pendiente')
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-amber-700 bg-amber-100 px-3 py-1.5 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Pendiente
                    </span>
                @elseif($incidencia->estado === 'En proceso')
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-blue-700 bg-blue-100 px-3 py-1.5 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> En proceso
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Resuelto
                    </span>
                @endif
                <p class="text-[11px] text-slate-400 mt-2">
                    Reportado el {{ $incidencia->created_at->format('d/m/Y \a \l\a\s H:i') }} h
                </p>
            </div>

            {{-- Profesional --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Profesional</p>
                <div>
                    <p class="font-bold text-slate-800">{{ $incidencia->apellidos }}, {{ $incidencia->nombres }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">DNI: {{ $incidencia->dni }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <div>
                        <span class="font-bold text-slate-400 uppercase text-[10px]">Celular</span>
                        <p class="font-medium mt-0.5">{{ $incidencia->celular }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-slate-400 uppercase text-[10px]">Correo</span>
                        <p class="font-medium mt-0.5 truncate" title="{{ $incidencia->correo }}">{{ $incidencia->correo }}</p>
                    </div>
                </div>
            </div>

            {{-- Establecimiento --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Establecimiento</p>
                <div>
                    <p class="font-bold text-slate-800 text-sm">{{ $incidencia->nombre_establecimiento }}</p>
                    <span class="text-[11px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md inline-block mt-1">
                        {{ $incidencia->categoria }} · IPRESS {{ $incidencia->codigo_ipress }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <div>
                        <span class="font-bold text-slate-400 uppercase text-[10px]">Distrito</span>
                        <p class="font-medium mt-0.5">{{ $incidencia->distrito_establecimiento }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-slate-400 uppercase text-[10px]">Provincia</span>
                        <p class="font-medium mt-0.5">{{ $incidencia->provincia_establecimiento }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-slate-400 uppercase text-[10px]">Red</span>
                        <p class="font-medium mt-0.5 text-[11px]">{{ $incidencia->red }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-slate-400 uppercase text-[10px]">Microred</span>
                        <p class="font-medium mt-0.5 text-[11px]">{{ $incidencia->microred }}</p>
                    </div>
                </div>
            </div>

            {{-- Módulo --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Módulo SIHCE</p>
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-orange-700 bg-orange-50 border border-orange-200 px-3 py-1.5 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                    {{ str_replace('_', ' ', ucwords($incidencia->modulos, '_')) }}
                </span>
            </div>
        </div>

        {{-- DERECHA: Observación, imágenes y formulario --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Observación --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Descripción del problema</p>
                <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 rounded-xl p-4 border border-slate-100">
                    {{ $incidencia->observacion }}
                </p>
            </div>

            {{-- Imágenes de evidencia --}}
            @php
                $imagenes = array_filter([$incidencia->imagen1, $incidencia->imagen2, $incidencia->imagen3]);
            @endphp
            @if(count($imagenes) > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Evidencia fotográfica ({{ count($imagenes) }})</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($imagenes as $img)
                    <a href="{{ asset('storage/' . $img) }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 hover:border-orange-400 transition group">
                        <img src="{{ asset('storage/' . $img) }}" alt="Evidencia"
                            class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Historial de respuestas --}}
            @if($incidencia->respuestas->count() > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">
                    Historial de respuestas ({{ $incidencia->respuestas->count() }})
                </p>
                <div class="space-y-4">
                    @foreach($incidencia->respuestas->sortByDesc('created_at') as $resp)
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-600">
                                {{ $resp->usuario->name ?? 'Técnico' }}
                            </span>
                            <div class="flex items-center gap-2">
                                @if($resp->estado === 'Resuelto')
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Resuelto</span>
                                @else
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">En proceso</span>
                                @endif
                                <span class="text-[10px] text-slate-400">{{ $resp->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-700">{{ $resp->respuesta }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- FORMULARIO DE RESPUESTA --}}
            <div class="bg-white rounded-2xl border border-orange-200 shadow-sm p-6">
                <p class="text-xs font-bold text-orange-500 uppercase tracking-widest mb-4">Nueva Respuesta</p>

                <form action="{{ route('usuario.mesa-ayuda.guardar-respuesta', $incidencia->id) }}"
                    method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                            Respuesta técnica *
                        </label>
                        <textarea name="respuesta" rows="5" maxlength="2000" required
                            placeholder="Describa la acción tomada, solución aplicada o pasos a seguir..."
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition resize-none @error('respuesta') border-red-400 @enderror">{{ old('respuesta') }}</textarea>
                        @error('respuesta')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                            Actualizar estado *
                        </label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="estado" value="En proceso" class="accent-blue-600"
                                    {{ old('estado', 'En proceso') === 'En proceso' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-blue-700">En proceso</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="estado" value="Resuelto" class="accent-emerald-600"
                                    {{ old('estado') === 'Resuelto' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-emerald-700">Resuelto</span>
                            </label>
                        </div>
                        @error('estado')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                            Imágenes de evidencia (opcional, hasta 3)
                        </label>
                        <input type="file" name="imagenes[]" multiple accept=".jpg,.jpeg,.png"
                            class="text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 transition">
                        <p class="text-xs text-slate-400 mt-1">JPG/PNG, máx 5 MB por imagen.</p>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-bold px-6 py-2.5 rounded-xl transition-all shadow-md shadow-orange-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Guardar Respuesta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
