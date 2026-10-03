<x-layouts.tenant-portal-auth title="Aucun bail actif">

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-10 text-center">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
            <x-tenant-portal.icon name="home" class="w-7 h-7" />
        </div>

        <h2 class="text-xl font-bold text-gray-900 mt-5">Aucun bail actif</h2>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
            Votre compte existe bien, mais aucun contrat de location n'y est rattaché pour le moment.
            Votre gestionnaire doit activer votre bail pour que vous accédiez à votre espace.
        </p>

        <form method="POST" action="{{ route('tenant-portal.logout') }}" class="mt-7">
            @csrf
            <button type="submit"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2">
                <x-tenant-portal.icon name="log-out" class="w-4 h-4" />
                Se déconnecter
            </button>
        </form>
    </div>

</x-layouts.tenant-portal-auth>
