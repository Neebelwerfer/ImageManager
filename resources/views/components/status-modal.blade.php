<div x-data="{ showModal: true }">
    <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <!-- Modal Content -->
        <div class="w-1/2 max-w-3xl p-8 bg-gray-700 rounded-lg shadow-lg" x-on:click.away="showModal = false">
            <h2 class="mb-4 text-2xl font-bold border-b">{{ $header }}</h2>

            <div class="my-2 mb-6 space-y-2 overflow-auto">
                {{ $slot }}
            </div>

            <!-- Close Button -->
            <button x-on:click="showModal = false" class="px-4 py-2 text-white bg-red-500 rounded-md">
                Close
            </button>
        </div>
    </div>
</div>
