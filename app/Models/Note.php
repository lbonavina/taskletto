<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes, BelongsToUser;

    protected $fillable = ['title', 'content', 'color', 'pinned', 'category', 'tags'];

    protected $casts = [
        'pinned' => 'boolean',
    ];

    public function share(): HasOne
    {
        return $this->hasOne(NoteShare::class);
    }

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

    /** Returns tags as a clean array. */
    public function getTagsArrayAttribute(): array
    {
        if (!$this->tags) return [];
        return array_values(array_filter(
            array_map('trim', explode(',', $this->tags))
        ));
    }

    /** Returns all distinct tags across all notes with counts. */
    public static function allTags(): array
    {
        try {
            $rows = static::whereNotNull('tags')->pluck('tags');
        } catch (\Exception $e) {
            // Column 'tags' may not exist yet if migration hasn't run
            return [];
        }
        $counts = [];
        foreach ($rows as $raw) {
            foreach (array_filter(array_map('trim', explode(',', $raw))) as $tag) {
                $counts[$tag] = ($counts[$tag] ?? 0) + 1;
            }
        }
        arsort($counts);
        return array_map(fn($tag, $count) => ['tag' => $tag, 'count' => $count],
            array_keys($counts), array_values($counts));
    }
}