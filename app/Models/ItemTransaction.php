<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    protected $table = 'transaction_items';

    public function transaction()
    {
        return $this->belongsTo(
            Transaction::class,
            'transaction_id',
            'transaction_id'
        );
    }
}
