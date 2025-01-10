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

                    <ul class="w-full space-y-2">
                        @foreach ($users as $user)
                            <li class="w-full border hover:bg-slate-600">
                                <div class="flex flex-row justify-between w-full p-2 rounded">
                                    <div class="flex flex-row w-full">
                                        <p>Name: {{ $user->name }}</p> <p class="ml-4">Email: {{ $user->email }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
