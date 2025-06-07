<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ApiController  extends Controller
{
    public function LoginUser(Request $request)
    {
        try {

            // Validasi input
            $validatedData = $request->validate([
                'phone_number' => 'required|string',
                'password' => 'required|string'
            ]);

            $passHash = base64_encode(hash_hmac('sha256', $validatedData['phone_number'] . ':' . $validatedData['password'], '#@R4dJaAN91n?#@', true));
            $user = DB::table('customers')
                ->where('nomor_hp', $validatedData['phone_number'])
                ->where('password', $passHash)
                ->where('is_delete', 0)
                ->first();

            if ($user) {

                $expiresAt = Carbon::now()->addHours(1)->timestamp;
                $tokenData = json_encode([
                    'user_id' => $user->nomor_hp,
                    'expires_at' => $expiresAt
                ]);
                $token = $this->encryptAES128($tokenData);

                //update ke DB
                DB::table('customers')->where('rowid', $user->rowid)->update([ 'token' => $token]);

                return response()->json([
                    'endpoint' => 'login',
                    'responseCode' => '0',
                    'responseMessage' => 'login success',
                    'user' => [
                        'id' => $user->rowid,
                        'nama_lengkap' => $user->nama_lengkap,
                        'nomor_hp' => $user->nomor_hp,
                        'firebase_id' => $user->id_firebase,
                        'token' => $token,
                    ]
                ], 200);
                
            } else {
                return response()->json([
                    'endpoint' => 'login',
                    'responseCode' => '1',
                    'responseMessage' => 'login failed [user tidak ditemukan]'
                ], 200);
            }
        } catch (\Exception $e) {
            //dd($e);
            Log::error($request->input('phone_number') . ' Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'login',
                'responseCode' => '1',
                'responseMessage' => 'login failed [exception error]'
            ], 200);
        }
    }

    public function RegistrationUser(Request $request)
    {
        try {

            // Validasi input
            $validatedData = $request->validate([
                'fullname' => 'required|string|max:100',
                'phone_number' => 'required|string|max:15',
                'password' => 'required|string|max:12',
                'firebase_id' => 'required|string'
            ]);

            // Cek nomor HP sudah ada
            $isPhoneExist = DB::table('customers')->where('nomor_hp', $validatedData['phone_number'])->exists();
            if ($isPhoneExist) {
                return response()->json([
                    'endpoint' => 'register',
                    'responseCode' => '1',
                    'responseMessage' => 'phone number already registered'
                ], 200);
            }

            $passHash = base64_encode(hash_hmac('sha256', $validatedData['phone_number'] . ':' . $validatedData['password'], '#@R4dJaAN91n?#@', true));
            DB::table('customers')->insert([
                'nama_lengkap' => $validatedData['fullname'],
                'nomor_hp'=> $validatedData['phone_number'],
                'password' => $passHash,
                'id_firebase' => $validatedData['firebase_id'],
            ]);

            $user = DB::table('customers')
                ->where('nomor_hp', $validatedData['phone_number'])
                ->where('password', $passHash)
                ->first();

            $expiresAt = Carbon::now()->addHours(1)->timestamp;
            $tokenData = json_encode([
                'user_id' => $user->nomor_hp,
                'expires_at' => $expiresAt
            ]);
            $token = $this->encryptAES128($tokenData);

            //update ke DB
            DB::table('customers')->where('rowid', $user->rowid)->update([ 'token' => $token]);

            return response()->json([
                'endpoint' => 'register',
                'responseCode' => '0',
                'responseMessage' => 'register success',
                'user' => [
                    'id' => $user->rowid,
                    'nama_lengkap' => $user->nama_lengkap,
                    'nomor_hp' => $user->nomor_hp,
                    'firebase_id' => $user->id_firebase,
                    'token' => $token,
                ]
            ], 200);

        } catch (\Exception $e) {
            //dd($e);
            Log::error('RegistrationUser Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'register',
                'responseCode' => '1',
                'responseMessage' => 'register failed [exception error]'
            ], 200);
        }
    }

    public function DeleteUser(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'phone_number' => 'required|string',
                'password' => 'required|string'
            ]);

            $passHash = base64_encode(hash_hmac('sha256', $validatedData['phone_number'] . ':' . $validatedData['password'], '#@R4dJaAN91n?#@', true));
            $user = DB::table('customers')
                ->where('nomor_hp', $validatedData['phone_number'])
                ->where('password', $passHash)
                ->first();

            if ($user) {

                $expiresAt = Carbon::now()->addHours(1)->timestamp;
                $tokenData = json_encode([
                    'user_id' => $user->nomor_hp,
                    'expires_at' => $expiresAt
                ]);
                $token = $this->encryptAES128($tokenData);

                //update ke DB
                DB::table('customers')->where('rowid', $user->rowid)->update([ 'is_delete' => 1]);

                return response()->json([
                    'endpoint' => 'delete',
                    'responseCode' => '0',
                    'responseMessage' => 'Delete account success'
                    ]
                , 200);
                
            } else {
                return response()->json([
                    'endpoint' => 'delete',
                    'responseCode' => '1',
                    'responseMessage' => 'delete failed [user tidak ditemukan]'
                ], 200);
            }
        } catch (\Exception $e) {
            //dd($e);
            Log::error($request->input('phone_number') . ' Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'delete',
                'responseCode' => '1',
                'responseMessage' => 'delete failed [exception error]'
            ], 200);
        }
    }

    public function Checkout(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'nama' => 'required|string',
                'nomor_hp' => 'required|string|max:15',
                'meja' => 'required|string',
                'detail_order' => 'required|string',
                'total_bayar' => 'required|string',
                'metode_bayar' => 'required|string'
            ]);

            $tokenCheck = $this->validateToken($request->input('token'));
            if ($tokenCheck['status']) {

                $idTransaksi = 'ORDER'.Carbon::now()->addHours(7)->format('YmdHis');

                // Simpan data ke database
                DB::table('transactions')->insert([
                    'id_transaksi' => $idTransaksi,
                    'customer' => $validatedData['nama'],
                    'nomor_hp' => $validatedData['nomor_hp'],
                    'meja' => $validatedData['meja'],
                    'details' => $validatedData['detail_order'],
                    'total_bayar' => $validatedData['total_bayar'],
                    'metode_bayar' => $validatedData['metode_bayar'],
                    'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
                    'status' => 'BELUM_BAYAR',
                ]);

                return response()->json([
                    'endpoint' => 'checkout',
                    'responseCode' => '0',
                    'responseMessage' => 'checkout success',
                    'id_transaksi' => $idTransaksi
                ], 200);
                
            }else{

                return response()->json([
                    'endpoint' => 'checkout',
                    'responseCode' => '21',
                    'responseMessage' => $tokenCheck['message']
                ], 401);

            }

        } catch (\Exception $e) {
            
            Log::error('checkout Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'checkout',
                'responseCode' => '1',
                'responseMessage' => 'checkout failed [exception error]'
            ], 200);

        }
    }

    public function UploadStruk(Request $request)
    {
        try{

            $validatedData = $request->validate([
                'id_transaksi' => 'required|string'
            ]);

            $tokenCheck = $this->validateToken($request->input('token'));
            if ($tokenCheck['status']) {

                if ($request->hasFile('bukti_pembayaran')) {
                    $image = $request->file('bukti_pembayaran');
                    $filename = 'buktitransfer_'.$validatedData['id_transaksi'].'.jpg';
    
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
                            'endpoint' => 'upload-struk',
                            'responseCode' => '1',
                            'responseMessage' => 'format file tidak valid'
                        ], 200);
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
                    ->where('id_transaksi', $validatedData['id_transaksi'])
                    ->update([ 'bukti_bayar' => $filename, 'status' => 'KONFIRMASI', ]);
        
                    $mimage = 'webkopinggir/public/invoice/'. $filename;
                    
                    return response()->json([
                        'endpoint' => 'upload-struk',
                        'responseCode' => '0',
                        'responseMessage' => 'upload success',
                        'id_transaksi' => $validatedData['id_transaksi'],
                        'image_url' => url($mimage)
                    ], 200);

                }else{

                    return response()->json([
                        'endpoint' => 'upload-struk',
                        'responseCode' => '1',
                        'responseMessage' => 'image file tidak valid'
                    ], 200);

                }

            }else{

                return response()->json([
                    'endpoint' => 'upload-struk',
                    'responseCode' => '21',
                    'responseMessage' => $tokenCheck['message']
                ], 401);

            }

        }catch (\Exception $e) {
            Log::error('Gagal upload bukti pembayaran: ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'upload-struk',
                'responseCode' => '1',
                'responseMessage' => 'upload failed [exception error]'
            ], 200);
        }
    }

    public function Detail(Request $request)
    {
        try {

            // Validasi input
            $validatedData = $request->validate([
                'id_transaksi' => 'required|string|max:100'
            ]);

            $tokenCheck = $this->validateToken($request->input('token'));
            if ($tokenCheck['status']) {

                $dataTransaksi = DB::table('transactions')->where('id_transaksi', $validatedData['id_transaksi'])->first();
                if ($dataTransaksi) {
                    return response()->json([
                        'endpoint' => 'detail-order',
                        'responseCode' => '0',
                        'responseMessage' => 'success',
                        'data' => $dataTransaksi
                    ], 200);
                }else{
                    return response()->json([
                        'endpoint' => 'detail-order',
                        'responseCode' => '1',
                        'responseMessage' => 'detail-order not found',
                        'data' => null
                    ], 200);
                }
            }else{

                return response()->json([
                    'endpoint' => 'detail-order',
                    'responseCode' => '21',
                    'responseMessage' => $tokenCheck['message'],
                    'data' => null
                ], 401);

            }

        } catch (\Exception $e) {
            
            Log::error('detail-order Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'detail-order',
                'responseCode' => '1',
                'responseMessage' => 'detail-order failed [exception error]',
                'data' => null
            ], 200);

        }
    }

    public function History(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'phone_number' => 'required|string|max:15'
            ]);

            $tokenCheck = $this->validateToken($request->input('token'));
            if ($tokenCheck['status']) {

                $dataTransaksi = DB::table('transactions')->where('nomor_hp', $validatedData['phone_number'])->get();
                if ($dataTransaksi) {
                    return response()->json([
                        'endpoint' => 'history',
                        'responseCode' => '0',
                        'responseMessage' => 'history success',
                        'data' => $dataTransaksi
                    ], 200);
                }else{
                    return response()->json([
                        'endpoint' => 'history',
                        'responseCode' => '1',
                        'responseMessage' => 'history not found',
                        'data' => null
                    ], 200);
                }
            }else{

                return response()->json([
                    'endpoint' => 'history',
                    'responseCode' => '21',
                    'responseMessage' => $tokenCheck['message'],
                    'data' => null
                ], 401);

            }

        } catch (\Exception $e) {
            
            Log::error('History Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'history',
                'responseCode' => '1',
                'responseMessage' => 'history failed [exception error]',
                'data' => null
            ], 200);

        }
    }

    public function Menu(Request $request)
    {
        try {

                $dataCat = DB::table('categories')->get();
                $dataMenu = DB::table('menus')->where('is_delete', '0')->where('is_active', '0')->get();
                
                if ($dataCat) {
                    if ($dataMenu) {
                        return response()->json([
                            'endpoint' => 'menu',
                            'responseCode' => '0',
                            'responseMessage' => 'success',
                            'dataCategory' => $dataCat,
                            'dataMenu' => $dataMenu
                        ], 200);
                    } else {
                        return response()->json([
                            'endpoint' => 'menu',
                            'responseCode' => '1',
                            'responseMessage' => 'menu not found',
                            'dataCategory' => null,
                            'dataMenu' => null
                        ], 200);
                    }
                }else{
                    return response()->json([
                        'endpoint' => 'menu',
                        'responseCode' => '1',
                        'responseMessage' => 'category not found',
                        'dataCategory' => null,
                        'dataMenu' => null
                    ], 200);
                }

        } catch (\Exception $e) {
            
            Log::error('menu Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'menu',
                'responseCode' => '1',
                'responseMessage' => 'menu failed [exception error]',
                'dataCategory' => null,
                'dataMenu' => null
            ], 200);

        }
    }

    public function Merchant(Request $request)
    {
        try {

                $data = DB::table('merchants')->get();
                if ($dataMerchant = $data->first()) {
                    return response()->json([
                        'endpoint' => 'merchants',
                        'responseCode' => '0',
                        'responseMessage' => 'success',
                        'data' => $dataMerchant
                    ], 200);
                }else{
                    return response()->json([
                        'endpoint' => 'merchants',
                        'responseCode' => '1',
                        'responseMessage' => 'not found',
                        'data' => null
                    ], 200);
                }

        } catch (\Exception $e) {
            
            Log::error('merchants Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'merchants',
                'responseCode' => '1',
                'responseMessage' => 'merchants failed [exception error]',
                'data' => null
            ], 200);

        }
    }

    public function Status(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'phone_number' => 'required|string|max:15',
                'id_transaksi' => 'required|string|max:100'
            ]);

            $tokenCheck = $this->validateToken($request->input('token'));
            if ($tokenCheck['status']) {

                $dataTransaksi = DB::table('transactions')
                ->where('nomor_hp', $validatedData['phone_number'])
                ->where('id_transaksi', $validatedData['id_transaksi'])
                ->first();

                if ($dataTransaksi) {
                    return response()->json([
                        'endpoint' => 'status',
                        'responseCode' => '0',
                        'responseMessage' => 'status found',
                        'data' => [
                            'status' => $dataTransaksi->status ?? 'BELUM_BAYAR'
                        ]
                    ], 200);
                }else{
                    return response()->json([
                        'endpoint' => 'status',
                        'responseCode' => '1',
                        'responseMessage' => 'status not found',
                        'data' => null
                    ], 200);
                }
            }else{

                return response()->json([
                    'endpoint' => 'status',
                    'responseCode' => '1',
                    'responseMessage' => $tokenCheck['message'],
                    'data' => null
                ], 401);

            }

        } catch (\Exception $e) {
            
            Log::error('status Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'status',
                'responseCode' => '1',
                'responseMessage' => 'status failed [exception error]',
                'data' => null
            ], 200);

        }
    }

    function encryptAES128($plaintext)
    {
        $key = 'CaNElo#BagUS@123';
        $cipher = 'AES-128-ECB';
        $options = OPENSSL_RAW_DATA;

        $encrypted = openssl_encrypt($plaintext, $cipher, $key, $options);

        return base64_encode($encrypted);
    }

    function decryptAES128($encrypted)
    {
        $key = 'CaNElo#BagUS@123';
        $cipher = 'AES-128-ECB';
        $options = OPENSSL_RAW_DATA;

        $decoded = base64_decode($encrypted);
        return openssl_decrypt($decoded, $cipher, $key, $options);
    }

    function validateToken($token)
    {
        try {
            // Dekripsi token
            $tokenData = json_decode($this->decryptAES128($token), true);
            if (!isset($tokenData['user_id']) || !isset($tokenData['expires_at'])) {
                return ['status' => false, 'message' => 'invalid token'];
            }

            // Cek apakah token sudah expired
            if (Carbon::now()->timestamp > $tokenData['expires_at']) {
                return ['status' => false, 'message' => 'token expired'];
            }

            //cek token di table
            $user = DB::table('customers')
            ->where('nomor_hp', $tokenData['user_id'])
            ->where('token', $token)
            ->first();

            if ($user){

                // Token valid
                return ['status' => true, 'user_id' => $tokenData['user_id']];

            }else{
                return ['status' => false, 'message' => 'invalid token'];
            }

        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'invalid token'];
        }
    }

}
