<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, BelongsToUser;

    protected $fillable = ['name', 'color', 'icon', 'description'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function tasksCount(): int
    {
        return $this->tasks()->count();
    }

    public function activeTasksCount(): int
    {
        return $this->tasks()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
    }
}
