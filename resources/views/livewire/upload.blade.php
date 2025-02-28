<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>


@script
<script>

    window.onCancel = function (event) {
        let component = Livewire.getByName('upload')[0];

        const uploading = component.get('uploading');

        if(uploading)
        {
            if(confirm("Leaving this page will cancel the upload"))
            {
                event.preventDefault();
                component.cancelUpload("images");
                Livewire.dispatch('UploadCancelled', {url: event.detail.url});
            }
            else
            {
                event.preventDefault();
                document.addEventListener('livewire:navigate', (event) => {
                    window.onCancel(event);
                }, {once: true});
            }
        }
    }

    document.addEventListener('livewire:navigate', (event) => {
        window.onCancel(event);
    }, {once: true});

    document.addEventListener('livewire:navigated', () => {
        let component = Livewire.getByName('upload')[0];
        let ulid = null;

        function chunkArray(array, chunkSize) {
            const chunks = [];
            for (let i = 0; i < array.length; i += chunkSize) {
                // Slice the array into chunks of 'chunkSize'
                chunks.push(array.slice(i, i + chunkSize));
            }
            return chunks;
        }

        function startUpload() {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open("Get", '{{ route('media.upload.start') }}', true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            console.log('Started Upload');
                            ulid = xhr.getResponseHeader('ulid');
                            console.log('ulid: ' + ulid);
                            resolve();
                        } else {
                            console.error('Failed to upload files.');
                            reject('Failed to start uploading');
                        }
                    }
                };
                xhr.send();
            });
        }


        function asyncUpload(ulid, files, onComplete, OnProgress)
        {
            return new Promise((resolve, reject) => {
                uploadImages(ulid, files,
                () => {

                    resolve();
                },
                (percentage) => {
                    OnProgress(percentage);
                },
                () => {
                    reject('upload failed');
                },
                () => {

                    reject('upload cancelled');
                })
            });
        }

        function uploadImages(ulid, files, onComplete, OnProgress, OnError, OnCancelled)
        {
            const formData = new FormData();

            if(files.length == 0)
            {
                OnCancelled();
            }

            for (let i = 0; i < files.length; i++) {
                formData.append("images[]", files[i]);
            }
            console.log('Sending ' + files.length + ' images');

            const xhr = new XMLHttpRequest();
            xhr.open("Post", '{{ route('media.upload') }}', true);
            xhr.setRequestHeader('ulid', ulid);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Files uploaded successfully!');
                        onComplete();
                    } else {
                        console.error('Failed to upload files.');
                        OnError()
                    }
                }
            };
            xhr.addEventListener("progress", (event) => {
                if (event.lengthComputable) {
                    const percentComplete = (event.loaded / event.total) * 100;
                    OnProgress(percentComplete);
                }
            });
            xhr.addEventListener("error", OnError);
            xhr.addEventListener("abort", OnCancelled);
            xhr.send(formData);
        }

        document.getElementById('imageInput').addEventListener('change', async function() {
            const files = [...document.getElementById("imageInput").files];
            const progressBar = document.getElementById("progress");
            const percentage =  document.getElementById("percentage")

            progressBar.value = 0;
            percentage.innerHTML = "0%";
            await startUpload();

            try {
                if(files.length <= 20)
                {
                    await asyncUpload(ulid, files,
                        () => {},
                        (p) => {
                            console.log(p);
                        }
                    );
                }
                else {
                    const chunks = chunkArray(files, 20);

                    let value = 0;
                    for (const [index, chunk] of chunks.entries())
                    {
                        let step = (chunk.length / files.length) * 100;
                        progressBar.value = value;
                        percentage.innerHTML = value.toFixed(0) + "%";

                        const uploading = component.get('uploading');
                        if(!uploading) return;

                        await asyncUpload(ulid, chunk,
                        () => {},
                        (p) => {
                            console.log(p);
                        });
                    }
                }
            } catch (error) {
                console.log(error);
                return;
            }
        });
    }, { once: true })
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
                    <h1 class="mb-2 text-6xl font-bold underline">Upload In progress: <span id="percentage">{{ number_format($progress, 0) }}%</span></h1>
                    <progress max="100" value="{{ $progress }}" id="progress"></progress>
                </div>
            </div>
        </div>
    </div>
</div>
