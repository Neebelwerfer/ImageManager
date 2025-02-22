<?php

namespace App\Livewire\Component;

use App\Models\ImageUpload;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class UploadStatus extends Component
{

    #[Locked]
    public $completedImageUploads = [];

    #[Locked]
    public $imageUploads = [];

    public function getStateColour($state)
    {
        switch($state){
            case 'waiting':
                return 'border-gray-600';
            case 'scanning':
            case 'processing':
                return 'border-orange-500';
            case 'foundDuplicates':
            case 'error':
                return 'border-red-600';
        }
    }

    public function retrieveImageUploads()
    {
        $uploads = ImageUpload::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        $this->imageUploads = [];
        $this->completedImageUploads = [];
        foreach ($uploads as $upload)
        {
            if($upload->state === "done")
                $this->completedImageUploads[$upload->uuid] = ['state' => $upload->state, 'startTime' => $upload->created_at->diffForHumans()];
            else
                $this->imageUploads[$upload->uuid] = ['state' => $upload->state, 'startTime' => $upload->created_at->diffForHumans()];
        }
    }

    public function mount()
    {
        $this->retrieveImageUploads();
    }

    public function render()
    {
        return <<<'HTML'
        <x-dropdown align="left" width="96">
            <x-slot name="trigger">
                <p class="p-1 text-sm border rounded hover:bg-gray-400 hover:dark:bg-gray-500">Uploads</p>
            </x-slot>
            <x-slot name="content">
                <div class="overflow-scroll" x-data="{showCompleted: false, showInProgress: false}">
                    <div class="flex flex-row justify-between">
                        <p class="ml-1 underline" x-on:click.stop="showCompleted = !showCompleted">Completed:</p>
                        <p class="mr-1"> {{ count($completedImageUploads) }} </p>
                    </div>
                    <template x-if="showCompleted">
                        <ul class="space-y-1">
                            @foreach ($completedImageUploads as $uuid => $imageUpload)
                                <li>
                                    <button x-on:click="Livewire.navigate('/upload/{{ $uuid }}')" class="w-full border rounded shadow-md bg-green-800/80 shadow-black">
                                        <p class="overflow-clip">{{ $uuid }}</p>
                                        <div class="flex flex-row justify-between mx-2">
                                            <p>{{ $imageUpload['startTime'] }}
                                            <p>{{ $imageUpload['state'] }}</p>
                                        </div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </template>

                    <div class="flex flex-row justify-between">
                        <p class="ml-1 underline" x-on:click.stop="showInProgress = !showInProgress">In Progress:</p>
                        <p class="mr-1"> {{ count($imageUploads) }} </p>
                    </div>
                    <template x-if="showInProgress">
                    <ul class="space-y-1">
                        @foreach ($imageUploads as $uuid => $imageUpload)
                            <li>
                                <button x-on:click="Livewire.navigate('/upload/{{ $uuid }}')" class="w-full border rounded shadow-md {{ $this->getStateColour($imageUpload['state']) }} shadow-black">
                                    <p class="overflow-clip">{{ $uuid }}</p>
                                    <div class="flex flex-row justify-between mx-2">
                                        <p>{{ $imageUpload['startTime'] }}
                                        <p>{{ $imageUpload['state'] }}</p>
                                    </div>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    </template>
                </div>
            </x-slot>

        </x-dropdown>
        HTML;
    }
}
