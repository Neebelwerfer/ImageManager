<x-modal>
    <x-slot name="title">
        Edit Album
    </x-slot>

    <x-slot name="content">
        <form wire:submit.prevent="save">
            <div class="flex flex-col">
                <label for="name" class="">Name</label>
                <input type="text" class="text-black form-control" wire:model="name" placeholder="Name..."/>
            </div>
        </form>


        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                        AccessLevel
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @foreach ($this->sharedWith as $sharedResource)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            {{ $sharedResource->shared_with->email }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            {{ $sharedResource->level }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            <button class="p-1 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="removeShared({{ $sharedResource->id }})">Remove</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex flex-row justify-between w-full gap-2">
            <span>
            <button class="p-1 mt-4 bg-gray-700 border rounded btn dark:bg-slate-500 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="save">Save</button>
            <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="$dispatch('closeModal')">Cancel</button>
            <button class="p-1 mt-4 bg-red-700 border rounded btn dark:bg-red-700 hover:bg-gray-400 hover:dark:bg-gray-500" wire:click="delete">Delete</button>
            </span>
            <button class="p-1 mt-4 bg-green-700 border rounded btn dark:bg-green-500 hover:bg-green-400 hover:dark:bg-gray-500" wire:click="$dispatch('openModal', {component: 'modal.manage.share', arguments: {type: 'album', id: '{{ $album->id }}'} })">Share</button>
        </div>
    </x-slot>
</x-modal>
