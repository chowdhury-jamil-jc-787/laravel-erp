<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationTracker extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationship to the Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship to the UploadedFile
    public function uploadedFile()
    {
        return $this->belongsTo(UploadedFile::class, 'uploaded_file_id');
    }
}
