<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskList extends Model
{
    protected $fillable = ['title','description','user_id'];
    protected $table = 'lists';
    protected $with = ['user'];

    public function task():HasMany
    {
        return $this->HasMany(Task::class);
    }
    public function user():BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
