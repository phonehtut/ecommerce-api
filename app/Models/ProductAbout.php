<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAbout extends Model
{
    use HasFactory;

    protected $table = 'product_abouts';

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'stock'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
