<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $grade_id
 * @property string $firstname
 * @property string $lastname
 * @property string $code
 */
class Student extends Model
{
  use HasFactory;
  protected $fillable = ['id', 'grade_id', 'firstname', 'lastname', 'code'];

  function name(): Attribute
  {
    return Attribute::make(
      get: fn($value) => "{$this->firstname} {$this->lastname}"
    );
  }
}
