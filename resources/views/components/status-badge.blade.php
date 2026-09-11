{{-- resources/views/components/status-badge.blade.php --}}
@props(['status'])
@php($presented = \App\Support\StatusPresenter::for($status))
<span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11.5px] font-semibold {{ $presented['wrapper'] }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $presented['dot'] }}"></span>
    {{ $presented['label'] }}
</span>
