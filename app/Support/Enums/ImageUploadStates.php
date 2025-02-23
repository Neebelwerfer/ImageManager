<?php

namespace App\Support\Enums;

enum ImageUploadStates : string
{
    case Waiting = "waiting";
    case Scanning = "scanning";
    case FoundDuplicates = "foundDuplicates";
    case Processing = "processing";
    case Error = 'error';
    case Done = "done";
}
