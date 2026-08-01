<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Message de {{ $contactMessage->name }}</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6 max-w-2xl space-y-4">
        <div>
            <p class="text-xs font-bold text-ciel uppercase">Nom</p>
            <p class="text-encre">{{ $contactMessage->name }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-ciel uppercase">Email</p>
            <p class="text-encre">{{ $contactMessage->email }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-ciel uppercase">Reçu le</p>
            <p class="text-encre">{{ $contactMessage->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-ciel uppercase mb-1">Message</p>
            <p class="text-encre whitespace-pre-line">{{ $contactMessage->message }}</p>
        </div>

        <div class="flex gap-3 pt-4">
            <a href="mailto:{{ $contactMessage->email }}" class="bg-laurier text-white text-sm font-bold uppercase px-5 py-2.5 rounded-full hover:bg-laurier/90">
                Répondre par email
            </a>
            <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-encre/70 hover:text-encre">
                Retour à la liste
            </a>
        </div>
    </div>
</x-app-layout>
