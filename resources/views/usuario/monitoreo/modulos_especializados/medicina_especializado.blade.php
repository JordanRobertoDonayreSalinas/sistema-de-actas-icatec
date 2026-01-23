@extends('layouts.usuario')

@section('title', 'Consulta Externa - Medicina')

@section('content')
    <div class="py-10 bg-[#F8F9FC] min-h-screen font-sans text-slate-600">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. CABECERA (Tu código original) --}}
            <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span
                            class="px-3 py-1 bg-teal-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Módulo
                            Especializado</span>
                        <span class="text-slate-400 font-bold text-[10px] uppercase">ID Acta:
                            #{{ str_pad($acta->numero_acta ?? $acta->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">02. Triaje</h2>
                    <p class="text-slate-500 font-bold uppercase text-xs mt-1">
                        <i data-lucide="clipboard-pulse" class="inline-block w-4 h-4 mr-1 text-teal-500"></i>
                        {{ $acta->establecimiento->nombre }}
                    </p>
                </div>
                <a href="{{ route('usuario.monitoreo.modulos', $acta->id) }}"
                    class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 rounded-2xl text-slate-600 font-black text-xs hover:bg-slate-50 transition-all uppercase shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al Panel
                </a>
            </div>

            {{-- 2. FORMULARIO CON LOS COMPONENTES --}}
            {{-- Asegúrate de definir la ruta correcta en el action --}}
            <form action="{{ route('usuario.monitoreo.sm_medicina_general.store', $acta->id) }}" method="POST">
                @csrf

                {{-- Contenedor de componentes con espaciado vertical --}}
                <div class="space-y-6">

                    {{-- Componente 1: Detalle de Consultorio --}}
                    {{-- Nota: Laravel infiere el nombre del componente basado en el nombre del archivo --}}
                    <x-esp_1_detalleDeConsultorio :detalle="$detalle" />

                    {{-- Componente 2: Datos Profesional --}}
                    <x-esp_2_datosProfesional :detalle="$detalle" :prefix="$prefix" />

                    {{-- Componente 3: Detalle DNI --}}
                    <x-esp_3_detalleDni :detalle="$detalle" color="teal" />

                    <x-esp_4_detalleCap :model="json_encode($detalle->contenido ?? [])" />

                    {{-- Componente 5: Equipos --}}
                    <x-esp_5_equipos model="form.inventario" />

                    {{-- Componente 6: Soporte --}}
                    <x-esp_6_soporte :detalle="$detalle" />

                    <x-esp_7_comentariosEvid :detalle="$detalle" />

                </div>

                {{-- 3. BARRA DE ACCIONES (BOTÓN GUARDAR) --}}
                <div class="mt-8 flex items-center justify-end border-t border-slate-200 pt-6">
                    <button type="submit"
                        class="flex items-center gap-2 px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 active:scale-95 transition-all shadow-lg shadow-indigo-200">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Guardar Ficha
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection
