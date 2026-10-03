<x-layouts.tenant-portal title="Signaler un problème">

    <div class="mb-6">
        <a href="{{ route('tenant-portal.repairs') }}" class="text-ardoise text-sm flex items-center gap-1 mb-3">← Retour</a>
        <h1 class="text-2xl font-semibold text-lagune">Signaler un problème</h1>
        <p class="text-sm text-ardoise mt-1">{{ $lease->property->title }}</p>
    </div>

    <form method="POST" action="{{ route('tenant-portal.repairs.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-sm font-medium text-lagune mb-2">Type de problème</label>
                <select name="type" class="w-full px-4 py-3 rounded-xl border border-gray-200 @error('type') border-red-400 @enderror">
                    <option value="">Choisir...</option>
                    @foreach(['plomberie' => 'Plomberie', 'electricite' => 'Électricité', 'serrure' => 'Serrure', 'peinture' => 'Peinture', 'climatisation' => 'Climatisation', 'autre' => 'Autre'] as $val => $label)
                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-sm font-medium text-lagune mb-2">Description</label>
                <textarea name="description" rows="4" placeholder="Décrivez le problème..."
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-sm font-medium text-lagune mb-2">Urgence</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['faible' => 'Faible', 'moyenne' => 'Moyenne', 'urgente' => 'Urgente'] as $val => $label)
                        <label class="relative">
                            <input type="radio" name="urgency" value="{{ $val }}" {{ old('urgency', 'moyenne') === $val ? 'checked' : '' }} class="sr-only peer">
                            <div class="cursor-pointer text-center py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-ardoise peer-checked:border-lagune peer-checked:bg-lagune/5 peer-checked:text-lagune transition-colors">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('urgency')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-sm font-medium text-lagune mb-1">Photos (optionnel)</label>
                <p class="text-xs text-ardoise mb-3">Max 5 photos · 5 Mo chacune</p>
                <input type="file" name="photos[]" multiple accept="image/*" class="w-full text-sm text-ardoise">
                @error('photos.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="w-full bg-lagune text-white py-4 rounded-2xl font-medium text-base hover:bg-opacity-90 transition-colors">
                Envoyer le signalement
            </button>
        </div>
    </form>

</x-layouts.tenant-portal>
