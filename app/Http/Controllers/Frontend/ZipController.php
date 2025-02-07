<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ZipArchive;

class ZipController extends Controller
{
    public function createZip($zipFileName, $photos)
    {
        $zip = new ZipArchive;

        if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === TRUE) {

            foreach ($photos as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();

            $path = public_path($zipFileName);

            return $path;
            
        } else {
            return "Failed to create the zip file.";
        }
    }
}