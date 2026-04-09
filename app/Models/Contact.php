<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Contact extends Model
{
      use HasFactory;
   public $timestamps = false;
protected $guarded = [];

   public function student(){

   //for one to one and one to many
      return $this->belongsTo(Student::class);
   }
}
