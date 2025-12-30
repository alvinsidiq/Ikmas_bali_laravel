@props([
    'src' => null,
    'alt' => '',
    'class' => '',
    'fallback' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80',
])
@php($resolved = \App\Support\MediaPath::url($src) ?? $fallback)
@if($resolved)
    <img src="{{ $resolved }}"
         alt="{{ $alt }}"
         loading="lazy"
         decoding="async"
         referrerpolicy="no-referrer"
         onerror="this.style.display='none';"
         {{ $attributes->merge(['class' => $class]) }} />
@endif
