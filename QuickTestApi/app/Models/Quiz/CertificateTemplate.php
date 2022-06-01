<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $table='certificateTemplates';
    protected $primaryKey = 'certificateTemplateId';
    public $timestamps = false;
    protected $fillable = [
        'title',    
        'heading',
        'mainText',    
        'publishDate',
        'topLeftImagePath',    
        'topRightImagePath',
        'bottomMiddleImagePath',    
        'backgroundImagePath',
        'leftSignatureText',    
        'leftSignatureImagePath',
        'rightSignatureText',    
        'rightSignatureImagePath',
        'addedBy'
    ];

    protected $hidden = [
        'isMigrationData',
    ];
}
