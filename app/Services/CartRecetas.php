<?php

namespace App\Services;

use Illuminate\Support\Collection;


/**
 * Class Cart
 * @package App\Classes
 */

 class CartRecetas {

    // permite saber el tipo de datos de nuestra clase
    //define la clase cart como una collection
    protected Collection $cart_recetas;

        /**
     * Cart constructor.
     */
    public function __construct() {
        if (session()->has("cart_recetas")) {
            $this->cart_recetas = session("cart_recetas");
        } else {
            $this->cart_recetas = new Collection;
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
      return $this->cart_recetas;
  }


    /**
     * Save the cart on session
     */
    //actualizar la informacion cart
    protected function save(): void {
        session()->put("cart_recetas", $this->cart_recetas);
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
        $this->cart_recetas->push($product);
        $this->save();//llama al save del metodo de arriba

    }

    public function findProduct($product) {
        $this->cart_recetas->get($product);
        return $product->qty;

    }
    /**
     *
     * Remove Product from cart
     *
     * @param int $id
     */
    public function removeProduct(int $id): void {
        $this->cart_recetas = $this->cart_recetas->reject(function ($product) use ($id) {
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
        $amount = $this->cart_recetas->count(function ($product) {
            $cantidad_total = $product['qty'];
            return $cantidad_total;

        });

        return $amount;
    }

    public function totalAmount() {
        $precio = $this->cart_recetas->sum(function ($product) {
            $precio_total = $product['cost']*$product['qty']*$product['relacion'];
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
        return $this->cart_recetas->count();
    }

    /*
     * Clear cart
     */
    public function clear(): void {
        $this->cart_recetas = new Collection;
        $this->save();
    }


 }
