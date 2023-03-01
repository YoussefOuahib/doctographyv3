<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Casts\Attribute;


use App\Models\User;
use App\Models\Appointment;
use App\Models\Condition;
use App\Models\Payment;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;
    protected $dates = ['birth_date'];


    protected $fillable = [
        'full_name',
        'phone',
        'birth_date',
        'cin',
        'gender',
        'insurance'
    ];
    public function scopeStartOfMonth($query) {
        return $query->where('created_at', '>=', Carbon::now()->startOfMonth());
    }
 
    /**
     * Get the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
        
            get: fn ($value) => Crypt::decryptString($value),
            set: fn ($value) => Crypt::encryptString($value),
        );
    }
    
    /**
     * Get the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function cin(): Attribute
    {
        return Attribute::make(
        
            get: fn ($value) => Crypt::decryptString($value),
            set: fn ($value) => Crypt::encryptString($value),
        );
    }
 
    public function appointments() {
        return $this->hasMany(Appointment::class);
    }
}
