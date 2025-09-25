<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Aws\S3\S3Client;

class PackageSignature extends Model
{
    public function getClientSignatureAttribute($value)
    {
        $s3_base_url = config('filesystems.disks.s3.url');
        $s3_image_path = $s3_base_url.'clientsignature/';
        // dd($s3_path);
        if(\Storage::has("/clientsignature/".$value) && $value != '' ){
            return asset($s3_image_path.$value);
        }else{
            return asset("storage/image/company-admin/signature/signature-default.svg");
        }
    }

    public function getEmployeeSignatureAttribute($value)
    {
        $s3_base_url = config('filesystems.disks.s3.url');
        $s3_image_path = $s3_base_url.'clientsignature/';
        if(\Storage::has("/clientsignature/".$value) && $value != '' ){
            return asset($s3_image_path.$value);
        }else{
            return asset("storage/image/company-admin/signature/signature-default.svg");
        }
    }


}
