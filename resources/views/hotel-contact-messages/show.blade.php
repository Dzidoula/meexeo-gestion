<x-layouts.app title="Message — MASTERCLAYS">
    <x-page-header :title="$message->subject" />
    @include('hotel._subnav')
    <div class="mt-6 max-w-xl space-y-2 text-sm">
        <p><strong>De :</strong> {{ $message->first_name }} {{ $message->last_name }} ({{ $message->email }}@if($message->phone), {{ $message->phone }}@endif)</p>
        <p><strong>Reçu le :</strong> {{ $message->created_at->format('d/m/Y H:i') }}</p>
        <p class="mt-4" style="white-space:pre-line">{{ $message->message }}</p>
    </div>
</x-layouts.app>
