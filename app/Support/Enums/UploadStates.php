<?php

namespace App\Support\Enums;

enum UploadStates : string
{
    case Waiting = "waiting";
    case Scanning = "scanning";
    case FoundDuplicates = "foundDuplicates";
    case Processing = "processing";
    case Error = 'error';
    case Done = "done";
}
