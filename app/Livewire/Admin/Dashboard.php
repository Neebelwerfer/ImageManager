<?php

namespace App\Livewire\Admin;

use App\Models\Image;
use App\Models\User;
use FilesystemIterator;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

#[Layout('layouts.admin')]
class Dashboard extends Component
{

    public function UserCount()
    {
        return User::count();
    }

    public function ImageCount()
    {
        return Image::count();
    }

    function GetDirectorySize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }

    #[Computed()]
    public function SizeUsage()
    {
        $available = intval(disk_total_space(storage_path('app')) / 1024 / 1024 / 1024);
        $usedByImages = number_format((float)$this->GetDirectorySize(storage_path('app/images')) / 1024 / 1024, 2);
        $usedByFullImages = number_format((float)$this->GetDirectorySize(storage_path('app/originalImages')) / 1024 / 1024, 2);
        $usedByTemp = number_format((float)$this->GetDirectorySize(storage_path('app/temp')) / 1024 / 1024, 2);
        $usedByLiveWireTemp = number_format((float)$this->GetDirectorySize(storage_path('app/livewire-tmp')) / 1024 / 1024, 2);

        $used = $usedByFullImages + $usedByImages;

        $percent = ($used / $available) * 100;
        return ['used' => $used, 'free' => $available, 'percent' => $percent, 'usedByImages' => $usedByImages, 'usedByFullImages' => $usedByFullImages, 'usedByLiveWireTemp' => $usedByLiveWireTemp, 'usedByTemp' => $usedByTemp];

    }

    #[Computed()]
    public function memoryUsage(){
        return number_format((float)memory_get_usage() / 1024 / 1024, 2);
    }

    #[Computed()]
    public function softDeletedImages(){
        return Image::onlyTrashed()->count();
    }

    public function deleteSoftDeletedImages(){
        $softDeletedImages = Image::onlyTrashed()->get();
        foreach ($softDeletedImages as $image) {
            $image->forceDelete();
        }
    }

    public function deleteTempImages(){
        $temp = Storage::disk('local')->allFiles('livewire-tmp');
        foreach ($temp as $file) {
            Storage::disk('local')->delete($file);
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
