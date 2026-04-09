<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
      use HasFactory;
   public $timestamps = false;
   protected $guarded = [];

   public function contact(){
      //for one to one
    return $this->hasOne(Contact::class);

    //for one to many
   //  return $this->hasMany(Contact::class);
   }
}
