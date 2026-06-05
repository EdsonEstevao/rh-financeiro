{{-- resources/views/errors/illustrations.blade.php --}}
{{-- Componente de ilustração SVG para cada tipo de erro --}}

@switch($code)
    @case(403)
        {{-- Ilustração de cadeado --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Corrente --}}
                <path d="M100 20 C70 20 40 35 40 60 L40 80" stroke="#ef4444" stroke-width="8" stroke-linecap="round"
                    fill="none" />
                <path d="M100 20 C130 20 160 35 160 60 L160 80" stroke="#ef4444" stroke-width="8" stroke-linecap="round"
                    fill="none" />
                {{-- Cadeado --}}
                <rect x="30" y="80" width="140" height="100" rx="15" fill="#fee2e2" stroke="#ef4444"
                    stroke-width="8" />
                <circle cx="100" cy="125" r="15" fill="#ef4444" />
                <rect x="95" y="130" width="10" height="25" rx="3" fill="#ef4444" />
                {{-- Chave caída --}}
                <circle cx="155" cy="170" r="12" fill="#fca5a5" stroke="#ef4444" stroke-width="4" />
                <rect x="162" y="160" width="25" height="8" rx="2" fill="#fca5a5" stroke="#ef4444"
                    stroke-width="3" />
            </svg>
        </div>
    @break

    @case(404)
        {{-- Ilustração de lupa/página perdida --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Lupa --}}
                <circle cx="85" cy="85" r="55" fill="#fef3c7" stroke="#f59e0b" stroke-width="8" />
                <line x1="125" y1="125" x2="170" y2="170" stroke="#f59e0b" stroke-width="12"
                    stroke-linecap="round" />
                {{-- Ponto de interrogação dentro da lupa --}}
                <text x="85" y="100" text-anchor="middle" font-size="50" font-weight="bold" fill="#f59e0b">?</text>
                {{-- Folhas voando --}}
                <rect x="145" y="20" width="20" height="25" rx="3" fill="#fef3c7" stroke="#f59e0b"
                    stroke-width="3" transform="rotate(20 155 32)" />
                <rect x="160" y="50" width="15" height="20" rx="2" fill="#fef3c7" stroke="#f59e0b"
                    stroke-width="3" transform="rotate(-15 167 60)" />
            </svg>
        </div>
    @break

    @case(419)
        {{-- Ilustração de relógio --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="#dbeafe" stroke="#3b82f6" stroke-width="8" />
                <circle cx="100" cy="100" r="5" fill="#3b82f6" />
                <line x1="100" y1="100" x2="100" y2="45" stroke="#3b82f6" stroke-width="8"
                    stroke-linecap="round" />
                <line x1="100" y1="100" x2="135" y2="115" stroke="#3b82f6" stroke-width="8"
                    stroke-linecap="round" />
                {{-- Areia caindo --}}
                <circle cx="100" cy="175" r="3" fill="#93c5fd" />
                <circle cx="115" cy="180" r="2" fill="#93c5fd" />
                <circle cx="85" cy="178" r="2.5" fill="#93c5fd" />
            </svg>
        </div>
    @break

    @case(500)
        {{-- Ilustração de engrenagem quebrada --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="70" fill="#fce7f3" stroke="#ec4899" stroke-width="8" />
                <circle cx="100" cy="100" r="25" fill="#fce7f3" stroke="#ec4899" stroke-width="6" />
                {{-- Dentes da engrenagem --}}
                @for ($i = 0; $i < 8; $i++)
                    <rect x="92" y="22" width="16" height="20" rx="4" fill="#fce7f3" stroke="#ec4899"
                        stroke-width="4" transform="rotate({{ $i * 45 }} 100 100)" />
                @endfor
                {{-- Símbolo de alerta --}}
                <text x="100" y="112" text-anchor="middle" font-size="35" font-weight="bold" fill="#ec4899">!</text>
            </svg>
        </div>
    @break

    @case(503)
        {{-- Ilustração de ferramentas --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Chave inglesa --}}
                <path d="M40 160 L80 120 L95 135 L55 175 Z" fill="#e0e7ff" stroke="#6366f1" stroke-width="6"
                    stroke-linejoin="round" />
                <circle cx="45" cy="155" r="20" fill="#e0e7ff" stroke="#6366f1" stroke-width="6" />
                <circle cx="45" cy="155" r="8" fill="#6366f1" />
                {{-- Martelo --}}
                <rect x="120" y="50" width="50" height="25" rx="5" fill="#e0e7ff" stroke="#6366f1"
                    stroke-width="6" transform="rotate(-30 145 62)" />
                <rect x="130" y="75" width="12" height="70" rx="4" fill="#c7d2fe" stroke="#6366f1"
                    stroke-width="5" transform="rotate(-30 136 110)" />
            </svg>
        </div>
    @break

@endswitch
