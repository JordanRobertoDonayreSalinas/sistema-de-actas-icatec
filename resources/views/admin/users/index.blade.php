@extends('layouts.app')

@section('title', 'Gestionar usuarios')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Gestionar usuarios</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <span>Panel del Administrador</span>
        <span class="text-slate-300">•</span>
        <span>Gestionar Usuarios</span>
    </div>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Alertas PHP estándar --}}
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100 flex items-center gap-3 shadow-sm animate-fade-in-down">
                <div class="p-2 bg-emerald-100 rounded-full text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-red-50 text-red-800 border border-red-100 flex items-center gap-3 shadow-sm">
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        {{-- TABLA DE USUARIOS --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            
            {{-- Toolbar --}}
            <div class="p-6 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Listado de Usuarios</h2>
                </div>
                <a href="{{ route('admin.users.create') }}" class="group relative inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-semibold text-sm transition-all shadow-lg shadow-indigo-500/30 active:scale-95">
                    <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Usuario
                </a>
            </div>

            <div class="overflow-x-auto custom-scroll">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[11px] uppercase tracking-wider font-bold border-b border-slate-100">
                            <th class="px-6 py-4">Usuario</th>
                            <th class="px-6 py-4">Credenciales</th>
                            <th class="px-6 py-4 text-center">Rol</th>
                            <th class="px-6 py-4 text-center">Acceso Rápido</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                
                                {{-- Avatar y Nombre --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-100 to-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm border border-white shadow-sm">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-700 text-sm">{{ $user->name }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">ID: {{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Username --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-slate-600 font-mono bg-slate-100 px-2 py-1 rounded">{{ $user->username }}</span>
                                    </div>
                                </td>

                                {{-- Rol --}}
                                <td class="px-6 py-4 text-center">
                                    @if($user->role === 'admin')
                                        <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold bg-violet-100 text-violet-700 border border-violet-200">ADMIN</span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">USUARIO</span>
                                    @endif
                                </td>

                                {{-- Switch AJAX --}}
                                <td class="px-6 py-4 text-center">
                                    @if($user->id !== Auth::id())
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer status-toggle" 
                                                data-id="{{ $user->id }}" 
                                                {{ $user->is_active ? 'checked' : '' }}>
                                            <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                            <span class="ms-2 text-xs font-medium text-slate-500 status-text">
                                                {{ $user->is_active ? 'Activo' : 'Bloq.' }}
                                            </span>
                                        </label>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Tu cuenta</span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-50 bg-slate-50/50 flex justify-center">{{ $users->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Notificación Toast Flotante --}}
    <div id="toast-success" class="fixed top-24 right-5 z-50 flex items-center w-full max-w-xs p-4 space-x-3 text-emerald-500 bg-white rounded-xl shadow-2xl border border-emerald-100 transform transition-all duration-300 translate-x-full opacity-0 pointer-events-none">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-emerald-500 bg-emerald-100 rounded-lg">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
        </div>
        <div class="pl-2 text-sm font-semibold text-slate-700" id="toast-message">Estado actualizado.</div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggles = document.querySelectorAll('.status-toggle');
            const toast = document.getElementById('toast-success');
            const toastMessage = document.getElementById('toast-message');

            function showToast(message) {
                toastMessage.textContent = message;
                toast.classList.remove('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                }, 3000);
            }

            toggles.forEach(toggle => {
                toggle.addEventListener('change', function () {
                    const userId = this.getAttribute('data-id');
                    const isChecked = this.checked;
                    const statusText = this.closest('label').querySelector('.status-text');

                    statusText.textContent = isChecked ? 'Activo' : 'Bloq.';

                    // CORRECCIÓN: Usamos la función route() de Blade para obtener la URL correcta
                    // La ruta ahora es /admin/gestionar-usuarios/{user}/toggle-status
                    const url = `{{ url('/admin/gestionar-usuarios') }}/${userId}/toggle-status`;

                    fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la red');
                        return response.json();
                    })
                    .then(data => {
                        showToast(data.message || 'Estado actualizado correctamente');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revertir el switch si falla
                        this.checked = !isChecked;
                        statusText.textContent = !isChecked ? 'Activo' : 'Bloq.';
                        alert('Hubo un error al actualizar el estado.');
                    });
                });
            });
        });
    </script>
@endpush