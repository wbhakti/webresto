<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Services\FirebaseService;
use App\Services\QrisService;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\Category;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {

        $timeNow = now()->format('H:i');

        $merchant = DB::table('merchants')
        ->where('id', $request->input('merchantId'))->first();

        if ($timeNow < $merchant->open) {
            return response()->json([
                'message' => 'Mohon Maaf, order belum dibuka'
            ]);
        }
        
        if ($timeNow > $merchant->closed) {
            return response()->json([
                'message' => 'Mohon Maaf, order sudah ditutup'
            ]);
        }

        $productId = $request->input('id');
        $productName = $request->input('name');
        $productPrice = $request->input('price');
        $quantity = $request->input('quantity', 1);
        $img = $request->input('productImage');

        //get cart
        $cart = session()->get('cart', []);

        //sudah ada di cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            //produk baru ke cart
            $cart[$productId] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'price' => $productPrice,
                'quantity' => $quantity,
                'product_discount' => '0',
                'promotion_id' => '',
                'note' => '',
                'image' => $img,
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

        // Hit API Merchant
        $merchant = DB::table('merchants')->first();
        $cartCount = count($cart);

        //RESET PROMO
        foreach ($cart as $key => $item) {
            $cart[$key]['promotion_id'] = '';
            $cart[$key]['product_discount'] = 0;
            session()->put('cart', $cart);
        }

        // GET PROMO
        $promotions = Promotion::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_at')
                    ->orWhereTime('start_at', '<=', now()->format('H:i:s'));
            })
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhereTime('end_at', '>=', now()->format('H:i:s'));
            })
            ->with(['products', 'categories'])
            ->get();



        foreach ($promotions as $promotion) {
            foreach ($cart as $key => $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    continue;
                }

                // Cek apakah produk memenuhi promo
                if (!$this->isApplicablePromo($promotion, $product)) {
                    continue;
                }

                if ($promotion->type === 'percentage') {
                    $itemDiscount = $item['price'] * ($promotion->value / 100);
                } elseif ($promotion->discount_type === 'fixed') {
                    // $itemDiscount =
                    //     min($promotion->promo_value, $itemTotal);
                } else {
                    $itemDiscount = 0;
                }

                $cart[$key]['promotion_id'] = $promotion->id;
                $cart[$key]['product_discount'] = $itemDiscount;
                session()->put('cart', $cart);

            }
        }
            

        return response()
        ->view('home-page.cart', compact(
            'cart',
            'merchant',
            'cartCount'
        ))
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }

    private function isApplicablePromo($promotion, $product)
    {
        if ($promotion->scope === 'all') {
            return true;
        }

        if ($promotion->scope === 'product') {
            return $promotion->products
                ->contains('product_id', $product->id);
        }

        if ($promotion->scope === 'category') {
            return $promotion->categories
                ->contains('category_id', $product->category_id);
        }

        return false;
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()
        ->route('cart.view')
        ->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            
            session()->put('cart', $cart);

            $itemTotal = $cart[$id]['price'] * $cart[$id]['quantity'];
            $itemDiscountTotal = $cart[$id]['product_discount'] * $cart[$id]['quantity'];

            $grandTotal = 0;
            $resDiscount = 0;
            foreach ($cart as $item) {
                $grandTotal += $item['price'] * $item['quantity'];
                $resDiscount += $item['product_discount'] * $item['quantity'];

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

    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:255',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan di cart.'
            ], 404);
        }

        $cart[$id]['note'] = $request->note;

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil disimpan.'
        ]);
    }

    public function checkout(Request $request)
    {   
        try{

            $invoiceNumber = 'ORDER'.Carbon::now()->format('YmdHis');
            $merchant = DB::table('merchants')->first();
            $components = explode(",", $merchant->table_name);
            $mmeja = $components[$request->input('meja')];

            $cart    = session('cart', []);
			$details = [];
			$subtotal = 0;
            $subtotaldiscount = 0;
            $idPromotion = '';

			foreach ($cart as $id => $item) {
				$qty   = (int) ($item['quantity'] ?? 0);
				$price = (int) ($item['price'] ?? 0);
                $discount = (int) ($item['product_discount'] ?? 0);
                $subtotal += $qty * $price;
                $subtotaldiscount += $qty * $discount;

                $idPromotion = $item['promotion_id'];

				$details[] = [
					'product_id'    => $item['product_id'],
                    'product_name'  => $item['product_name'],
                    'price'         => $price,
                    'quantity'      => $qty,
                    'discount'      => $qty * $discount,
                    'subtotal'      => $qty * $price,
					'note'          => $item['note'],
				];
			}

            $qrisDynamic = QrisService::makeDynamicQR($merchant->qris_info, $subtotal - $subtotaldiscount);

            // Simpan data ke database
            $transactionId = DB::table('transactions')->insertGetId([
                'invoice_number'    => $invoiceNumber,
                'source'            => 'WEB ORDER',
                'customer'          => $request->input('nama'),
                'customer_hp'       => '',
                'table_id'          => $mmeja,
                'cashier_id'        => '',
                'qr_code'           => $qrisDynamic,
                'subtotal'          => $subtotal,
                'discount'          => $subtotaldiscount,
                'tax'               => '0',
                'service_charge'    => '0',
                'grand_total'       => $subtotal - $subtotaldiscount,
                'payment_status'    => 'UNPAID',
                'payment_method'    => $request->input('metode_pembayaran'),
                'order_status'      => 'UNPAID',
                'notes'             => '',
                'created_at'        => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            foreach ($details as $item) {
				DB::table('transaction_items')->insert([
                    'transaction_id'=> $transactionId,
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['product_name'],
                    'price'         => $item['price'],
                    'quantity'      => $item['quantity'],
                    'discount'      => $item['discount'],
                    'subtotal'      => $item['subtotal'],
					'note'          => $item['note'],
                    'created_at'    => Carbon::now()->format('Y-m-d H:i:s')
                ]);
			}

            if ($idPromotion != ''){
                DB::table('promotion_usages')->insert([
                    'promotion_id'    => $idPromotion,
                    'transaction_id'  => $transactionId,
                    'customer_id'     => $request->input('nama'),
                    'discount_amount' => $subtotaldiscount,
                    'used_at'         => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            }
            

            session()->forget('cart');

            return redirect()->route('success', ['id' => $transactionId]);

        }catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('menu')->with('error', 'gagal checkout');
        }
    }

    public function success($id)
    {
        try{

            $transaction = DB::table('transactions')->where('transaction_id', $id)->first();

            if (!$transaction) {
                abort(404);
            }

            $merchant = DB::table('merchants')->first();
            $phone_wa = $merchant->phone; 

            // Ambil data dari request
            $nama = $transaction->customer;
            $meja = $transaction->table_id;
            $qrisDynamic = $transaction->qr_code;
            $totalTagihan = $transaction->grand_total;
            $details = DB::table('transaction_items')->where('transaction_id', $id)->get();

            if($transaction->payment_method == 'qris'){

                $textHeading = 'Order berhasil dibuat!';
                $textBody = 'Segera lakukan pembayaran untuk proses pemesanan makanan!';

                return view('home-page.checkout', [
                    'phone_wa' => $phone_wa,
                    'cartCount' => 0,
                    'qrisDynamic' => $qrisDynamic,
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
                $timestamp = 'fivestar : ' . Carbon::now()->addHours(7)->format('Y-m-d H:i:s');
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
                ->where('transaction_id', $request->input('idtransaksi'))
                ->update([ 'payment_proof' => $filename, 'order_status' => 'PAID','payment_status' => 'PAID','updated_at' => Carbon::now()->format('Y-m-d H:i:s') ]);
    
                $mimage = 'fivestar/public/invoice/'. $filename;

                // ============= NOTIFIKASI ==============
                $admin = User::where('role', 'kasir')->first();
                $transaction = DB::table('transactions')->where('transaction_id', $request->input('idtransaksi'))->first();

                try {
                    if (!$admin) {
                        Log::warning('Admin kasir tidak ditemukan');
                    } elseif (empty($admin->fcm_token)) {
                        Log::warning('FCM token admin kosong', [
                            'admin_id' => $admin->id,
                        ]);
                    } else {
                        $firebase = app(FirebaseService::class);
                        $firebase->sendToToken(
                            $admin->fcm_token,
                            'Order Baru',
                            'Ada order baru dari ' . $transaction->customer,
                            [
                                'type' => 'NEW_ORDER',
                                'idTransaksi' => $transaction->transaction_id,
                                'customer' => $transaction->customer,
                                'meja' => $transaction->table_id,
                                'status' => $transaction->payment_status,
                            ]
                        );
                    }

                    
                } catch (\Exception $e) {
                    Log::error('Gagal kirim FCM', [
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage(),
                    ]);
                }
                
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
