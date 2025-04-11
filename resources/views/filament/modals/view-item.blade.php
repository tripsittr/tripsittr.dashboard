<?php /** @var \App\Models\Inventory $record */ ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
    <!-- Left: Product Image & Info -->
    <div class="flex flex-col items-center text-center">
        <img src="{{ $record->image ?? '/placeholder.jpg' }}" alt="{{ $record->name }}" class="w-48 h-48 object-cover rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mt-4">{{ $record->name }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $record->description ?? 'No description available.' }}</p>
    </div>

    <!-- Right: Product Details -->
    <div class="space-y-3">
        <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>SKU:</strong> {{ $record->sku ?? 'N/A' }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Stock:</strong> {{ $record->stock }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Price:</strong> ${{ number_format($record->price, 2) }}</p>
        </div>
        
        <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Weight:</strong> {{ $record->weight ?? 'N/A' }} {{ $record->weight_unit }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Dimensions:</strong> {{ $record->dimensions ?? 'N/A' }} {{ $record->dims_unit }}</p>
        </div>

        <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Color:</strong> {{ $record->color ?? 'N/A' }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Material:</strong> {{ $record->material ?? 'N/A' }}</p>
        </div>

        <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Owner:</strong> {{ $record->user->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-200"><strong>Band:</strong> {{ $record->band->name ?? 'N/A' }}</p>
        </div>
    </div>
</div>
