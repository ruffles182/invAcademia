<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'product_id',
        'quantity',
        'notes',
        'person'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Método boot para manejar eventos del modelo
    protected static function boot()
    {
        parent::boot();

        static::created(function ($movement) {
            $product = $movement->product;  // Asegúrate de que esto realmente obtiene el producto
            if ($product) {
                switch ($movement->type) {
                    case 'entrada':
                        // Incrementa la cantidad del producto
                        $product->increment('quantity', $movement->quantity);
                        break;
                    case 'salida':
                        // Decrementa la cantidad del producto
                        $product->decrement('quantity', $movement->quantity);
                        break;
                    case 'ajuste':
                        // Establece la cantidad del producto
                        $product->quantity = $movement->quantity;
                        $product->save();
                        break;
                    default:
                        // Registra un log si el tipo no es reconocido
                        \Log::info('Tipo de movimiento no reconocido:', ['type' => $movement->type]);
                        \Log::info('producto:', ['type' => $product->id]);
                        break;
                }
            } else {
                // Registra un log si no se encuentra el producto
                \Log::error('Producto no encontrado para el movimiento:', ['movement_id' => $movement->id]);
            }
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:entrada,salida,ajuste',
            // Asegúrate de validar otros campos necesarios aquí
        ]);

        // Log para verificar el valor recibido
        \Log::info('Valor de type recibido:', ['type' => $request->type]);

        // Proceso para guardar el movimiento, etc.
    }

}
