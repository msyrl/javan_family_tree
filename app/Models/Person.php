<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'person';

    public $timestamps = false;

    protected $guarded = ['id'];

    const GENDER_MEN = 1;

    const GENDER_WOMEN = 2;

    const GENDERS = [
        self::GENDER_MEN,
        self::GENDER_WOMEN,
    ];

    const GENDERS_STRING = [
        self::GENDER_MEN => 'Laki-laki',
        self::GENDER_WOMEN => 'Perempuan',
    ];
}
