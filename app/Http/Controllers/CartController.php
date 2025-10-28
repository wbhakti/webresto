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

        $order = DB::table('configuration')->where('parameter', 'close_order')->first();
        if($order->value == 'closed'){
            return response()->json([
                'message' => 'Mohon Maaf Sudah Close Order'
            ]);
        }

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
        $merchant = DB::table('merchants')->first();
        $cartCount = count($cart);

        //HIT table discount
        $discount = 0;
        $result = DB::table('configuration')->where('parameter', 'diskon')->first();

        if ($result) {
            $time = $result->description;
            $timeArr = explode("-",$time);
            if (count($timeArr) > 1) {
                $startTime = $timeArr[0];
                $endTime = $timeArr[1];
                $today = Carbon::now()->addHours(7)->format('H:i');

                if ( strtotime($today) > strtotime($startTime) && strtotime($today) < strtotime($endTime) )  {
                    $discount = $result->value;
                }
            }
        }

        return view('home-page/cart', compact('cart', 'merchant', 'discount'), ['cartCount' => $cartCount]);
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

    public function update(Request $request, $id, $discount)
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

            $resDiscount = $grandTotal * $discount / 100;
            $total = $grandTotal - $resDiscount;

            return response()->json([
                'success' => true,
                'itemTotal' => number_format($itemTotal, 0, ',', '.'),
                'grandTotal' => number_format($grandTotal, 0, ',', '.'),
                'discount' => number_format($resDiscount, 0, ',', '.'),
                'total' => number_format($total, 0, ',', '.')
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function checkout(Request $request)
    {   
        try{

            session()->forget('cart');

            $idTransaksi = 'ORDER'.Carbon::now()->addHours(7)->format('YmdHis');
            $merchant = DB::table('merchants')->first();
            $components = explode(",", $merchant->table_name);
            $mmeja = $components[$request->input('meja')];

            // Simpan data ke database
            DB::table('transactions')->insert([
                'id_transaksi' => $idTransaksi,
                'customer' => $request->input('nama'),
                'meja' => $mmeja,
                'details' => $request->input('details'),
                'total_bayar' => $request->input('total'),
                'discount' => $request->input('discount'),
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

            $merchant = DB::table('merchants')->first();
            $phone_wa = $merchant->phone_number; 
            $qrisImage = asset('img/'. $merchant->qris_image );

            // Ambil data dari request
            $nama = $transaction->customer;
            $meja = $transaction->meja;
            $totalTagihan = $transaction->total_bayar;
            $details = json_decode($transaction->details, true);

            if($transaction->metode_bayar == 'qris'){

                $textHeading = 'Order berhasil dibuat!';
                $textBody = 'Segera lakukan pembayaran untuk proses pengantaran makanan!';

                return view('home-page.checkout', [
                    'phone_wa' => $phone_wa,
                    'cartCount' => 0,
                    'qrisImage' => $qrisImage,
                    'isQRIS' => true,
                    'head' => $textHeading,
                    'body' => $textBody,
                    'nama' => $nama,
                    'meja' => $meja,
                    'metodePembayaran' => 'QRIS',
                    'discount' => $transaction->discount,
                    'totalTagihan' => $totalTagihan,
                    'details' => $details,
                    'idtransaksi' => $id
                ]);

            }else{
                $textHeading = 'Order berhasil dibuat!';
                $textBody = 'Segera lakukan pembayaran dikasir!';

                return view('home-page.checkout', [
                    'phone_wa' => $phone_wa,
                    'cartCount' => 0,
                    'qrisImage' => $qrisImage,
                    'isQRIS' => false,
                    'head' => $textHeading,
                    'body' => $textBody,
                    'nama' => $nama,
                    'meja' => $meja,
                    'metodePembayaran' => 'TUNAI',
                    'discount' => $transaction->discount,
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
                $image = $request->file('bukti_pembayaran');
                $filename = 'buktitransfer_'.$request->input('idtransaksi').'.jpg';

                //kompres image
                $mimeType = $image->getMimeType();
                list($width, $height) = getimagesize($image->getRealPath());
                $newWidth = 600;
                $newHeight = 400;
                $tmp = imagecreatetruecolor($newWidth, $newHeight);

                if ($mimeType === 'image/jpeg') {
                    $source = imagecreatefromjpeg($image->getRealPath());
                } elseif ($mimeType === 'image/png'){
                    $source = imagecreatefrompng($image->getRealPath());
                    imagealphablending($tmp, false);
                    imagesavealpha($tmp, true);
                } else {
                    return response()->json([
                        'success' => false,
                    ]);
                }

                // Resize gambar
                imagecopyresampled($tmp, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Tambahkan teks
                $font = public_path('arial.ttf');
                $fontSize = 25;
                $textColor = imagecolorallocate($tmp, 255, 255, 255);
                $timestamp = 'kopian : ' . Carbon::now()->addHours(7)->format('Y-m-d H:i:s');
                $xTimestamp = 20;
                $yTimestamp = 50;

                imagettftext($tmp, $fontSize, 0, $xTimestamp, $yTimestamp, $textColor, $font, $timestamp);

                if ($mimeType === 'image/jpeg') {
                    imagejpeg($tmp, public_path('invoice') . '/' . $filename, 80); // JPEG kualitas 80%
                } elseif ($mimeType === 'image/png') {
                    imagepng($tmp, public_path('invoice') . '/' . $filename, 8); // PNG kompresi level 8
                }

                imagedestroy($tmp);
                imagedestroy($source);

                DB::table('transactions')
                ->where('id_transaksi', $request->input('idtransaksi'))
                ->update([ 'bukti_bayar' => $filename, ]);
    
                $mimage = 'webkopinggir/public/invoice/'. $filename;
                
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
