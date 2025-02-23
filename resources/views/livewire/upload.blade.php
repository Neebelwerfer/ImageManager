<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>


@script
<script>
    document.addEventListener('livewire:initialized', function () {
        let component = Livewire.getByName('upload')[0];

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        function chunkArray(array, chunkSize) {
            const chunks = [];
            for (let i = 0; i < array.length; i += chunkSize) {
                // Slice the array into chunks of 'chunkSize'
                chunks.push(array.slice(i, i + chunkSize));
            }
            return chunks;
        }

        document.getElementById('imageInput').addEventListener('change', async function() {
            const files = [...document.getElementById("imageInput").files];

            component.set('uploading', true);
            component.set('fileCount', files.length);

            try {
                // for (const [index, file] of files.entries()) {
                //     component.upload('images.'+index, file,
                //         (n) => {},
                //         () => {
                //             alert('Error')
                //         },
                //         (e) => {},
                //         () => {
                //             Livewire.dispatch('UploadCancelled');
                //         }
                //     );
                //     await sleep(500);
                // };

                if(files.length <= 20)
                {
                    component.uploadMultiple('images', files,
                        (n) => {},
                        () => {},
                        (e) => {},
                        () => {}
                    );
                }
                else {
                    const chunks = chunkArray(files, 20);

                    for (const [index, chunk] of chunks.entries())
                    {
                        component.uploadMultiple('chunks.'+index, chunk,
                            (n) => {
                                Livewire.dispatch('ChunkComplete', { index: index });
                            },
                            (error) => {
                                alert(error)
                            },
                            (e) => {},
                            () => {}
                        );
                    };
                }
            } catch (error) {
                alert(error);
                Livewire.dispatch('UploadCancelled');
                return;
            }

        });

    });
</script>
@endscript

<div class="relative flex flex-row h-full">
    <div class="flex justify-center w-full">
        <div>
            <div wire:loading wire:target="image">
                Uploading...
                <x-spinning-loader />
            </div>
            <div class="flex flex-col">
                @if(!$uploading)
                    <div class="mb-3">
                        <input type="file" accept="image/*" name="images" placeholder="Choose images" id="imageInput" multiple>
                        @error('image')
                            <div class="mt-1 mb-1 text-red-600 alert">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif(!$processing)
                    <div class="">
                        <h1 class="text-6xl font-bold underline">Upload In progress: {{ count($images) }}/{{ $fileCount }}</h1>
                    </div>
                @else
                    <div class="w-96 h-96">
                        <x-spinning-loader />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
