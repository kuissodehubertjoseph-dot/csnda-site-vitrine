<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Modifier l'actualité</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6">
        <form method="POST" action="{{ route('admin.actualites.update', $news) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.news._form')
        </form>
    </div>
</x-app-layout>
