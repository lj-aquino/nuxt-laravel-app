<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'students'; 

    // Specify the fields that can be mass-assigned
    protected $fillable = [
        'student_number', 
        'student_name', 
        'enrolled', 
        'id_validated'
    ];

    // Optionally, if you want to define relationships (e.g., with face_encodings table)
    public function faceEncoding()
    {
        return $this->belongsTo(FaceEncoding::class, 'face_encoding_id');
    }
}
