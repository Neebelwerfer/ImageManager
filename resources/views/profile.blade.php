<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <x-section class="w-full">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </x-section>

            <x-section class="w-full">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </x-section>

            <x-section class="w-full">
                <div class="w-full">
                    <livewire:profile.login-activity />
                </div>
            </x-section>

            <x-section class="w-full">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </x-section>
        </div>
    </div>
</x-app-layout>
