<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Producto extends Model {
    use SoftDeletes;
    protected $fillable = ['nombre','descripcion','precio','precio_oferta','oferta_hasta','disponible'];
    protected function casts(): array { return ['precio'=>'decimal:2','precio_oferta'=>'decimal:2','oferta_hasta'=>'datetime','disponible'=>'boolean']; }
}
