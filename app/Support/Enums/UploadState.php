<?php

namespace App\Support\Enums;

enum UploadState : string
{
    case Waiting = "waiting";
    case Scanning = "scanning";
    case FoundDuplicates = "foundDuplicates";
    case Processing = "processing";
    case Finished = "finished";
}
