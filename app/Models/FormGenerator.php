<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModelPayload;

class FormGenerator extends Model
{
    use HasFactory;

    public function generateForm($type, $generate, $trains)
    {
        // Fetch payload template from database
        $fal = Fal::where('model_type', $type)->first();

        if (!$fal ) {
            throw new \InvalidArgumentException("Unsupported model type: " . $fal->model_type);
        }

        // Decode JSON payload template
        $payloadTemplate = json_decode($fal ->payload, true);

        // Replace placeholders dynamically
        $payload = $this->replacePlaceholders($payloadTemplate, $generate, $trains);

        return $payload;
    }

    private function replacePlaceholders(array $template, $generate, $trains)
    {
        // Generate HTML form based on the template
            $html = '';
            foreach ($template as $key => $value) {
             
                if(isset($value)){
                      // Handle other types of inputs
                      $html .= "<div class='form-group'>";
                      if(!empty($generate[$key])){
                          $html .= $this->displayMedia($generate[$key]);
                      }
                      if ($key === 'prompt') {
                          $html .= "<textarea name='{$key}' id='{$key}' class='form-control'></textarea>";
                      } elseif($key === 'audio_url') {
                          $html .= "<button type='button' class='btn btn-primary' onclick=\"document.getElementById('{$key}').click();\">Upload Audio</button>";
                          $html .= "<input type='file' name='{$key}' id='{$key}' class='form-control' style='display:none;'>";
                      } elseif($key === 'video_url' && !empty($generate[$key])) {
                          $html .= "<input type='hidden' name='{$key}' value='{$generate[$key]}' id='{$key}' class='form-control'>";
                      } elseif($key === 'image_url' && !empty($generate[$key])) {
                          $html .= "<input type='hidden' name='{$key}' value='{$generate[$key]}' id='{$key}' class='form-control'>";
                      }
                      $html .= "</div>";
                 }
            }
            return $html;
            }



    private  function getNestedProperty($object, $keys)
    {
        foreach ($keys as $key) {
            if (is_object($object) && isset($object->$key)) {
                $object = $object->$key;
            } elseif (is_array($object) && isset($object[$key])) {
                $object = $object[$key];
            } else {
                return null;
            }
        }
        return $object;
    }



public function displayMedia($filePath) {
    // Extract the file extension from the URL
    $fileExtension = $this->getFileExtension($filePath);
    // Convert to lowercase for consistency
    $fileExtension = strtolower($fileExtension);
    // Generate the appropriate HTML
    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
        // Display image
        return "<img src='$filePath' alt='Image' style='max-width:100%; height:auto;'>";
    } elseif ($fileExtension === 'mp4') {
        // Display video
        return "
        <video width='100%' controls>
            <source src='$filePath' type='video/mp4'>
            Your browser does not support the video tag.
        </video>";
    } elseif ($fileExtension === 'mp3') {
        // Display audio
        return "
        <audio controls>
            <source src='$filePath' type='audio/mpeg'>
            Your browser does not support the audio element.
        </audio>";
    } else {
        return null;
    }
}


//Method to get the .jog or .mp4 or .mp3 part of this example url https://fal.media/files/koala/05JDakuw80qhoFwS8VpHr_image.jpg

public function getFileExtension($filePath) {
    // Extract the file extension from the URL
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    // Convert to lowercase for consistency
    $fileExtension = strtolower($fileExtension);
    // If the file extension is not found, try to get it from the last part of the URL
    if (!$fileExtension) {
        $pathParts = explode('/', $filePath);
        $lastPart = end($pathParts);
        $fileExtension = pathinfo($lastPart, PATHINFO_EXTENSION);
    }
    // Convert to lowercase for consistency
    $fileExtension = strtolower($fileExtension);
    return $fileExtension;
}

}