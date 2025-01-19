<div class="relative w-full h-full overflow-hidden">

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            User Management
        </h2>
    </x-slot>

    <div class="relative flex flex-col w-full h-full">
        <div class="flex justify-center w-full">
            <div class="p-1.5 mx-4 my-2 bg-gray-800 border rounded h-full" style="width: 70%;">
                <div class="flex flex-row justify-between w-full p-2 mb-2 rounded border-y">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Add User</h2>
                    <form wire:submit.prevent="createUser">
                        <div class="flex flex-col">
                            <div class="flex flex-row justify-between">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input class="px-2 py-1 ml-1 text-black border rounded" type="text" placeholder="Name..." wire:model="userForm.name">
                            </div>
                            <div class="flex flex-row justify-between">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input class="px-2 py-1 ml-1 text-black border rounded" type="text" placeholder="Email..." wire:model="userForm.email">
                            </div>
                            <div class="flex flex-row justify-between">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <input class="px-2 py-1 ml-1 text-black border rounded" type="password" placeholder="Password..." wire:model="userForm.password">
                             </div>
                            <button class="p-1 bg-blue-600 border border-blue-500 rounded">Create</button>
                        </div>
                    </form>
                </div>


                <div class="flex flex-col justify-between w-full p-2 mb-2 space-y-2 border-b rounded">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Users</h2>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                    Images
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                    Registered
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-bold text-left text-gray-500 uppercase dark:text-gray-400">
                                    Last Login
                                </th>
                            </tr>
                        </thead>
                        @foreach ($users as $user)
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ $user->images->count() }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ $user->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ $user->lastLogin() }}
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
