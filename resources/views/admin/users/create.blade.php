@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Nuevo Usuario</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 transition-colors">Usuarios</a>
        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span>Crear Registro</span>
    </div>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- COLUMNA DATOS --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                        <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Datos del Perfil
                        </h3>
                        <p class="text-xs text-slate-500 mb-6 pl-7">Información básica del nuevo integrante.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nombre --}}
                            <div class="col-span-2">
                                <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nombre Completo</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('name') border-red-300 @enderror" placeholder="Ej: Juan Pérez">
                                </div>
                                @error('name') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-span-2">
                                <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Correo Electrónico</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v9a2 2 0 002 2z"></path></svg></div>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('email') border-red-300 @enderror" placeholder="ejemplo@empresa.com">
                                </div>
                                @error('email') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- DNI --}}
                            <div>
                                <label for="username" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Usuario (DNI)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 2-2 2h4c-1.5 0-2-1.116-2-2z"></path></svg></div>
                                    <input type="number" name="username" id="username" value="{{ old('username') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm font-mono @error('username') border-red-300 @enderror" placeholder="8 dígitos">
                                </div>
                                @error('username') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Rol --}}
                            <div>
                                <label for="role" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Rol de Acceso</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                                    <select name="role" id="role" class="block w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm appearance-none cursor-pointer">
                                        <option value="" disabled selected>Seleccionar...</option>
                                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Usuario (Operador)</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador (Total)</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                                </div>
                                @error('role') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- COLUMNA SEGURIDAD --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 h-full flex flex-col">
                        <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Credenciales
                        </h3>
                        <p class="text-xs text-slate-500 mb-6 pl-7">Establece la contraseña de acceso.</p>

                        <div class="space-y-5 flex-1">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Contraseña</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg></div>
                                    <input type="password" name="password" id="password" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all sm:text-sm @error('password') border-red-300 @enderror" placeholder="••••••••">
                                </div>
                                @error('password') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Confirmar</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all sm:text-sm" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-100 mt-4">
                                <div class="flex gap-2">
                                    <svg class="w-4 h-4 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-[11px] text-blue-700 leading-tight">Mínimo 6 caracteres.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all">Cancelar</a>
                <button type="submit" class="group relative inline-flex items-center gap-2 px-8 py-3 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all transform active:scale-95">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Guardar Usuario
                </button>
            </div>

        </form>
    </div>
@endsection