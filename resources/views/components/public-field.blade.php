@props(['name', 'label', 'help' => null])
<div class="min-w-0">
    <label for="{{ $name }}" class="block font-semibold">{{ $label }}</label>
    {{ $slot }}
    @if($help)<p id="{{ $name }}-help" class="mt-2 text-sm leading-6 text-brand-muted">{{ $help }}</p>@endif
    @error($name)<p id="{{ $name }}-error" class="mt-2 text-sm font-semibold text-red-900">{{ $message }}</p>@enderror
</div>
