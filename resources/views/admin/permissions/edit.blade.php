{{-- resources/views/admin/activity-logs/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">📝 Logs de Atividade</h1>
            <p class="mt-1 text-sm text-gray-500">Registro de ações no sistema</p>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select name="user_id" class="rounded-md border-gray-300 text-sm">
                    <option value="">Todos os usuários</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
                <select name="event" class="rounded-md border-gray-300 text-sm">
                    <option value="">Todos os eventos</option>
                    @foreach ($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                            {{ ucfirst($event) }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="rounded-md border-gray-300 text-sm" placeholder="Data inicial">
                <div class="flex gap-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="flex-1 rounded-md border-gray-300 text-sm" placeholder="Data final">
                    <button type="submit" class="px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200 text-sm">🔍</button>
                </div>
            </form>
        </div>

        {{-- Tabela --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Data/Hora</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Usuário</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Evento</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Descrição</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Model</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $log->causer?->name ?? 'Sistema' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full
                                @if ($log->event === 'created') bg-green-100 text-green-700
                                @elseif($log->event === 'updated') bg-blue-100 text-blue-700
                                @elseif($log->event === 'deleted') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700 @endif">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $log->description }}</td>
                            <td class="px-4 py-3 text-xs text-gray-
