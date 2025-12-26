@extends('layouts.admin')

@section('title', 'Crear Nuevo Usuario')

@section('header-content')
    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Nuevo Usuario</h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
        <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 transition-colors">Gestionar usuarios</a>
        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span>Crear usuario</span>
    </div>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- COLUMNA DATOS DEL PERFIL --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                        <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center gap-2">
                            <i data-lucide="user-circle" class="w-5 h-5 text-indigo-500"></i>
                            Datos del Perfil
                        </h3>
                        <p class="text-xs text-slate-500 mb-6 pl-7">Información básica del nuevo integrante.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Apellido Paterno --}}
                            <div>
                                <label for="apellido_paterno" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Apellido Paterno</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="user" class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('apellido_paterno') border-red-300 @enderror" placeholder="Ej: Pérez">
                                </div>
                                @error('apellido_paterno') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            {{-- Apellido Materno --}}
                            <div>
                                <label for="apellido_materno" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Apellido Materno</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="user" class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('apellido_materno') border-red-300 @enderror" placeholder="Ej: García">
                                </div>
                                @error('apellido_materno') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            {{-- Nombres --}}
                            <div class="col-span-2">
                                <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nombres</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="info" class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('name') border-red-300 @enderror" placeholder="Ej: Juan Alberto">
                                </div>
                                @error('name') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-span-2">
                                <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Correo Electrónico</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="mail" class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm @error('email') border-red-300 @enderror" placeholder="ejemplo@empresa.com">
                                </div>
                                @error('email') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            {{-- Usuario (DNI) --}}
                            <div>
                                <label for="username" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Usuario</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="fingerprint" class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="number" name="username" id="username" value="{{ old('username') }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm font-mono @error('username') border-red-300 @enderror" placeholder="8 dígitos">
                                </div>
                                @error('username') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            {{-- Rol --}}
                            <div>
                                <label for="role" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Rol de Acceso</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="shield-check" class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <select name="role" id="role" class="block w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm appearance-none cursor-pointer">
                                        <option value="" disabled selected>Seleccionar...</option>
                                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Usuario</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @error('role') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            {{-- Estado del Usuario (NUEVO REINTEGRADO) --}}
                            <div class="col-span-2">
                                <label for="status" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Estado Inicial</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="activity" class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <select name="status" id="status" class="block w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all sm:text-sm appearance-none cursor-pointer">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>ACTIVO</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>INACTIVO</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                </div>
                                @error('status') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- COLUMNA CREDENCIALES --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 h-full flex flex-col">
                        <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center gap-2">
                            <i data-lucide="key" class="w-5 h-5 text-emerald-500"></i>
                            Credenciales
                        </h3>
                        <p class="text-xs text-slate-500 mb-6 pl-7">Establece la contraseña de acceso.</p>

                        <div class="space-y-5 flex-1">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Contraseña</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i data-lucide="lock" class="h-4 w-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i></div>
                                    <input type="password" name="password" id="password" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all sm:text-sm @error('password') border-red-300 @enderror" placeholder="••••••••">
                                </div>
                                @error('password') <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Confirmar</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i data-lucide="check-circle" class="h-4 w-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i></div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all sm:text-sm" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100/50 mt-auto">
                                <div class="flex gap-3">
                                    <i data-lucide="shield-alert" class="w-5 h-5 text-blue-500 shrink-0"></i>
                                    <p class="text-[11px] text-blue-700 leading-relaxed font-medium">Por seguridad, la contraseña debe tener al menos 6 caracteres.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ACCIONES --}}
            <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 pt-8">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-all">Cancelar</a>
                <button type="submit" class="group relative inline-flex items-center gap-2 px-8 py-3 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all transform active:scale-95">
                    <i data-lucide="save" class="w-4 h-4 transition-transform group-hover:-translate-y-0.5"></i>
                    Guardar Usuario
                </button>
            </div>

        </form>
    </div>
@endsection