<div class="relative w-full h-full overflow-hidden">

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Traits
        </h2>
    </x-slot>

    <div class="relative flex flex-col w-full h-full">
        <div class="flex justify-center w-full">
            <div class="p-1.5 mx-4 my-2 bg-gray-800 border rounded h-full" style="width: 70%;">
                <div class="flex justify-end">
                    <button class="p-1 bg-blue-600 border border-blue-500 rounded" wire:click="$dispatch('openModal', {component: 'modal.manage.create-trait'})">Create Trait</button>
                </div>
                <div class="flex flex-col justify-between w-full p-2 mb-2 space-y-2 border-b rounded">
                    <div>
                        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Traits</h2>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            @foreach ($traits as $trait)
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                            {{ $trait->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                            {{ $trait->type }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                            <button class="p-1 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="deleteTrait({{ $trait->id }})">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
