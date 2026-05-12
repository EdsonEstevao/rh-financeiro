@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Novo cargo</h1>
                <p class="mt-1 text-sm text-gray-500">Cadastre um novo cargo.</p>
            </div>
        </div>

        <div class="mt-6 rounded-lg bg-white p-6 shadow ring-1 ring-black/5">
            <form method="POST" action="{{ route('rh.cargos.store') }}">
                @csrf
                @include('rh.cargos._form')
            </form>
        </div>
    </div>
@endsection
