<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {}; ?>


<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Image Upload') }}
    </h2>
</x-slot>

<div class="mt-5 panel panel-primary card">

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="panel-body card-body" style="margin-left: 22%;">

        <form method="POST" enctype="multipart/form-data" id="upload-image" action="{{ url('upload') }}">
            @csrf

            <div class="mb-3">
                <div class="form-group">
                    <input type="file" name="image" placeholder="Choose image" id="image">
                    @error('image')
                        <div class="mt-1 mb-1 alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <img id="image-preview"
                    src="https://cdn.dribbble.com/users/4438388/screenshots/15854247/media/0cd6be830e32f80192d496e50cfa9dbc.jpg?resize=1000x750&vertical=center"
                    alt="preview image" style="max-height: 250px;">
            </div>

            <script>
                function previewImage(event) {
                    var input = event.target;
                    var reader = new FileReader();
                    reader.onload = function(){
                        var img = document.getElementById('imagePreview');
                        img.src = reader.result;
                        img.style.display = 'block';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            </script>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary" id="submit">Submit</button>
            </div>
    </div>
    </form>
</div>
