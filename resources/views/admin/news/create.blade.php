<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Nouvelle actualité</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6">
        <form method="POST" action="{{ route('admin.actualites.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.news._form', ['news' => null])
        </form>
    </div>
</x-app-layout>
