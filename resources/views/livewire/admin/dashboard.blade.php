<div class="relative w-full h-full overflow-hidden">

    <div class="relative flex flex-col w-full h-full">
        <div class="flex justify-center w-full">
            <div class="grid grid-cols-1 grid-rows-4 gap-2 p-1.5 mx-4 my-2 bg-gray-800 border rounded h-full"
                style="width: 70%;">
                <div class="flex flex-col justify-between p-2 rounded w-max">
                    <ul class="w-full space-y-2">
                        <li><p class="p-1">Users: {{ $this->UserCount() }}</p></li>
                        <li><p class="p-1">Images: {{ $this->ImageCount() }}</p></li>
                        <li><p class="p-1">Storage available: {{ $this->SizeUsage()['free'] }} GB</p></li>
                        <li><p class="p-1">Storagespace used by images: {{ $this->SizeUsage()['usedByImages'] }} MB</p></li>
                        <li><p class="p-1">Storagespace used by thumbnails: {{ $this->SizeUsage()['usedByThumbnails'] }} MB</p></li>
                        <li>
                            <div class="flex flex-row">
                                <p class="p-1">Storagespace used by temporary images: {{ $this->SizeUsage()['usedByTemp'] }} MB </p>
                                <button class="p-1 ml-2 border rounded bg-red-600/90" wire:click="deleteTempImages()"
                                    wire:confirm="Are you sure you want to delete all temporary images? It might brick ongoing upload session">Delete
                                    temporary images</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
