<x-mail::message>
# Nouveau message reçu via le site CSNDA

**Nom :** {{ $contactMessage->name }}
**Email :** {{ $contactMessage->email }}

**Message :**

{{ $contactMessage->message }}

<x-mail::button :url="config('app.url').'/admin/messages'">
Voir dans le back-office
</x-mail::button>

Cours Secondaire Notre-Dame des Apôtres
</x-mail::message>
