@php
    $unread = $threads->filter(fn ($t) => $t->isFromManager() && ! $t->read_at)->count()
            + $threads->sum(fn ($t) => $t->replies->where('sender', 'manager')->whereNull('read_at')->count());
@endphp

<x-layouts.tenant-portal title="Messagerie" :unread-messages="$unread">
<div class="animate-fade-in max-w-6xl mx-auto" x-data="{ compose: false }">

    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Messagerie</h3>
            <p class="text-sm text-gray-500 mt-1">
                {{ $unread > 0
                    ? $unread.' message'.($unread > 1 ? 's' : '').' non lu'.($unread > 1 ? 's' : '')
                    : 'Tous les messages sont lus' }}
            </p>
        </div>
        <button type="button" @click="compose = true"
                class="flex items-center gap-2 bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-all hover:shadow-lg hover:shadow-pl-500/25">
            <x-tenant-portal.icon name="plus" class="w-4 h-4" />
            Nouveau message
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex bg-white rounded-2xl border border-gray-100 overflow-hidden h-[calc(100vh-220px)] min-h-[500px]">

        {{-- ===== Liste des fils ===== --}}
        <div class="w-full sm:w-80 border-r border-gray-100 flex flex-col {{ $thread ? 'hidden sm:flex' : 'flex' }}">
            <div class="p-4 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Boîte de réception</p>
            </div>
            <div class="flex-1 overflow-y-auto scrollbar-thin">
                @forelse($threads as $t)
                    @php
                        $isUnread = $t->isFromManager() && ! $t->read_at;
                        $selected = $thread && $thread->id === $t->id;
                    @endphp
                    <a href="{{ route('tenant-portal.messages.show', $t) }}"
                       class="block w-full text-left p-4 border-b border-gray-50 hover:bg-gray-50/50 transition relative {{ $selected ? 'bg-pl-50/50' : '' }}">
                        @if($isUnread)
                            <span class="absolute top-1/2 -translate-y-1/2 left-1.5 w-1.5 h-1.5 rounded-full bg-pl-600"></span>
                        @endif
                        <div class="{{ $isUnread ? 'pl-3' : '' }}">
                            <div class="flex items-center gap-2">
                                <x-tenant-portal.icon :name="$isUnread ? 'mail' : 'mail-open'"
                                    class="w-3.5 h-3.5 shrink-0 {{ $isUnread ? 'text-pl-600' : 'text-gray-400' }}" />
                                <p class="text-sm flex-1 truncate {{ $isUnread ? 'font-semibold text-gray-900' : 'font-medium text-gray-600' }}">
                                    {{ $t->isFromManager() ? 'MEEXEO IMMOBILIER' : 'Moi' }}
                                </p>
                                <span class="text-[10px] text-gray-400 shrink-0">{{ $t->created_at->diffForHumans(short: true) }}</span>
                            </div>
                            <p class="text-sm mt-1 truncate {{ $isUnread ? 'font-medium text-gray-700' : 'text-gray-500' }}">
                                {{ $t->subject }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $t->body }}</p>
                        </div>
                    </a>
                @empty
                    <div class="py-16 text-center">
                        <x-tenant-portal.icon name="message-square" class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                        <p class="text-sm text-gray-400">Aucun message</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ===== Fil sélectionné ===== --}}
        @if($thread)
            <div class="flex-1 flex flex-col min-w-0">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pl-500 to-pl-700 flex items-center justify-center shrink-0">
                            <x-tenant-portal.icon name="building" class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900">
                                {{ $thread->isFromManager() ? 'MEEXEO IMMOBILIER' : $tenant->fullName }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $thread->created_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
                        </div>
                        <a href="{{ route('tenant-portal.messages') }}" class="sm:hidden text-gray-400">
                            <x-tenant-portal.icon name="close" class="w-5 h-5" />
                        </a>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mt-4">{{ $thread->subject }}</h3>
                </div>

                <div class="flex-1 overflow-y-auto scrollbar-thin p-6">
                    @foreach(explode("\n", $thread->body) as $line)
                        <p class="text-sm text-gray-600 leading-relaxed mb-3">{{ $line }}</p>
                    @endforeach

                    @foreach($thread->replies as $reply)
                        <div class="mt-6 pt-5 border-t border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-semibold text-gray-900">
                                    {{ $reply->isFromManager() ? 'MEEXEO IMMOBILIER' : 'Moi' }}
                                </span>
                                <span class="text-[11px] text-gray-400">{{ $reply->created_at->isoFormat('D MMM, HH:mm') }}</span>
                            </div>
                            @foreach(explode("\n", $reply->body) as $line)
                                <p class="text-sm text-gray-600 leading-relaxed mb-2">{{ $line }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- Répondre : la maquette n'a pas ce champ, mais une messagerie
                     sans réponse possible n'en est pas une. --}}
                <form method="POST" action="{{ route('tenant-portal.messages.store') }}"
                      class="p-4 border-t border-gray-100 flex items-end gap-2">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $thread->id }}">
                    <textarea name="body" rows="1" required placeholder="Votre réponse..."
                              class="flex-1 px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition"></textarea>
                    <button type="submit"
                            class="bg-pl-600 hover:bg-pl-700 text-white px-4 py-2.5 rounded-lg transition shrink-0">
                        <x-tenant-portal.icon name="send" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        @else
            <div class="hidden sm:flex flex-1 items-center justify-center">
                <div class="text-center">
                    <x-tenant-portal.icon name="message-square" class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                    <p class="text-sm text-gray-400">Sélectionnez un message pour le lire</p>
                </div>
            </div>
        @endif
    </div>

    {{-- ===== Nouveau message ===== --}}
    <div x-show="compose" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="compose = false">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm animate-fade-in" @click="compose = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg animate-scale-in">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Nouveau message</h3>
                <button type="button" @click="compose = false" class="text-gray-400 hover:text-gray-600 transition">
                    <x-tenant-portal.icon name="close" class="w-5 h-5" />
                </button>
            </div>
            <form method="POST" action="{{ route('tenant-portal.messages.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Destinataire</label>
                    <div class="px-3 py-2.5 rounded-lg bg-gray-50 border border-gray-200 text-sm text-gray-600">
                        MEEXEO IMMOBILIER — Service de gestion
                    </div>
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1.5">Sujet</label>
                    <input id="subject" type="text" name="subject" required placeholder="Objet de votre message"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition">
                </div>
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
                    <textarea id="body" name="body" rows="6" required placeholder="Votre message..."
                              class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="compose = false"
                            class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <x-tenant-portal.icon name="send" class="w-4 h-4" />
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.tenant-portal>
