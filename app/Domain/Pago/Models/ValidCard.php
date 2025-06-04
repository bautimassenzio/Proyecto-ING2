<?php

namespace App\Domain\Pago\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidCard extends Model
{
    use HasFactory;
    protected $table = 'valid_cards'; 
    protected $primaryKey = 'id';
    protected $fillable = ['card_number', 'expiry_month', 'expiry_year', 'cvv', 'cardholder_name'];

    public $timestamps = false;
}
