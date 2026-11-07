<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Comercio extends Model {
    use SoftDeletes;
    protected $fillable = ['nombre','slug','telefono_whatsapp','descripcion'];
    protected $casts = ['deleted_at' => 'datetime'];
}
