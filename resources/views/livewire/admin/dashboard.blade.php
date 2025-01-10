<div class="relative w-full h-full overflow-hidden">

    <div class="relative flex flex-col w-full h-full">
        <div class="flex justify-center w-full">
            <div class="grid grid-cols-5 grid-rows-4 gap-2 p-1.5 mx-4 my-2 bg-gray-800 border rounded h-full" style="width: 70%;">
                <div class="flex flex-row justify-between p-2 rounded w-max">
                    <ul class="w-full space-y-2">
                        <li>Users: {{ $this->UserCount()}}</li>
                        <li>Images: {{ $this->ImageCount()}}</li>
                        <li>Storage available: {{ $this->SizeUsage()['free']}} GB</li>
                        <li>Storagespace used by images: {{ $this->SizeUsage()['usedByImages']}} MB</li>
                        <li>Storagespace used by thumbnails: {{ $this->SizeUsage()['usedByThumbnails']}} MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
