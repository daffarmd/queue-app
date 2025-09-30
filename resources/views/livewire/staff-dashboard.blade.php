<div class="space-y-6">
  <!-- Page Header -->
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Staff Dashboard</h2>
        <p class="text-gray-600">Manage patient queues and service operations</p>
      </div>
      <div class="text-right">
        <div class="text-sm text-gray-500">Counter</div>
        <input wire:model="counter" type="text"
               class="mt-1 block w-20 rounded-md border-gray-300 shadow-sm focus:border-[#D32F2F] focus:ring-[#D32F2F]"
               placeholder="1">
      </div>
    </div>
  </div>

  <!-- Flash Messages -->
  @if (session()->has('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
  @endif
  @if (session()->has('error'))
    <x-alert type="error">{{ session('error') }}</x-alert>
  @endif
  @if (session()->has('warning'))
    <x-alert type="warning">{{ session('warning') }}</x-alert>
  @endif
  @if (session()->has('info'))
    <x-alert type="info">{{ session('info') }}</x-alert>
  @endif

  <!-- Queue Creation Form -->
  <div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New Queue</h3>

    <form wire:submit="createQueue" class="space-y-4">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="service" class="block text-sm font-medium text-gray-700 mb-2">
            Service
          </label>
          <select wire:model="selectedService" id="service"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#D32F2F] focus:ring-[#D32F2F]">
            <option value="">Select a service...</option>
            @foreach($services as $service)
              <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->code }})</option>
            @endforeach
          </select>
          @error('selectedService') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
          <label for="patientName" class="block text-sm font-medium text-gray-700 mb-2">
            Patient Name
          </label>
          <input wire:model="patientName" type="text" id="patientName"
                 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#D32F2F] focus:ring-[#D32F2F]"
                 placeholder="Enter patient name">
          @error('patientName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="flex justify-end">
        <button type="submit"
                class="bg-[#D32F2F] text-white px-6 py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-[#D32F2F] focus:ring-offset-2">
          Create Queue
        </button>
      </div>
    </form>
  </div>

  <!-- Queue Lists -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Waiting Queues -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Waiting ({{ isset($queues['waiting']) ? $queues['waiting']->count() : 0 }})
        </h3>
      </div>
      <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
        @if(isset($queues['waiting']) && $queues['waiting']->count() > 0)
          @foreach($queues['waiting'] as $queue)
            <x-queue-card :queue="$queue" :showActions="true" />
          @endforeach
        @else
          <p class="text-gray-500 text-center py-4">No waiting queues</p>
        @endif
      </div>
    </div>

    <!-- Called Queues -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Called ({{ isset($queues['called']) ? $queues['called']->count() : 0 }})
        </h3>
      </div>
      <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
        @if(isset($queues['called']) && $queues['called']->count() > 0)
          @foreach($queues['called'] as $queue)
            <x-queue-card :queue="$queue" :showActions="true" />
          @endforeach
        @else
          <p class="text-gray-500 text-center py-4">No called queues</p>
        @endif
      </div>
    </div>

    <!-- Skipped Queues -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Skipped ({{ isset($queues['skipped']) ? $queues['skipped']->count() : 0 }})
        </h3>
      </div>
      <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
        @if(isset($queues['skipped']) && $queues['skipped']->count() > 0)
          @foreach($queues['skipped'] as $queue)
            <x-queue-card :queue="$queue" :showActions="true" />
          @endforeach
        @else
          <p class="text-gray-500 text-center py-4">No skipped queues</p>
        @endif
      </div>
    </div>

  </div>
</div>
