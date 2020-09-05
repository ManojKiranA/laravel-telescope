<?php

namespace App;

use Faker\Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
    
    
    public function updateName()
    {
        $this->update([
            'name' => app(Generator::class)->name,
        ]);
    }
}
