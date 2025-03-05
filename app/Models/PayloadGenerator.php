<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModelPayload;

class PayloadGenerator extends Model
{
    use HasFactory;

    public  function generatePayload($fal, $generate)
    {
        // Fetch payload template from database
        $modelPayload = ModelPayload::where('model_type', $fal->model_type)->first();

        if (!$modelPayload) {
            throw new \InvalidArgumentException("Unsupported model type: " . $fal->model_type);
        }

        // Decode JSON payload template
        $payloadTemplate = json_decode($modelPayload->payload_template, true);

        // Replace placeholders dynamically
        $payload = $this->replacePlaceholders($payloadTemplate, $generate);

        return $payload;
    }

    private  function replacePlaceholders(array $template, $generate)
    {
        array_walk_recursive($template, function (&$value) use ($generate) {
            if (is_string($value) && preg_match('/\{(.+?)\}/', $value, $matches)) {
                $keys = explode('.', $matches[1]);
                $replacement = $this->getNestedProperty($generate, $keys);
                $value = $replacement ?? null;
            }
        });

        return $template;
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
}
