<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'content', 'color', 'pinned', 'category'];

    protected $casts = [
        'pinned' => 'boolean',
    ];

    public function scopePinned($query)
    {
        return $query->where('pinned', true);
    }

    public function excerpt(int $chars = 120): string
    {
        $text = strip_tags($this->content ?? '');
        $text = preg_replace('/\s+/', ' ', trim($text));
        return mb_strlen($text) > $chars ? mb_substr($text, 0, $chars) . '…' : $text;
    }
}
