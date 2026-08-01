<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Ajouter une catégorie</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6">
        <form method="POST" action="{{ route('admin.categories-galerie.store') }}">
            @csrf
            @include('admin.gallery-categories._form', ['category' => null])
        </form>
    </div>
</x-app-layout>
