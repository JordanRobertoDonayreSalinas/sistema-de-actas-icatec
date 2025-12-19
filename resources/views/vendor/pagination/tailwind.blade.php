@if ($paginator->hasPages())
    <nav role="navigation" class="flex justify-between items-center mt-4">
        {{-- Botón Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-500">Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Anterior</a>
        @endif

        {{-- Links --}}
        <div class="flex gap-2">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 text-gray-500">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-2 rounded-lg bg-indigo-500 text-white font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-indigo-100 transition">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Botón Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Siguiente</a>
        @else
            <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-500">Siguiente</span>
        @endif
    </nav>
@endif
