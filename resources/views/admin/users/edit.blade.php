@extends('layouts.app')

@section('title', 'Editar Usuario - ' . $user->name)

{{-- Esta sección se inyecta en el Header blanco superior del layout --}}
@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Editar Usuario</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 transition-colors">Usuarios</a>
        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span>Editando: <strong class="text-slate-700">{{ $user->name }}</strong></span>
    </div>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">

        {{-- [IMPORTANTE] BLOQUE DE ERRORES: Si algo falla al guardar, aparecerá aquí --}}
        @if ($errors->any())
            <div class="mb-8 p-4 rounded-xl bg-red-50 border border-red-200 shadow-sm animate-pulse">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-red-100 rounded-full text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="font-bold text-red-800">No se pudieron guardar los cambios:</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 ml-12">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- COLUMNA IZQUIERDA: DATOS DEL PERFIL (8 cols) --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/40 border border-slate-100 p-8">
                        
                        <div class="flex items-center gap-3 mb-6 border-b border-slate-50 pb-4">
                            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Datos del Perfil</h3>
                                <p class="text-xs text-slate-500">Información personal y de contacto.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nombre --}}
                            <div class="col-span-2">
                                <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nombre Completo</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('name') border-red-300 @enderror" 
                                    placeholder="Ej: Juan Pérez">
                                @error('name') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email (CRUCIAL PARA EVITAR ERROR SQL 1364) --}}
                            <div class="col-span-2">
                                <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('email') border-red-300 @enderror" 
                                    placeholder="ejemplo@empresa.com">
                                @error('email') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- DNI --}}
                            <div>
                                <label for="username" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Usuario (DNI)</label>
                                <input type="number" name="username" id="username" value="{{ old('username', $user->username) }}" 
                                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm font-mono @error('username') border-red-300 @enderror" 
                                    placeholder="8 dígitos">
                                @error('username') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Rol --}}
                            <div>
                                <label for="role" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Rol de Acceso</label>
                                <div class="relative">
                                    <select name="role" id="role" class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm appearance-none cursor-pointer">
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Usuario (Operador)</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador (Total)</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                                </div>
                                @error('role') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- SWITCH ESTADO DE LA CUENTA --}}
                            <div class="col-span-2 mt-2 pt-6 border-t border-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Estado de la Cuenta</label>
                                        <p class="text-xs text-slate-500 mt-1">Controla si este usuario puede iniciar sesión.</p>
                                    </div>
                                    <label class="inline-flex items-center cursor-pointer group">
                                        {{-- Input oculto para enviar '0' si el checkbox no está marcado (Truco de Laravel) --}}
                                        <input type="hidden" name="is_active" value="0"> 
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        
                                        <div class="relative w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">
                                            {{ old('is_active', $user->is_active) ? 'Activo' : 'Bloqueado' }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: SEGURIDAD (4 cols) --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/40 border border-slate-100 p-8 h-full flex flex-col">
                        
                        <div class="flex items-center gap-3 mb-6 border-b border-slate-50 pb-4">
                            <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Seguridad</h3>
                                <p class="text-xs text-slate-500">Actualizar credenciales.</p>
                            </div>
                        </div>

                        {{-- Aviso Importante --}}
                        <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100 mb-6 flex gap-3 items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xs text-blue-700 leading-tight font-medium">Si dejas estos campos vacíos, se mantendrá la contraseña actual.</p>
                        </div>

                        <div class="space-y-6 flex-1">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nueva Contraseña</label>
                                {{-- autocomplete="new-password" evita que el navegador la rellene solo --}}
                                <input type="password" name="password" id="password" autocomplete="new-password"
                                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all sm:text-sm @error('password') border-red-300 bg-red-50 @enderror"
                                    placeholder="Opcional">
                                @error('password') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Confirmar Nueva</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all sm:text-sm"
                                    placeholder="Solo si cambias">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER CON BOTONES --}}
            <div class="mt-8 flex items-center justify-end gap-4 p-6 bg-slate-100 rounded-2xl border border-slate-200">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-white transition-all">
                    Cancelar
                </a>
                <button type="submit" class="group relative inline-flex items-center gap-2 px-8 py-3 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all transform active:scale-95">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Actualizar Usuario
                </button>
            </div>

        </form>
    </div>
@endsection