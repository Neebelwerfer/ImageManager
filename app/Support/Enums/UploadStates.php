<?php

namespace App\Support\Enums;

enum UploadStates : string
{
    case Uploading ="uploading";
    case Waiting = "waiting";
    case Processing = "processing";
    case Done = "done";
}
