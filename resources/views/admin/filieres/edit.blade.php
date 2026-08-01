<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Modifier la filière</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6">
        <form method="POST" action="{{ route('admin.filieres.update', $filiere) }}">
            @csrf
            @method('PUT')
            @include('admin.filieres._form')
        </form>
    </div>
</x-app-layout>
