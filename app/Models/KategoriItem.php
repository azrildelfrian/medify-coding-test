<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KategoriItem extends Model
{
    use HasFactory;

    protected $table = 'kategori_items';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            MasterItem::class,
            'item_kategori',
            'kategori_item_id',
            'master_item_id'
        )->withTimestamps();
    }
}
