<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';
    public $timestamps = false; // we manage created_at manually

    protected $fillable = ['project_id', 'title', 'status', 'created_at'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getId()
    {
        return (int) $this->getAttribute('id');
    }
}
