<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'surname', 
        'telephone',
        'address',
         'user_id'
        ];
    
        public function dettes()
        {
            return $this->hasMany(Dette::class);
        }
        public function user() {
            return $this->belongsTo(User::class);
        }

      
}