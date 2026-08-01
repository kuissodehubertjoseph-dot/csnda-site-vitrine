<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Messages reçus</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-ciel/10 text-encre uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Statut</th>
                    <th class="text-left px-4 py-3">Nom</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-left px-4 py-3">Reçu le</th>
                    <th class="text-right px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ciel/10">
                @forelse ($messages as $message)
                    <tr class="{{ $message->read ? '' : 'bg-saumon/5' }}">
                        <td class="px-4 py-3">
                            @if ($message->read)
                                <span class="text-xs text-encre/50">Lu</span>
                            @else
                                <span class="text-xs font-bold text-white bg-saumon px-2 py-0.5 rounded-full">Nouveau</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-encre">{{ $message->name }}</td>
                        <td class="px-4 py-3 text-encre/70">{{ $message->email }}</td>
                        <td class="px-4 py-3 text-encre/70">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.messages.show', $message) }}" class="text-ciel font-semibold hover:underline">Lire</a>
                            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce message ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-saumon font-semibold hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-encre/60">Aucun message reçu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
</x-app-layout>
