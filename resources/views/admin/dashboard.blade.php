<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Tableau de bord</h1>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-white rounded-2xl p-6 shadow border-l-4 border-ciel">
            <p class="text-xs font-bold text-ciel uppercase mb-1">Actualités publiées</p>
            <p class="text-3xl font-extrabold text-encre">{{ $stats['news'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow border-l-4 border-laurier">
            <p class="text-xs font-bold text-laurier uppercase mb-1">Photos en galerie</p>
            <p class="text-3xl font-extrabold text-encre">{{ $stats['photos'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow border-l-4 border-saumon">
            <p class="text-xs font-bold text-saumon uppercase mb-1">Messages non lus</p>
            <p class="text-3xl font-extrabold text-encre">{{ $stats['unreadMessages'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-encre uppercase text-sm tracking-wide">Derniers messages reçus</h2>
            <a href="{{ route('admin.messages.index') }}" class="text-sm text-ciel font-semibold hover:underline">Voir tout &rarr;</a>
        </div>

        @if ($latestMessages->isEmpty())
            <p class="text-sm text-encre/60">Aucun message reçu pour le moment.</p>
        @else
            <ul class="divide-y divide-ciel/10">
                @foreach ($latestMessages as $message)
                    <li class="py-3 flex items-center justify-between">
                        <a href="{{ route('admin.messages.show', $message) }}" class="flex-1">
                            <p class="font-semibold text-encre text-sm">{{ $message->name }} <span class="text-encre/50 font-normal">— {{ $message->email }}</span></p>
                            <p class="text-encre/60 text-sm line-clamp-1">{{ $message->message }}</p>
                        </a>
                        @unless ($message->read)
                            <span class="ml-3 inline-block bg-saumon text-white text-xs font-bold px-2 py-0.5 rounded-full">Nouveau</span>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
