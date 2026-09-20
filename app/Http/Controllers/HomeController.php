<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function termconditions()
    {
        return view('home-page/termsandconditions');
    }

    public function contact()
    {
        return view('home-page/contact');
    }

    public function menu(Request $request)
    {
        try{

            $dataCategories = [];
            $dataProducts = [];

            $reset = $request->query('reset');
            if ($reset === 'Y') {
                session()->forget('cart');
            }

            $cart = session()->get('cart', []);
            $cartCount = count($cart);

            $jamSekarang = now()->format('H:i:s');

            $dataProducts = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as nama_kategori')
            ->where('products.deleted_at', null)
            ->where('products.is_active', 1)
            ->orderBy('categories.sort_order')
            ->orderBy('products.sort_order')
            ->get();

            $mCat = $request->query('categories');
            
            if (!empty($mCat)) {
                if ($mCat !== "all") {
                    $dataProducts = $dataProducts->where('category_id', $mCat);
                }
            }

            $merchant = DB::table('merchants')->first();
            $dataCategories = DB::table('categories')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->orderBy('sort_order')->get();
            // $datapromo = DB::table('configuration')->where('parameter', 'popup_banner')->first();

            return view('home-page/restoran', [
                'categories' => $dataCategories, 
                // 'promo' => $datapromo, 
                'products' => $dataProducts,
                'merchant' => $merchant,
                'cartCount' => $cartCount
            ]);

        }catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            abort(500);
        }
    }

}
