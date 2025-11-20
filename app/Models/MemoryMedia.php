<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoryMedia extends Model
{
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
