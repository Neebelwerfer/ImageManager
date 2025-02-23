<?php

namespace App\Support\Enums;

enum UploadStates : string
{
    case Uploading ="uploading";
    case Waiting = "waiting";
    case Scanning = "scanning";
    case FoundDuplicates = "foundDuplicates";
    case Processing = "processing";
    case Done = "done";
}
