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

    public function menu(Request $request)
    {
        try{

            $dataKategori = [];
            $dataproduk = [];

            $reset = $request->query('reset');
            if ($reset === 'Y') {
                session()->forget('cart');
            }

            $cart = session()->get('cart', []);
            $cartCount = count($cart);

            $dataproduk = DB::table('menus')
            ->join('categories', 'menus.kategori', '=', 'categories.id')
            ->select('menus.*', 'categories.nama as nama_kategori')
            ->where('menus.is_delete', '0')
            ->get();

            $kategori = $request->query('kategori');
            
            if (!empty($kategori)) {
                if ($kategori !== "all") {
                    $dataproduk = $dataproduk->where('nama_kategori', $kategori);
                }
            }

            $merchant = DB::table('merchants')->first();
            $dataKategori = DB::table('categories')->where('is_delete', '0')->get();

            return view('home-page/restoran', [
                'kategori' => $dataKategori, 
                'produk' => $dataproduk,
                'merchant' => $merchant,
                'cartCount' => $cartCount
            ]);

        }catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            abort(500);
        }
    }

}
