<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $merchantId = $request->input('merchantId');
        $productId = $request->input('id');
        $productName = $request->input('name');
        $productPrice = $request->input('price');
        $quantity = $request->input('quantity', 1);
        $img = $request->input('productImage');

        $cart = session()->get('cart', []);

        if (!empty($cart)) {
            // Ambil merchantId di keranjang
            $currentMerchantId = reset($cart)['merchantId'];

            //reset keranjang
            if ($currentMerchantId !== $merchantId) {
                $cart = [];
            }
        }

        //sudah ada di cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            //produk baru ke cart
            $cart[$productId] = [
                'name' => $productName,
                'price' => $productPrice,
                'quantity' => $quantity,
                'merchantId' => $merchantId,
                'image' => $img,
                'idMenu' => $productId,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'cart' => $cart,
        ]);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/')->with('error', 'Keranjang belanja Anda kosong.');
        }

        if (!empty($cart)) {
            // Ambil merchantId
            $firstProduct = reset($cart);
            $merchantId = $firstProduct['merchantId'] ?? null;
        }

        // Hit API Merchant
        $merchant = DB::table('merchants')->first();;
        $cartCount = count($cart);

        return view('home-page/cart', compact('cart', 'merchant'), ['cartCount' => $cartCount]);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            
            session()->put('cart', $cart);

            $itemTotal = $cart[$id]['price'] * $cart[$id]['quantity'];

            $grandTotal = 0;
            foreach ($cart as $item) {
                $grandTotal += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'itemTotal' => number_format($itemTotal, 0, ',', '.'),
                'grandTotal' => number_format($grandTotal, 0, ',', '.')
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function checkout(Request $request)
    {   
        try{

            session()->forget('cart');

            $idTransaksi = 'ORDER'.Carbon::now()->addHours(7)->format('YmdHis');

            // Simpan data ke database
            DB::table('transactions')->insert([
                'id_transaksi' => $idTransaksi,
                'customer' => $request->input('nama'),
                'meja' => $request->input('meja'),
                'details' => $request->input('details'),
                'total_bayar' => $request->input('total'),
                'metode_bayar' => $request->input('metode_pembayaran'),
                'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
            ]);

            return redirect()->route('success', ['id' => $idTransaksi]);

        }catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('menu')->with('error', 'gagal checkout');
        }
    }

    public function success($id)
    {
        try{

            $transaction = DB::table('transactions')->where('id_transaksi', $id)->first();

            if (!$transaction) {
                abort(404);
            }

            // Ambil data dari request
            $nama = $transaction->customer;
            $meja = $transaction->meja;
            $totalTagihan = $transaction->total_bayar;
            $details = json_decode($transaction->details, true);

            if($transaction->metode_bayar == 'qris'){

                $qrisImage = asset('img/qris-kopian.jpeg');
                $textHeading = 'Order berhasil dibuat!';
                $textBody = 'Segera lakukan pembayaran untuk proses pengantaran makanan!';

                return view('home-page.checkout', [
                    'cartCount' => 0,
                    'qrisImage' => $qrisImage,
                    'isQRIS' => true,
                    'head' => $textHeading,
                    'body' => $textBody,
                    'nama' => $nama,
                    'meja' => $meja,
                    'metodePembayaran' => 'QRIS',
                    'totalTagihan' => $totalTagihan,
                    'details' => $details,
                    'idtransaksi' => $id
                ]);

            }else{
                
                $qrisImage = asset('img/qris-kopian.png');
                $textHeading = 'Order berhasil dibuat!';
                $textBody = 'Segera lakukan pembayaran dikasir!';

                return view('home-page.checkout', [
                    'cartCount' => 0,
                    'qrisImage' => $qrisImage,
                    'isQRIS' => false,
                    'head' => $textHeading,
                    'body' => $textBody,
                    'nama' => $nama,
                    'meja' => $meja,
                    'metodePembayaran' => 'TUNAI',
                    'totalTagihan' => $totalTagihan,
                    'details' => $details,
                    'idtransaksi' => $id
                ]);
            }

        }catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('menu')->with('error', 'status transaksi gagal');
        }
    }

    public function upload(Request $request)
    {
        try{

            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = 'buktitransfer_'.$request->input('idtransaksi').'.jpg';
                $file->move(public_path('invoice'), $filename);
    
                DB::table('transactions')
                ->where('id_transaksi', $request->input('idtransaksi'))
                ->update([ 'bukti_bayar' => $filename, ]);
    
                $mimage = 'webkopian/public/invoice/'. $filename;
                
                return response()->json([
                    'success' => true,
                    'imageUrl' => url($mimage),
                ]);
            }
    
            return response()->json(['success' => false]);

        }catch (\Exception $e) {
            Log::error('Gagal upload bukti pembayaran: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload file.'
            ], 500);
        }
    }
    
}
