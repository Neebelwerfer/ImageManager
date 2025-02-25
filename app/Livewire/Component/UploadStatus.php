<?php

namespace App\Livewire\Component;

use App\Models\ImageUpload;
use App\Models\Upload;
use App\Support\Enums\UploadStates;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class UploadStatus extends Component
{

    #[Locked]
    public $completedImageUploads = [];

    #[Locked]
    public $imageUploads = [];

    #[Locked]
    public int $id;

    public bool $updated = false;

    #[On('echo:upload.{id},.stateUpdated')]
    public function onStateUpdated($data)
    {
        $this->retrieveImageUploads();
        $this->updated = true;
    }

    #[On('echo:upload.{id},.newUpload')]
    public function NewUpload($data)
    {
        $upload = Upload::where('user_id', Auth::user()->id)->where('ulid', $data['ulid'])->first();
        $this->imageUploads[$data['ulid']] = ['state' => $upload->state, 'startTime' => $upload->created_at->diffForHumans(), 'count' => $upload->images()->count()];
        $this->updated = true;
    }

    public function getStateColour($state)
    {
        switch($state){
            case 'waiting':
                return 'border-gray-600';
            case 'scanning':
            case 'processing':
            case 'uploading':
                return 'border-orange-500';
            case 'foundDuplicates':
            case 'error':
                return 'border-red-600';
        }
    }

    public function retrieveImageUploads()
    {
        $uploads = Upload::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        $this->imageUploads = [];
        $this->completedImageUploads = [];
        foreach ($uploads as $upload)
        {
            if($upload->state === "done")
                $this->completedImageUploads[$upload->ulid] = ['state' => $upload->state, 'startTime' => $upload->created_at->diffForHumans(), 'count' => $upload->images()->count()];
            else
            {
                $this->imageUploads[$upload->ulid] = ['state' => $upload->state, 'startTime' => $upload->created_at->diffForHumans(), 'count' => $upload->images()->count()];
            }
        }
    }

    public function mount()
    {
        $this->id = Auth::user()->id;
        $this->retrieveImageUploads();
    }

    public function render()
    {
        return <<<'HTML'
        <x-dropdown align="left" width="96">
            <x-slot name="trigger">
                <button class="relative p-1 border rounded hover:bg-gray-400 hover:dark:bg-gray-500" x-data="{updated: $wire.entangle('updated')}" :class="updated ? 'bg-yellow-500/80' : ''" x-on:click="updated = false">
                    <p class="text-sm" >Uploads</p>
                </button>
            </x-slot>
            <x-slot name="content">
                <div class="overflow-scroll" x-data="{showCompleted: false, showInProgress: false}">
                    <div class="flex flex-row justify-between">
                        <p class="ml-1 underline" x-on:click.stop="showCompleted = !showCompleted">Completed:</p>
                        <p class="mr-1"> {{ count($completedImageUploads) }} </p>
                    </div>
                    <template x-if="showCompleted">
                        <ul class="space-y-1">
                            @foreach ($completedImageUploads as $ulid => $imageUpload)
                                <li>
                                    <button x-on:click="Livewire.navigate('/upload/multiple/{{ $ulid }}')" class="w-full border rounded shadow-md bg-green-800/80 shadow-black">
                                        <div class="flex flex-row justify-between mx-2">
                                            <p class="overflow-clip">{{ $ulid }}</p>
                                            <p >Images: {{ $imageUpload['count'] }}</p>
                                        </div>
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
                        @foreach ($imageUploads as $ulid => $imageUpload)
                            <li>
                                <button x-on:click="Livewire.navigate('/upload/multiple/{{ $ulid }}')" class="w-full border rounded shadow-md {{ $this->getStateColour($imageUpload['state']) }} shadow-black">
                                    <div class="flex flex-row justify-between mx-2">
                                        <p class="overflow-clip">{{ $ulid }}</p>
                                        <p >Images: {{ $imageUpload['count'] }}</p>
                                    </div>
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
