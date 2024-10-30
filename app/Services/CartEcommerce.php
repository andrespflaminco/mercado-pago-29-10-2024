<?php

namespace App\Services;

use Illuminate\Support\Collection;


/**
 * Class Cart
 * @package App\Classes
 */

 class CartEcommerce {

    // permite saber el tipo de datos de nuestra clase
    //define la clase cart como una collection
    protected Collection $cart_ecommerce;

        /**
     * cart_ecommerce constructor.
     */
    public function __construct() {
        if (session()->has("cart_ecommerce")) {
            $this->cart_ecommerce = session("cart_ecommerce");
        } else {
            $this->cart_ecommerce = new Collection;
        }
    }

    /**
     *
     * Get cart_ecommerce contents
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
      return $this->cart_ecommerce;
  }


    /**
     * Save the cart_ecommerce on session
     */
    //actualizar la informacion cart_ecommerce
    protected function save(): void {
        session()->put("cart_ecommerce", $this->cart_ecommerce);
        session()->save();

    }

    /**
     *
     * Add Product on cart_ecommerce
     *
     * @param $product
     */
    //agrega un producto al carrito
    public function addProduct($product): void {
        $this->cart_ecommerce->push($product);
        $this->save();//llama al save del metodo de arriba

    }


    public function findProduct($product) {
        $this->cart_ecommerce->get($product);
        return $product->qty;

    }
    /**
     *
     * Remove Product from cart_ecommerce
     *
     * @param int $id
     */
    public function removeProduct($id): void {
        $this->cart_ecommerce = $this->cart_ecommerce->reject(function ($product) use ($id) {
            return $product['id'] === $id;
        });
        $this->save();
    }

    /**
     *
     * calculates the total cost in the cart_ecommerce
     *
     * @param bool $formatted
     * @return mixed
     */
    public function totalCantidad() {
        $amount = $this->cart_ecommerce->sum(function ($product) {
            $cantidad_total = $product['qty'];
            return $cantidad_total;

        });

        return $amount;
    }

    public function totalAmount() {
        $precio = $this->cart_ecommerce->sum(function ($product) {
            $precio_total = $product['price']*$product['qty'];
            return $precio_total;

        });

        return $precio;
    }

    /**
     *
     * Total products in cart_ecommerce
     *
     * @return int
     */
    public function hasProducts(): int {
        return $this->cart_ecommerce->count();
    }

    /*
     * Clear cart_ecommerce
     */
    public function clear(): void {
        $this->cart_ecommerce = new Collection;
        $this->save();
    }


 }
