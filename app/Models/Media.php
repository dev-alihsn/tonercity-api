<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'disk',
        'path',
        'type',
        'alt',
    ];

    /**
     * Get the full URL for this media item.
     */
    public function getUrl(): string
    {
        return asset('storage/'.$this->path);
    }
}
