<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = ['title','description','due_date','status','list_id'];

    public function list():BelongsTo
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }
}
