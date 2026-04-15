@extends('layouts.usuario')

@section('title', 'Banco de Firmas - SIHCE')

@section('header-content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <i data-lucide="signature" class="w-8 h-8 text-indigo-600"></i>
                BANCO DE FIRMAS
            </h1>
            <p class="text-slate-500 text-sm font-medium">Gestión centralizada de rúbricas para actas digitales</p>
        </div>
        <div>
            <form action="{{ route('admin.firmas.harvest') }}" method="POST">
                @csrf
                <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    Sincronizar desde Historial
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Filtros y Búsqueda --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 p-6">
        <form action="{{ route('admin.firmas.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Buscar Profesional</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="DNI, Nombres o Apellidos..."
                        class="w-full pl-12 pr-4 py-3 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-medium">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Estado de Firma</label>
                <select name="estado" class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-medium py-3">
                    <option value="">Todos los registros</option>
                    <option value="con_firma" {{ request('estado') == 'con_firma' ? 'selected' : '' }}>Con firma (Manual o Digital)</option>
                    <option value="sin_firma" {{ request('estado') == 'sin_firma' ? 'selected' : '' }}>Pendientes</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-slate-800 text-white font-bold py-3 rounded-2xl hover:bg-slate-900 transition-all shadow-lg flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.firmas.index') }}" class="bg-slate-100 text-slate-600 font-bold py-3 px-4 rounded-2xl hover:bg-slate-200 transition-all flex items-center justify-center">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla de Profesionales --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider">Profesional</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider text-center">Tipo / Estado</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider">Última Actualización</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($profesionales as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
                                        {{ substr($p->nombres, 0, 1) }}{{ substr($p->apellido_paterno, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $p->apellido_paterno }} {{ $p->apellido_materno }}</p>
                                        <p class="text-xs text-slate-500 font-medium">{{ $p->nombres }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono font-bold text-slate-600">
                                {{ $p->doc }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->tipo_firma === 'DIGITAL')
                                    <div class="inline-flex flex-col items-center gap-1">
                                        <div class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-wider">
                                            Firma Digital
                                        </div>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tight">(Automática)</span>
                                    </div>
                                @elseif($p->firma_path)
                                    <div class="inline-flex flex-col items-center gap-1">
                                        <div class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-wider">
                                            Manual
                                        </div>
                                        <img src="{{ Storage::url($p->firma_path) }}" alt="Firma" class="h-8 object-contain bg-white rounded border border-slate-200 p-1">
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-wider">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                        Pendiente
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                {{ $p->ultima_actualizacion_firma ? $p->ultima_actualizacion_firma->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="previewSignature('{{ $p->id }}', '{{ $p->apellido_paterno }} {{ $p->apellido_materno }} {{ $p->nombres }}', '{{ $p->doc }}', '{{ $p->tipo_firma }}', '{{ $p->firma_path ? Storage::url($p->firma_path) : '' }}', '{{ $p->cargo }}')"
                                        class="p-2.5 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-800 hover:text-white transition-all pointer" title="Vista Previa">
                                        <i data-lucide="eye" class="w-5 h-5"></i>
                                    </button>
                                    <button onclick="openUploadModal('{{ $p->id }}', '{{ $p->apellido_paterno }} {{ $p->apellido_materno }} {{ $p->nombres }}', '{{ $p->tipo_firma }}')" 
                                        class="p-2.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all pointer">
                                        <i data-lucide="settings" class="w-5 h-5"></i>
                                    </button>
                                    @if($p->firma_path)
                                        <form action="{{ route('admin.firmas.destroy', $p->id) }}" method="POST" onsubmit="return confirm('¿Eliminar la firma?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all pointer">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i data-lucide="users" class="w-12 h-12 text-slate-200 mx-auto mb-4"></i>
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">No se encontraron profesionales</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($profesionales->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $profesionales->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal de Ajustes/Carga --}}
<div id="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeUploadModal()"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="bg-indigo-600 px-6 py-6 text-white relative">
                <button onclick="closeUploadModal()" class="absolute top-6 right-6 text-white/80 hover:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
                <div class="flex items-center gap-3 mb-1">
                    <i data-lucide="signature" class="w-6 h-6 text-indigo-300"></i>
                    <h3 class="text-xl font-black uppercase tracking-tight">Gestionar Firma</h3>
                </div>
                <p class="text-indigo-100 text-sm font-medium" id="profesionalName">Cargando...</p>
            </div>

            <form id="uploadForm" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-3">Modo de Firma</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-all has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-300 group">
                            <input type="radio" name="tipo_firma" value="MANUAL" id="tipoManual" class="absolute opacity-0">
                            <i data-lucide="upload-cloud" class="w-5 h-5 mb-2 text-slate-400 group-has-[:checked]:text-indigo-600"></i>
                            <span class="text-xs font-bold text-slate-700 group-has-[:checked]:text-indigo-900">Manual (PNG)</span>
                        </label>
                        <label class="relative flex flex-col p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-all has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-300 group">
                            <input type="radio" name="tipo_firma" value="DIGITAL" id="tipoDigital" class="absolute opacity-0">
                            <i data-lucide="shield-check" class="w-5 h-5 mb-2 text-slate-400 group-has-[:checked]:text-indigo-600"></i>
                            <span class="text-xs font-bold text-slate-700 group-has-[:checked]:text-indigo-900">Digital (Auto)</span>
                        </label>
                    </div>
                </div>

                <div id="manualInputContainer" class="relative">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Cargar Nueva Imagen</label>
                    <div id="drop-area" class="border-2 border-dashed border-slate-200 rounded-3xl p-6 text-center hover:border-indigo-400 hover:bg-indigo-50/30 transition-all group pointer">
                        <i data-lucide="image" class="w-8 h-8 text-slate-300 mx-auto mb-2 group-hover:text-indigo-400 transition-colors"></i>
                        <p class="text-xs font-bold text-slate-600 mb-1">Click para seleccionar</p>
                        <input type="file" name="firma" id="firmaInput" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <div id="preview-container" class="mt-4 hidden animate-in fade-in zoom-in duration-300">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 flex items-center justify-center">
                            <img id="image-preview" src="#" alt="Vista previa" class="max-h-24 object-contain">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeUploadModal()" class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                        Cerrar
                    </button>
                    <button type="submit" class="flex-2 px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de Vista Previa --}}
<div id="previewSignatureModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" onclick="closePreviewModal()"></div>

        <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden transform transition-all border border-slate-100">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="shield-check" class="w-8 h-8 text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-1">Vista Previa</h3>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-6" id="previewProfName">PROFESIONAL</p>
                
                <div id="previewManualContainer" class="hidden mb-6">
                    <div class="bg-slate-50 p-6 rounded-3xl border border-dashed border-slate-200 flex items-center justify-center min-h-[150px]">
                        <img id="previewManualImg" src="" class="max-h-32 object-contain" alt="Firma Manual">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase">Rúbrica Manual Registrada</p>
                </div>

                <div id="previewDigitalContainer" class="hidden mb-6">
                    <div class="flex justify-center">
                        <x-signature-preview id="previewDigitalStamp" profesional="..." doc="..." cargo="..." />
                    </div>
                    <p class="text-[10px] text-slate-400 mt-4 font-bold uppercase italic">Sello generado automáticamente en PDF</p>
                </div>

                <button onclick="closePreviewModal()" class="w-full py-4 bg-slate-100 text-slate-600 font-black rounded-2xl hover:bg-slate-200 transition-all uppercase tracking-widest text-xs">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('uploadModal');
        const dropArea = document.getElementById('drop-area');
        const input = document.getElementById('firmaInput');
        const profNameElem = document.getElementById('profesionalName');
        const form = document.getElementById('uploadForm');
        const manualContainer = document.getElementById('manualInputContainer');

        const baseUrl = "{{ url('/') }}";

        window.openUploadModal = function(id, name, currentType) {
            profNameElem.innerText = name;
            form.action = `${baseUrl}/admin/banco-firmas/${id}/upload`;
            
            if (currentType === 'DIGITAL') {
                document.getElementById('tipoDigital').checked = true;
                manualContainer.style.opacity = '0.5';
                manualContainer.style.pointerEvents = 'none';
            } else {
                document.getElementById('tipoManual').checked = true;
                manualContainer.style.opacity = '1';
                manualContainer.style.pointerEvents = 'auto';
            }

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            if (window.refreshLucide) window.refreshLucide();
        }

        window.closeUploadModal = function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('preview-container').classList.add('hidden');
            input.value = '';
        }

        document.querySelectorAll('input[name="tipo_firma"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'DIGITAL') {
                    manualContainer.style.opacity = '0.5';
                    manualContainer.style.pointerEvents = 'none';
                    input.value = '';
                    document.getElementById('preview-container').classList.add('hidden');
                } else {
                    manualContainer.style.opacity = '1';
                    manualContainer.style.pointerEvents = 'auto';
                }
            });
        });

        window.previewSignature = function(id, name, doc, type, path, cargo) {
            const pvModal = document.getElementById('previewSignatureModal');
            document.getElementById('previewProfName').innerText = name;
            
            const manualCont = document.getElementById('previewManualContainer');
            const digitalCont = document.getElementById('previewDigitalContainer');

            if (type === 'DIGITAL') {
                manualCont.classList.add('hidden');
                digitalCont.classList.remove('hidden');
                
                // Actualizar contenido del componente (DOM directo ya que no es reactivo tras carga)
                const stamp = document.getElementById('previewDigitalStamp');
                if (stamp) {
                    stamp.querySelector('span').innerText = name.toUpperCase();
                    // Buscar por texto ya que es Blade estático
                    const textDiv = stamp.querySelector('.text-\\[8px\\]');
                    if (textDiv) {
                        textDiv.innerHTML = `Firmado digitalmente por:<br><span class="font-bold text-[9px]">${name.toUpperCase()}</span><br>DNI: ${doc}<br>Motivo: En señal de conformidad<br>Fecha: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
                    }
                }
            } else if (path) {
                digitalCont.classList.add('hidden');
                manualCont.classList.remove('hidden');
                document.getElementById('previewManualImg').src = path;
            } else {
                Swal.fire({
                    icon: 'info',
                    text: 'Este profesional no tiene una firma registrada actualmente.',
                    confirmButtonText: 'Entendido',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-2xl' }
                });
                return;
            }

            pvModal.style.display = 'block';
            if (window.refreshLucide) window.refreshLucide();
        }

        window.closePreviewModal = function() {
            document.getElementById('previewSignatureModal').style.display = 'none';
        }

        window.previewImage = function(input) {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('preview-container');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        if (dropArea) {
            dropArea.addEventListener('click', () => input.click());
        }

        @if(session('success'))
            Swal.fire({ icon: 'success', title: '¡Perfecto!', text: '{{ session('success') }}', timer: 4000, showConfirmButton: false, customClass: { popup: 'rounded-3xl' } });
        @endif

        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Atención', text: '{{ session('error') }}', customClass: { popup: 'rounded-3xl' } });
        @endif
    });
</script>
@endpush
@endsection
