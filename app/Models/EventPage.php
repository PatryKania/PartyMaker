<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'main_banner',
        'content',
        'down_img',
        'down_content'
    ];
    protected $casts = [
    'content' => 'array',
    'down_content' => 'array',
];
    
     public function event()
    {
        return $this->belongsTo(Event::class);
    }
}