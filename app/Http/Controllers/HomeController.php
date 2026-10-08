<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            $dataProducts = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as nama_kategori')
            ->where('products.deleted_at', null)
            ->where('products.is_active', 1)
            ->where('categories.is_active', 1)
            ->orderByDesc('products.is_favorite')
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
            $now = Carbon::now();

            $openTime = Carbon::createFromFormat('H:i', $merchant->open);
            $closedTime = Carbon::createFromFormat('H:i', $merchant->closed);

            if (!$merchant->is_active) {
                $merchantStatus = 'inactive';
            } elseif ($now->lt($openTime)) {
                $merchantStatus = 'before_open';
            } elseif ($now->gt($closedTime)) {
                $merchantStatus = 'after_close';
            } else {
                $merchantStatus = 'open';
            }


            $dataCategories = DB::table('categories')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->orderBy('sort_order')->get();

            return view('home-page/restoran', [
                'categories' => $dataCategories, 
                'products' => $dataProducts,
                'merchant' => $merchant,
                'cartCount' => $cartCount,
                'merchantStatus' => $merchantStatus
            ]);

        }catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            abort(500);
        }
    }

}
