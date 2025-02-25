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
            const progressBar = document.getElementById("progress");

            progressBar.value = 0;
            component.set('uploading', true);
            component.set('fileCount', files.length);
            Livewire.dispatch('UploadStarted');

            try {
                if(files.length <= 20)
                {
                    component.uploadMultiple('images.0', files,
                        (n) => {
                            Livewire.dispatch('ChunkComplete', { index: 0 });
                        },
                        () => {},
                        (e) => {
                                progressBar.value = e.detail.progress;
                        },
                        () => {
                            Livewire.dispatch('UploadCancelled');
                        }
                    );
                }
                else {
                    const chunks = chunkArray(files, 20);

                    let value = 0;
                    for (const [index, chunk] of chunks.entries())
                    {
                        let step = chunk.length / files.length;
                        component.uploadMultiple('images.'+index, chunk,
                            (n) => {
                                Livewire.dispatch('ChunkComplete', { index: index });
                                value += step * 100;
                                progressBar.value = value;
                            },
                            (error) => {
                                alert(error)
                            },
                            (e) => {
                            },
                            () => {
                                console.log('cancelled');
                                Livewire.dispatch('UploadCancelled');
                            }
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

<div class="relative flex flex-row h-full" x-data="{uploading: $wire.entangle('uploading')}">
    <div class="flex justify-center w-full">
        <div>
            <div wire:loading wire:target="image">
                Uploading...
                <x-spinning-loader />
            </div>
            <div class="flex flex-col">
                <div class="mb-3" x-show="!uploading">
                    <input type="file" accept="image/*" name="images" placeholder="Choose images" id="imageInput" multiple>
                    @error('image')
                        <div class="mt-1 mb-1 text-red-600 alert">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col" x-show="uploading" x-cloak>
                    <h1 class="text-6xl font-bold underline">Upload In progress</h1>
                    <progress id="progress" max="100"></progress>
                </div>
                @if($processing)
                    <div class="w-96 h-96">
                        <x-spinning-loader />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
