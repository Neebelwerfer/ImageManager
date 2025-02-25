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

        // Wrap the uploadMultiple function in a Promise
        function uploadChunk(index, chunk, onComplete, onProgress) {
            return new Promise((resolve, reject) => {
                component.uploadMultiple('images.' + index, chunk,
                (n) => {
                    onComplete();
                    resolve();
                },
                (error) => {
                    reject(error);
                },
                (e) => {
                    onProgress(e);
                },
                () => {
                    console.log('cancelled');
                    Livewire.dispatch('UploadCancelled');
                });
            });
        }

        document.getElementById('imageInput').addEventListener('change', async function() {
            const files = [...document.getElementById("imageInput").files];
            const progressBar = document.getElementById("progress");
            const percentage =  document.getElementById("percentage")

            progressBar.value = 0;
            percentage.innerHTML = "0%";
            component.set('uploading', true);
            component.set('fileCount', files.length);
            Livewire.dispatch('UploadStarted');

            try {
                if(files.length <= 20)
                {
                    await uploadChunk(0, files,
                        () => {
                            Livewire.dispatch('ChunkComplete', { index: 0 });
                        }, (e) => {
                            let val = e.detail.progress;
                            component.set('progress', val);
                            progressBar.value = val;
                            percentage.innerHTML = val + "%";
                        }
                    );
                    Livewire.dispatch('UploadFinished');
                }
                else {
                    const chunks = chunkArray(files, 20);

                    let value = 0;
                    for (const [index, chunk] of chunks.entries())
                    {
                        let step = (chunk.length / files.length) * 100;
                        progressBar.value = value;
                        percentage.innerHTML = value.toFixed(0) + "%";

                        await uploadChunk(index, chunk,
                        () => {
                            value += step
                            Livewire.dispatch('ChunkComplete', { index: index });
                        },
                        (e) => {
                            let val = value + (step * (e.detail.progress / 100));
                            component.set('progress', val);
                            progressBar.value = val;
                            percentage.innerHTML = val.toFixed(0) + "%";
                        });
                    };

                    Livewire.dispatch('UploadFinished');
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

<div class="relative flex flex-row h-full" x-data="{uploading: $wire.entangle('uploading'), processing: $wire.entangle('processing')}">
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
                    <h1 class="mb-2 text-6xl font-bold underline">Upload In progress: <span id="percentage">{{ number_format($progress, 0) }}%</span></h1>
                    <progress max="100" value="{{ $progress }}" id="progress"></progress>
                </div>

                <div class="w-96 h-96" x-show="processing">
                    <x-spinning-loader />
                </div>
            </div>
        </div>
    </div>
</div>
