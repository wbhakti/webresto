<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';

    public function details()
    {
        return $this->hasMany(
            ItemTransaction::class,
            'transaction_id',
            'transaction_id'
        );
    }
}


// order_status 
// UNPAID
// PAID
// PROCESSING
// COMPLETED
// CANCELLED

// payment_status
// UNPAID
// PAID
// REFUNDED