<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'token',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function userTests()
    {
        return $this->hasMany(UserTest::class);
    }
}