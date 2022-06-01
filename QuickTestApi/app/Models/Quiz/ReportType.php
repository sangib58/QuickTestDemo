<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    protected $table='reportTypes';
    protected $primaryKey = 'reportTypeId';
    public $timestamps = false;
}
