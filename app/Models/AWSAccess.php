<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\S3Service;
use DateTime;
use Aws\Exception\AwsException;


class AWSAccess extends Model
{
    use HasFactory;

     public function getPresignedUrl($key)
    {
        $s3Service = new S3Service();

        $expiration = new DateTime("+20 minutes");

        $bucket = env('AWS_BUCKET');
        $command = $s3Service->getClient()->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);
        try {
            $preSignedUrl = $s3Service->preSignedUrl($command, $expiration);
            return $preSignedUrl;
        } catch (AwsException $exception) {
            echo $linebreak;
            die();
        }
    }

}
