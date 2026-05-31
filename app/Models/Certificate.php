<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['student_id', 'path_id', 'enrollment_id', 'cert_number', 'pdf_path', 'qr_code', 'issued_at'];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    public function student()    { return $this->belongsTo(User::class, 'student_id'); }
    public function path()       { return $this->belongsTo(Path::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }

    public function getVerifyUrlAttribute(): string
    {
        return route('certificates.verify', $this->cert_number);
    }
}
