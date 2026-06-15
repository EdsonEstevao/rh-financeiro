{{-- resources/views/components/month-input.blade.php --}}
@props(['name' => 'competencia', 'value' => null])

@php
    $value = $value ?? now()->format('Y-m');
    [$year, $month] = explode('-', $value);
@endphp

<div x-data="{
    month: '{{ $month }}',
    year: '{{ $year }}',

    get valorCompleto() {
        return this.year + '-' + this.month;
    },

    atualizar() {
        $dispatch('input', this.valorCompleto);
    }
}" class="flex gap-2">
    <select x-model="month" @change="atualizar()" name="{{ $name }}_mes"
        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        @foreach (['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'] as $num => $nome)
            <option value="{{ $num }}">{{ $nome }}</option>
        @endforeach
    </select>

    <select x-model="year" @change="atualizar()" name="{{ $name }}_ano"
        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        @foreach (range(now()->year - 5, now()->year + 2) as $y)
            <option value="{{ $y }}">{{ $y }}</option>
        @endforeach
    </select>

    <input type="hidden" name="{{ $name }}" :value="valorCompleto">
</div>
<p class="mt-1 text-xs text-gray-400">Selecione o mês e ano da competência</p>
