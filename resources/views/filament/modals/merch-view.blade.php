<div class="p-6 space-y-6">
    <button @click="$dispatch('close-modal')" class="absolute top-4 right-4 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">
        ✖
    </button>
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Image -->
        <div class="flex-shrink-0">
            <img src="{{ asset('storage/' . $record->image) }}" class="w-40 h-40 object-cover rounded-lg shadow-md">
        </div>

        <!-- Product Info -->
        <div class="flex-grow space-y-2">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $record->name }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $record->description ?? 'No description available.' }}</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">${{ number_format($record->price, 2) }}</p>
            
            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 dark:text-gray-400">
                <p><strong>Stock:</strong> {{ $record->stock }}</p>
                <p><strong>Type:</strong> {{ ucfirst($record->type) }}</p>
                @if ($record->size)
                    <p><strong>Size:</strong> {{ $record->size }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Details Section -->
    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Product Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-800 dark:text-gray-200 mt-2">
            <p><strong>SKU:</strong> {{ $record->sku ?? 'N/A' }}</p>
            <p><strong>Weight:</strong> {{ $record->weight ?? 'N/A' }}</p>
            <p><strong>Dimensions:</strong> {{ $record->dimensions ?? 'N/A' }}</p>
            <p><strong>Material:</strong> {{ $record->material ?? 'N/A' }}</p>
            <p><strong>Low Stock Alert:</strong> {{ $record->low_stock_threshold ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Colors Available -->
    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Colors Available</h3>
        <div class="flex flex-wrap gap-2 mt-2">
            @forelse ($record->colors ?? [] as $color)
                <span class="px-3 py-1 text-xs font-medium text-white bg-blue-500 dark:bg-blue-700 rounded-full">
                    {{ $color }}
                </span>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No colors specified.</p>
            @endforelse
        </div>
    </div>

    <!-- Ownership Section -->
    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Owned By</h3>
        <p class="text-sm text-gray-800 dark:text-gray-200">
            <strong>Band:</strong> {{ $record->band->name ?? 'N/A' }}<br>
            <strong>Artist:</strong> {{ $record->user->name ?? 'N/A' }}
        </p>
    </div>
</div>
