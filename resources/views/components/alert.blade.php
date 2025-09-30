@props(['type' => 'info'])

@php
$classes = [
  'success' => 'bg-green-100 text-green-800 border-green-300',
  'error' => 'bg-red-100 text-red-800 border-red-300',
  'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
  'info' => 'bg-blue-100 text-blue-800 border-blue-300',
];

$icons = [
  'success' => '✓',
  'error' => '✕',
  'warning' => '⚠',
  'info' => 'ℹ',
];
@endphp

<div {{ $attributes->merge(['class' => 'border-l-4 p-4 rounded-md ' . ($classes[$type] ?? $classes['info'])]) }}>
  <div class="flex">
    <div class="flex-shrink-0">
      <span class="font-bold">{{ $icons[$type] ?? $icons['info'] }}</span>
    </div>
    <div class="ml-3">
      <p class="text-sm font-medium">
        {{ $slot }}
      </p>
    </div>
  </div>
</div>
