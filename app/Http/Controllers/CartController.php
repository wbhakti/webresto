<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

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

        //HIT table discount
        $productDiscount = 0;
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
                    if ($request->input('isDiscount') == 1) {
                        $productDiscount = (($productPrice *  $quantity ) * $discount ) / 100;
                    }
                }
            }
        }
        
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
            $cart[$productId]['totalDiscount'] = $quantity * $productDiscount;
        } else {
            //produk baru ke cart
            $cart[$productId] = [
                'name' => $productName,
                'price' => $productPrice,
                'quantity' => $quantity,
                'merchantId' => $merchantId,
                'productDiscount' => $productDiscount,
                'totalDiscount' => $productDiscount,
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
            $cart[$id]['totalDiscount']  =  $request->quantity *  $cart[$id]['productDiscount'];
            
            session()->put('cart', $cart);

            $itemTotal = $cart[$id]['price'] * $cart[$id]['quantity'];
            $itemDiscountTotal = $cart[$id]['productDiscount'] * $cart[$id]['quantity'];

            $grandTotal = 0;
            $resDiscount = 0;
            foreach ($cart as $item) {
                $grandTotal += $item['price'] * $item['quantity'];
                $resDiscount += $item['productDiscount'] * $item['quantity'];
            }

            $total = $grandTotal - $resDiscount;

            return response()->json([
                'success' => true,
                'itemTotal' => number_format($itemTotal, 0, ',', '.'),
                'productDiscountTotal' => number_format($itemDiscountTotal, 0, ',', '.'),
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

            $idTransaksi = 'ORDER'.Carbon::now()->addHours(7)->format('YmdHis');
            $merchant = DB::table('merchants')->first();
            $components = explode(",", $merchant->table_name);
            $mmeja = $components[$request->input('meja')];

            $cart    = session('cart', []);
			$details = [];
			$subtotal = 0;
            $subtotaldiscount = 0;

			foreach ($cart as $id => $item) {
				$qty   = (int) ($item['quantity'] ?? 0);
				$price = (int) ($item['price'] ?? 0);
                $discount = (int) ($item['productDiscount'] ?? 0);

				$details[] = [
					'menu_id'  => $item['name'] ?? $id,
					'note'     => '-',
					'quantity' => $qty,
					'price'    => $price,
                    'product_discount'    => $discount,
				];
				$subtotal += $qty * $price;
                $subtotaldiscount += $qty * $discount;
			}

            // Simpan data ke database
            DB::table('transactions')->insert([
                'id_transaksi' => $idTransaksi,
                'customer' => $request->input('nama'),
                'meja' => $mmeja,
                'details' => json_encode($details),
                'total_bayar' => $subtotal,
                'discount' => $subtotaldiscount,
                'metode_bayar' => $request->input('metode_pembayaran'),
                'qris_dynamic' => $request->input('qris_dynamic'),
                'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s')
            ]);

            session()->forget('cart');

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
            $qrisDynamic = $transaction->qris_dynamic;
            $totalTagihan = $transaction->total_bayar;
            $details = json_decode($transaction->details, true);

            if($transaction->metode_bayar == 'qris'){

                $textHeading = 'Order berhasil dibuat!';
                $textBody = 'Segera lakukan pembayaran untuk proses pengantaran makanan!';

                return view('home-page.checkout', [
                    'phone_wa' => $phone_wa,
                    'cartCount' => 0,
                    'qrisDynamic' => $qrisDynamic,
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
                
                // Set a maximum height and width
                $width = 600;
                $height = 1000;

                // Get new dimensions
                list($width_orig, $height_orig) = getimagesize($image->getRealPath());

                $ratio_orig = $width_orig/$height_orig;

                if ($width/$height > $ratio_orig) {
                    $width = $height*$ratio_orig;
                } else {
                    $height = $width/$ratio_orig;
                }

                // Resample
                $tmp = imagecreatetruecolor($width, $height);
                // $tmp = imagecreatetruecolor($newWidth, $newHeight);

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
                // imagecopyresampled($tmp, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagecopyresampled($tmp, $source, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                // Tambahkan teks
                $font = public_path('arial.ttf');
                $fontSize = 12;
                $textColor = imagecolorallocate($tmp, 0, 0, 0);
                $timestamp = 'cubiq : ' . Carbon::now()->addHours(7)->format('Y-m-d H:i:s');
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
    
                $mimage = 'webcubiq/public/invoice/'. $filename;

                // ============= NOTIFIKASI ==============
                $admin = User::where('role', 'kasir')->first();
                $transaction = DB::table('transactions')->where('id_transaksi', $request->input('idtransaksi'))->first();

                if (!$admin) {
                    return 'Admin tidak ditemukan';
                }

                Log::info('Admin ditemukan', [
                    'id' => $admin->id,
                ]);

                Log::info('Subscription', [
                    'count' => $admin->pushSubscriptions()->count(),
                ]);

                $order = (object) [
                    'id_transaction' => $transaction->id_transaksi,
                    'nama' => $transaction->customer,
                ];

                $admin->notify(
                    new NewOrderNotification($order)
                );

                Log::info('Notifikasi berhasil dikirim');

                // ========================================
                
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
