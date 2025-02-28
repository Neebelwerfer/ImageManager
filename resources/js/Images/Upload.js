export default (baseRoute) => ({
    uploading: false,
    ulid: null,
    baseRoute: baseRoute,
    activeUpload: null,

    startUpload() {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open("Get", this.baseRoute + '/start', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Started Upload');
                        resolve(xhr.getResponseHeader('ulid'));
                    } else {
                        //console.error('Failed to upload files.');
                        reject('Failed to start uploading');
                    }
                }
            };
            xhr.send();
        });
    },

    completeUpload(ulid) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open("Get", this.baseRoute + '/complete', true);
            xhr.setRequestHeader('ulid', ulid);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('completed Upload');
                        resolve(xhr.getResponseHeader('url'));
                    } else {
                        reject('something went wrong completing upload');
                    }
                }
            };
            xhr.send();
        });
    },

    asyncUpload(ulid, files, OnComplete, OnProgress) {
        return new Promise((resolve, reject) => {
            this.uploadImages(ulid, files,
            () => {
                resolve();
                OnComplete();
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
    },

    uploadImages(ulid, files, onComplete, OnProgress, OnError, OnCancelled) {
        const formData = new FormData();
        if(files.length == 0)
        {
            OnCancelled();
        }

        for (let i = 0; i < files.length; i++) {
            formData.append("images[]", files[i]);
        }
        //console.log('Sending ' + files.length + ' images to ulid: ' + ulid);

        const xhr = new XMLHttpRequest();
        xhr.onprogress = function(event) {
            if (event.lengthComputable) {
                console.log('total: ' + event.total + ' loaded: ' + event.loaded);
                const percentComplete = (event.loaded / event.total) * 100;
                OnProgress(percentComplete);
            }
        };

        xhr.addEventListener("error", OnError);
        xhr.addEventListener("abort", OnCancelled);

        xhr.open("Post", this.baseRoute, true);
        xhr.setRequestHeader('ulid', ulid);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    //console.log('Files uploaded successfully!');
                    onComplete();
                } else {
                    //console.error('Failed to upload files.');
                    OnError()
                }
                this.activeUpload = null;
            }
        };
        xhr.send(formData);
        this.activeUpload = xhr;
    },

    async handleUpload() {
        const files = [...document.getElementById("imageInput").files];
        const progressBar = document.getElementById("progress");
        const percentage =  document.getElementById("percentage")

        this.uploading = true;
        progressBar.value = 0;
        percentage.innerHTML = "0%";
        this.ulid = await this.startUpload();

        try {
            if(files.length <= 20)
            {
                await this.asyncUpload(this.ulid, files,
                    () => {
                        progressBar.value = 100;
                        percentage.innerHTML = "100%";
                    },
                    (p) => {
                        console.log(p);
                    }
                );
            }
            else {
                const chunks = this.chunkArray(files, 20);

                let value = 0;
                for (const [index, chunk] of chunks.entries())
                {
                    if(!this.uploading) break;

                    let step = (chunk.length / files.length) * 100;
                    progressBar.value = value;
                    percentage.innerHTML = value.toFixed(0) + "%";

                    await this.asyncUpload(this.ulid, chunk,
                    () => {
                        value += step;
                        progressBar.value = value;
                        percentage.innerHTML = value.toFixed(0) + '%';
                    },
                    (p) => {
                        console.log(p);
                    });
                }
            }

            if(this.uploading)
            {
                const url = await this.completeUpload(this.ulid);
                this.uploading = false;
                Livewire.navigate(url);
            }
        } catch (error) {
            console.log(error);
            this.cancelUpload();
            return;
        }
    },

    cancelUpload()
    {
        if(!this.uploading) return;

        this.uploading = false;
        document.getElementById("imageInput").value = '';
        console.log('upload cancelled');

        if(this.activeUpload != null)
        {
            this.activeUpload.abort();
            this.activeUpload = null;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("Get", this.baseRoute + '/cancel', true);
        xhr.setRequestHeader('ulid', this.ulid);
        xhr.send();
    },

    chunkArray(array, chunkSize) {
        const chunks = [];
        for (let i = 0; i < array.length; i += chunkSize) {
            chunks.push(array.slice(i, i + chunkSize));
        }
        return chunks;
    },

    init() {
        document.addEventListener('livewire:navigate', (event) => {
            if(this.uploading)
            {
                if(confirm("Leaving this page will cancel the upload"))
                {
                    event.preventDefault();
                    let url = event.detail.url;
                    this.cancelUpload();
                    Livewire.navigate(url);
                }
                else
                {
                    event.preventDefault();
                }
            }
        });
    },
});
