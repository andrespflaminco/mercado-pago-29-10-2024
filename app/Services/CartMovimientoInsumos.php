<?php

namespace App\Services;

use Illuminate\Support\Collection;


/**
 * Class Cart
 * @package App\Classes
 */

 class CartMovimientoInsumos {

    // permite saber el tipo de datos de nuestra clase
    //define la clase cart como una collection
    protected Collection $cart;

        /**
     * Cart constructor.
     */
    public function __construct() {
        if (session()->has("cart-movimiento-insumos")) {
            $this->cart = session("cart-movimiento-insumos");
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
        session()->put("cart-movimiento-insumos", $this->cart);
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
     * calculates the total price in the cart
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
            $total = $this->cart->sum(function ($product) {
                $total_total = ($product['costo']*$product['qty']);
                return $total_total;

            });

            return $total;
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
