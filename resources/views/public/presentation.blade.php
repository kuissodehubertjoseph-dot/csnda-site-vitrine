<x-layouts.public :title="'Présentation'">

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-encre uppercase tracking-wide text-center mb-10">
            {{ $presentation->title }}
        </h1>

        @if ($presentation->image)
            <img src="{{ asset('storage/'.$presentation->image) }}" alt="{{ $presentation->title }}" class="w-full h-64 object-cover rounded-2xl mb-8">
        @endif

        <div class="prose max-w-none text-encre/90 leading-relaxed">
            {!! nl2br(e($presentation->content ?? "Le contenu de présentation de l'école sera bientôt disponible.")) !!}
        </div>
    </section>

    <section class="bg-ciel/5 py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl sm:text-2xl font-extrabold text-laurier uppercase tracking-wide text-center mb-8">
                {{ $historique->title }}
            </h2>
            <div class="prose max-w-none text-encre/90 leading-relaxed bg-white rounded-2xl p-6 sm:p-8 shadow">
                {!! nl2br(e($historique->content ?? "L'historique de l'école sera bientôt disponible.")) !!}
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h2 class="text-xl sm:text-2xl font-extrabold text-saumon uppercase tracking-wide text-center mb-8">
            {{ $motDirecteur->title }}
        </h2>
        <div class="bg-white rounded-2xl shadow p-6 sm:p-8 flex flex-col sm:flex-row gap-6 items-start border-2 border-saumon/30">
            @if ($motDirecteur->image)
                <img src="{{ asset('storage/'.$motDirecteur->image) }}" alt="Directeur" class="h-40 w-40 rounded-full object-cover mx-auto sm:mx-0 shrink-0">
            @else
                <div class="h-40 w-40 rounded-full bg-saumon/20 flex items-center justify-center text-saumon font-bold mx-auto sm:mx-0 shrink-0">Photo</div>
            @endif
            <div class="prose max-w-none text-encre/90 leading-relaxed">
                {!! nl2br(e($motDirecteur->content ?? "Le mot du directeur sera bientôt disponible.")) !!}
            </div>
        </div>
    </section>

</x-layouts.public>
