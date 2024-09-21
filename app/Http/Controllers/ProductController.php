<?php

namespace App\Http\Controllers;

use App\Product;
use Cart;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show() {
        $products = Product::all();
        return view('pages.products',['products'=>$products]);
    }
    public function cart_show() {
        if(Cart::isEmpty()) {
            $cartCollection = '';
        }
        else {
            $cartCollection = Cart::getContent();
            $cartCollection = json_decode($cartCollection);
        }
        /*if ($cartCollection->has(3)) {
            $result = 'yes';
        }else {
            $result = 'no';
        }*/
        /*$item = Cart::getContent()->get('3_200 mL');
        $product = Cart::get(3);*/


        return view('pages.cart',['cartCollection'=>$cartCollection, 'grandTotal'=>Cart::getTotal()]);
    }
    public function cart_add($id) {
        $product = Product::find($id);
        Cart::add(array(
            'id' => $id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'attributes' => array()
        ));
        return response()->json([
            'product' => $product,
        ]);
    }
}
