<?php

namespace App\Services;

use Illuminate\Support\Collection;


/**
 * Class Cart
 * @package App\Classes
 */

 class Cart {

    // permite saber el tipo de datos de nuestra clase
    //define la clase cart como una collection
    protected Collection $cart;

        /**
     * Cart constructor.
     */
    public function __construct() {
        if (session()->has("cart")) {
            $this->cart = session("cart");
        } else {
            $this->cart = new Collection;
        }
    }

    /**
     *
     * Get cart contents
     *
     */
    //podemos acceder al contenido del carrito
    //que tenga un cliente
    /**
   * Get all of the items in the collection.
   *
   * @return array
   */
  public function getContent()
  {
      return $this->cart;
  }


    /**
     * Save the cart on session
     */
    //actualizar la informacion cart
    protected function save(): void {
        session()->put("cart", $this->cart);
        session()->save();

    }

    /**
     *
     * Add Product on cart
     *
     * @param $product
     */
    //agrega un producto al carrito
    public function addProduct($product): void {
        $this->cart->push($product);
        $this->save();//llama al save del metodo de arriba

    }

    public function findProduct($product) {
        $this->cart->get($product);
        return $product->qty;

    }
    /**
     *
     * Remove Product from cart
     *
     * @param int $id
     */
    public function removeProduct($id): void {
        $this->cart = $this->cart->reject(function ($product) use ($id) {
            return $product['id'] === $id;
        });
        $this->save();
    }

    /**
     *
     * calculates the total cost in the cart
     *
     * @param bool $formatted
     * @return mixed
     */
    public function totalCantidad() {
        $amount = $this->cart->sum(function ($product) {
            $cantidad_total = $product['qty'];
            return $cantidad_total;

        });

        return $amount;
    }

    public function totalAmount() {
        $precio = $this->cart->sum(function ($product) {
            $subtotal = $product['cost']*$product['qty'];
            $descuento = $product['cost']*$product['qty']*$product['descuento'];
            $iva = ($subtotal - $descuento) * $product['iva'];
            
            $precio_total = $subtotal - $descuento + $iva;
            return $precio_total;

        });

        return $precio;
    }
    
        public function totalDescuento() {
            $descuento = $this->cart->sum(function ($product) {
                // Verifica que cost, qty y descuento sean numéricos
                if (is_numeric($product['cost']) && is_numeric($product['qty']) && is_numeric($product['descuento'])) {
                    // Si son numéricos, realiza el cálculo del descuento
                    $desc = $product['cost'] * $product['qty'] * $product['descuento'];
                } else {
                    // Si alguno no es numérico, asigna un valor predeterminado (0 en este caso)
                    $desc = 0;
                }
        
                return $desc;
            });
        
            return $descuento;
        }
        /*
        public function totalDescuento() {
        $descuento = $this->cart->sum(function ($product) {
            $desc = $product['cost']*$product['qty']*$product['descuento'];
            return $desc;

        });

        return $descuento;
    }
    */

    public function subtotalAmount() {
        $precio = $this->cart->sum(function ($product) {
            $precio_total = $product['cost']*$product['qty'];
            return $precio_total;

        });

        return $precio;
    }

    public function totalIva() {
        $precio = $this->cart->sum(function ($product) {
            $subtotal = $product['cost']*$product['qty'];
            $descuento = $product['cost']*$product['qty']*$product['descuento'];

            $precio_total = ($subtotal - $descuento) * $product['iva'];
            return $precio_total;

        });

        return $precio;
    }

    /**
     *
     * Total products in cart
     *
     * @return int
     */
    public function hasProducts(): int {
        return $this->cart->count();
    }

    /*
     * Clear cart
     */
    public function clear(): void {
        $this->cart = new Collection;
        $this->save();
    }


 }
