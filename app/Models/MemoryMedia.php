<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemoryMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'memory_id',
        'type',
        'path',
    ];

    public function memory()
    {
        return $this->belongsTo(Memory::class);
    }
}
