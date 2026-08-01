<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Ajouter une filière</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6">
        <form method="POST" action="{{ route('admin.filieres.store') }}">
            @csrf
            @include('admin.filieres._form', ['filiere' => null])
        </form>
    </div>
</x-app-layout>
