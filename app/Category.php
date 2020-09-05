<?php

namespace App;

use Faker\Generator;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
