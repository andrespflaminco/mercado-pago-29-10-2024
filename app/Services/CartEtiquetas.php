<?php

namespace App\Services;

use Illuminate\Support\Collection;


/**
 * Class Cart
 * @package App\Classes
 */

 class CartEtiquetas {

    // permite saber el tipo de datos de nuestra clase
    //define la clase cart como una collection
    protected Collection $cart_etiquetas;

        /**
     * Cart constructor.
     */
    public function __construct() {
        if (session()->has("cart_etiquetas")) {
            $this->cart_etiquetas = session("cart_etiquetas");
        } else {
            $this->cart_etiquetas = new Collection;
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
      return $this->cart_etiquetas;
  }


    /**
     * Save the cart on session
     */
    //actualizar la informacion cart
    protected function save(): void {
        session()->put("cart_etiquetas", $this->cart_etiquetas);
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
        $this->cart_etiquetas->push($product);
        $this->save();//llama al save del metodo de arriba

    }

    public function findProduct($product) {
        $this->cart_etiquetas->get($product);
        return $product->qty;

    }
    /**
     *
     * Remove Product from cart
     *
     * @param int $id
     */
    public function removeProduct($id): void {
        $this->cart_etiquetas = $this->cart_etiquetas->reject(function ($product) use ($id) {
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
        $amount = $this->cart_etiquetas->sum(function ($product) {
            $cantidad_total = $product['qty'];
            return $cantidad_total;

        });

        return $amount;
    }

    /**
     *
     * Total products in cart
     *
     * @return int
     */
    public function hasProducts(): int {
        return $this->cart_etiquetas->count();
    }

    /*
     * Clear cart
     */
    public function clear(): void {
        $this->cart_etiquetas = new Collection;
        $this->save();
    }


 }
