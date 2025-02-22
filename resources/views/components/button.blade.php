<button {{ $attributes->merge(['type' => 'button', 'class' => 'p-1 mt-4 bg-gray-700 border rounded dark:bg-slate-700 hover:bg-gray-400 hover:dark:bg-gray-500']) }}>
    {{ $slot }}
</button>
