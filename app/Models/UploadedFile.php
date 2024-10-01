<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationship to the QuotationTracker
    public function quotationTrackers()
    {
        return $this->hasMany(QuotationTracker::class, 'uploaded_file_id');
    }
}
