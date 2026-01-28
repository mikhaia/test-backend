<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';
    public $timestamps = false; // we only have created_at

    protected $fillable = ['title', 'created_at'];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function getId()
    {
        return (int) $this->getAttribute('id');
    }
}
