<?php

namespace App\Services;

use Illuminate\Support\Collection;


/**
 * Class Cart
 * @package App\Classes
 */

 class CartPromociones {

    // permite saber el tipo de datos de nuestra clase
    //define la clase cart como una collection
    protected Collection $cart;

        /**
     * Cart constructor.
     */
    public function __construct() {
        if (session()->has("cart-promociones")) {
            $this->cart = session("cart-promociones");
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
        session()->put("cart-promociones", $this->cart);
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
            return $product['id_promos_productos'] === $id;
        });
        $this->save();
    }
    public function removeProductById($id,$referencia_variacion): void {
        $this->cart = $this->cart->reject(function ($product) use ($id,$referencia_variacion) {
            return ($product['id'] === $id) && ($product['referencia_variacion'] === $referencia_variacion) ;
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


    public function subtotalAmount() {
        $subtotal = $this->cart->sum(function ($product) {
            $subtotal_total = $product['price']*$product['qty'];
            return $subtotal_total;

        });

        return $subtotal;
    }

    public function totalIva() {
        
        $iva = $this->cart->sum(function ($product) {
            $subtotal = $product['price']*$product['qty'];
            $iva_total = ( $subtotal +  ($subtotal*$product['recargo']) - ($subtotal*($product['descuento']/100)))*$product['iva'];
            return $iva_total;

        });

        return $iva;
    }

    public function totalRecargo() {
        $recargo = $this->cart->sum(function ($product) {
            $subtotal = $product['price']*$product['qty'];
            $recargo_total = ($subtotal-$subtotal*($product['descuento']/100))*$product['recargo'];
            return $recargo_total;

        });

        return $recargo;
    }

    public function totalDescuento() {
        $descuento = $this->cart->sum(function ($product) {
            $subtotal = $product['price']*$product['qty'];
            $descuento_total = $subtotal*($product['descuento']/100);
            return $descuento_total;

        });

        return $descuento;
    }


    public function totalAmount() {
        $total = $this->cart->sum(function ($product) {
            
            $subtotal = $product['price']*$product['qty'];
            $recargo_total = ($subtotal-$subtotal*($product['descuento']/100))*$product['recargo'];
            $descuento_total = $subtotal*($product['descuento']/100);
            $total = $subtotal + $recargo_total - $descuento_total;
            
            $total_total = ($total + ($total *$product['iva']));
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
