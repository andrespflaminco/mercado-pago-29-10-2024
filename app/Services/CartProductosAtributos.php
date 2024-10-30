<?php

namespace App\Services;

use Illuminate\Support\Collection;


/**
 * Class Cart
 * @package App\Classes
 */

 class CartProductosAtributos {

    // permite saber el tipo de datos de nuestra clase
    //define la clase cart como una collection
    protected Collection $cart;

        /**
     * Cart constructor.
     */
    public function __construct() {
        if (session()->has("cart-atributos")) {
            $this->cart_atributos = session("cart-atributos");
        } else {
            $this->cart_atributos = new Collection;
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
      return $this->cart_atributos;
  }


    /**
     * Save the cart on session
     */
    //actualizar la informacion cart
    protected function save(): void {
        session()->put("cart-atributos", $this->cart_atributos);
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
        $this->cart_atributos->push($product);
        $this->save();//llama al save del metodo de arriba

    }

    public function findProduct($product) {
        $this->cart_atributos->get($product);
        return $product->qty;

    }
    /**
     *
     * Remove Product from cart_atributos
     *
     * @param int $id
     */
    public function removeProduct($id): void {
        $this->cart_atributos = $this->cart_atributos->reject(function ($product) use ($id) {
            return $product['referencia_id'] === $id;
        });
        $this->save();
    }

    /**
     *
     * calculates the total price in the cart_atributos
     *
     * @param bool $formatted
     * @return mixed
     */
    public function totalCantidad() {
        $amount = $this->cart_atributos->sum(function ($product) {
            $cantidad_total = $product['qty'];
            return $cantidad_total;

        });

        return $amount;
    }


    /**
     *
     * Total products in cart_atributos
     *
     * @return int
     */
    public function hasProducts(): int {
        return $this->cart_atributos->count();
    }

    /*
     * Clear cart_atributos
     */
    public function clear(): void {
        $this->cart_atributos = new Collection;
        $this->save();
    }


 }
