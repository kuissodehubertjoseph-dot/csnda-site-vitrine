<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Nouvel élève
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('eleves.store') }}" enctype="multipart/form-data">
                    @csrf

                    @include('eleves.partials.form')

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('eleves.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-brand-sky text-white text-sm rounded-md hover:bg-brand-sky-deep">
                            Enregistrer l'élève
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
