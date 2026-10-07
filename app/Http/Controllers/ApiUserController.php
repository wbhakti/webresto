<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Services\FirebaseService;
use App\Models\Transaction;

class ApiUserController extends Controller
{
    public function LoginAdmin(Request $request)
    {
        try {

            // Validasi input
            $validatedData = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            $fcm_token = $request->input('fcm_token');
            $device = $request->input('device');

            $passHash = base64_encode(hash_hmac('sha256', $validatedData['username'] . ':' . $validatedData['password'], '#@R4dJaAN91n?#@', true));
            $user = DB::table('users')
                ->where('username', $validatedData['username'])
                ->where('password', $passHash)
                ->first();

            if ($user) {

                $expiresAt = Carbon::now()->addHours(20)->timestamp;
                $tokenData = json_encode([
                    'username' => $user->username,
                    'expires_at' => $expiresAt
                ]);

                $session = $this->encryptAES128($tokenData);
                //update ke DB

                DB::table('users')->where('id', $user->id)->update([ 'session' => $session, 'fcm_token' => $fcm_token, 'device' => $device]);

                return response()->json([
                    'endpoint' => 'user_login',
                    'responseCode' => '0',
                    'responseMessage' => 'login success',
                    'data' => [
                        'id'        => $user->id,
                        'username'  => $user->username,
                        'role'      => $user->role,
                        'token'     => $session,
                    ]
                ], 200);
                
            } else {
                return response()->json([
                    'endpoint' => 'user_login',
                    'responseCode' => '1',
                    'responseMessage' => 'login failed [user tidak ditemukan]'
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error($request->input('phone_number') . ' Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'user_login',
                'responseCode' => '1',
                'responseMessage' => 'login failed [exception error]'
            ], 200);
        }
    }

    public function SaveFCM(Request $request)
    {
        try {

            $tokenCheck = $this->validateUserToken($request->input('token'));
            if ($tokenCheck['status']) {
                // Validasi input
                $validatedData = $request->validate([
                    'user_id' => 'required|string',
                    'fcm_token' => 'required|string',
                    'device' => 'required|string'
                ]);

                //update ke DB
                $today = Carbon::now('Asia/Jakarta');
                $updated = DB::table('users')->where('id', $validatedData['user_id'])->update([ 'fcm_token' => $validatedData['fcm_token'], 'device' => $validatedData['device'], 'updated_at' => $today ]);
                
                if ($updated) {
                    return response()->json([
                        'endpoint' => 'save-fcm-token',
                        'responseCode' => '0',
                        'responseMessage' => 'SUKSES UPDATE FCM',
                        'data' => 'SUKSES UPDATE FCM ID'
                    ], 200);
                } else {
                    return response()->json([
                        'endpoint' => 'save-fcm-token',
                        'responseCode' => '1',
                        'responseMessage' => 'GAGAL UPDATE FCM',
                        'data' => 'GAGAL UPDATE FCM'
                    ], 200);
                }
            } else {
                return response()->json([
                    'endpoint' => 'save-fcm-token',
                    'responseCode' => '21',
                    'responseMessage' => $tokenCheck['message'],
                    'data' => null
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error($request->input('phone_number') . ' Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'save-fcm-token',
                'responseCode' => '1',
                'responseMessage' => 'GAGAL UPDATE FCM ID [exception error]'
            ], 200);
        }
    }

    public function HistoryTransaction(Request $request)
    {
        try {

            $tokenCheck = $this->validateUserToken($request->input('token'));
            if ($tokenCheck['status']) {

                $today = Carbon::now('Asia/Jakarta');

                $dataTransaksi = Transaction::with('details')
                ->whereDate('created_at', $today )
                ->orderBy('created_at', 'desc')
                ->get();

                if ($dataTransaksi) {
                    return response()->json([
                        'endpoint' => 'user_transaction',
                        'responseCode' => '0',
                        'responseMessage' => 'history success',
                        'data' => $dataTransaksi->map(function ($item) {
                            return [
                                'transactionId' => $item->transaction_id,
                                'invoiceNumber' => $item->invoice_number,
                                'nomorHp' => $item->customer_hp,
                                'customer' => $item->customer,
                                'table' => $item->table_id,
                                'details' => collect(json_decode($item->details, true))->map(function ($detail) {
                                    return [
                                        'menuId' => $detail['product_name'],
                                        'note' => $detail['note'],
                                        'quantity' => $detail['quantity'],
                                        'price' => $detail['price'],
                                        'productDiscount' => $detail['discount'],
                                    ];
                                })->values(),
                                'grandTotal' => $item->grand_total,
                                'discount' => $item->discount,
                                'paymentMethod' => $item->payment_method,
                                'paymentProof' => $item->payment_proof? url('fivestar/public/invoice/' . $item->payment_proof): null,
                                'orderStatus' => $item->order_status,
                                'addTime' => $item->created_at,
                                'qrCode' => $item->qr_code,
                            ];
                        })
                    ], 200);
                }else{
                    return response()->json([
                        'endpoint' => 'user_transaction',
                        'responseCode' => '1',
                        'responseMessage' => 'history not found',
                        'data' => null
                    ], 200);
                }
            }else{

                return response()->json([
                    'endpoint' => 'user_transaction',
                    'responseCode' => '21',
                    'responseMessage' => $tokenCheck['message'],
                    'data' => null
                ], 401);

            }

        } catch (\Exception $e) {
            
            Log::error('History Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'user_transaction',
                'responseCode' => '1',
                'responseMessage' => 'history failed [exception error]',
                'data' => null
            ], 200);

        }
    }

    public function UpdateStatus(Request $request)
    {
        try {
            $tokenCheck = $this->validateUserToken($request->input('token'));
            if ($tokenCheck['status']) {

                $transactionId = $request->input('id_transaksi');
                $status = $request->input('status');

                $transaction = Transaction::with('details')
                    ->where('transaction_id', $transactionId)
                    ->first();

                if (!$transaction) {
                    return response()->json([
                        'endpoint' => 'update_status_transaction',
                        'responseCode' => '1',
                        'responseMessage' => 'Transaksi tidak ditemukan',
                        'data' => $transactionId
                    ], 404);
                }

                // Update status
                $transaction->order_status = $status;
                $transaction->save();

                // Refresh data setelah update
                $transaction->load('details');
                // $ite = Transaction::with('details')
                // ->where('transaction_id', $transactionId)
                // ->first();

    
                $data = [
                    'transactionId' => $transactionId,
                    'invoiceNumber' => $transaction->invoice_number,
                    'nomorHp' => $transaction->customer_hp,
                    'customer' => $transaction->customer,
                    'table' => $transaction->table_id,
                    'details' => collect(json_decode($transaction->details, true))->map(function ($detail) {
                        return [
                            'menuId' => $detail['product_name'],
                            'note' => $detail['note'],
                            'quantity' => $detail['quantity'],
                            'price' => $detail['price'],
                            'productDiscount' => $detail['discount'],
                        ];
                    })->values(),
                    'grandTotal' => $transaction->grand_total,
                    'discount' => $transaction->discount,
                    'paymentMethod' => $transaction->payment_method,
                    'paymentProof' => $transaction->payment_proof? url('fivestar/public/invoice/' . $transaction->payment_proof): null,
                    'orderStatus' => $transaction->order_status,
                    'addTime' => $transaction->created_at,
                    'qrCode' => $transaction->qr_code,
                ];

                return response()->json([
                    'endpoint' => 'update_status_transaction',
                    'responseCode' => '0',
                    'responseMessage' => 'Status berhasil diperbarui',
                    'data' => $data
                ], 200);

            } else {
                return response()->json([
                    'endpoint' => 'update_status_transaction',
                    'responseCode' => '21',
                    'responseMessage' => $tokenCheck['message'],
                    'data' => null
                ], 401);
            }

            
    
        } catch (\Exception $e) {
            Log::error('Update Status Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'update_status_transaction',
                'responseCode' => '1',
                'responseMessage' => 'Update Status failed [exception error]',
                'data' => null
            ], 500);
        }
    }

    public function Notification(Request $request)
    {
        try {

            $tokens = $request->input('fcm_token');
            $transaction_id = $request->input('transaction_id');
            $customer = $request->input('customer');
            $meja = $request->input('meja');
            $status = $request->input('status');
            
            $firebase = app(FirebaseService::class);


            $firebase->sendToToken(
                $tokens,
                'Order Baru',
                'Ada order baru dari ' . $transaction_id,
                [
                    'type' => 'NEW_ORDER',
                    'idTransaksi' => $transaction_id,
                    'customer' => $customer,
                    'meja' => $meja,
                    'status' => $status,
                ]
            );

            return response()->json([
                'endpoint' => 'notification',
                'responseCode' => '0',
                'responseMessage' => 'notification Status sukses',
                'data' => $firebase
            ], 200);

        } catch (\Exception $e) {
            Log::error('Notification Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'notification',
                'responseCode' => '1',
                'responseMessage' => 'Notification failed [exception error]',
                'data' => null
            ], 500);
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

    function validateUserToken($token)
    {
        try {
            // Dekripsi token
            $tokenData = json_decode($this->decryptAES128($token), true);
            if (!isset($tokenData['username']) || !isset($tokenData['expires_at'])) {
                return ['status' => false, 'message' => 'invalid token'];
            }

            // Cek apakah token sudah expired
            if (Carbon::now()->timestamp > $tokenData['expires_at']) {
                return ['status' => false, 'message' => 'token expired'];
            }

            //cek token di table
            $user = DB::table('users')
            ->where('username', $tokenData['username'])
            ->where('session', $token)
            ->first();

            if ($user){

                // Token valid
                return ['status' => true, 'id' => $tokenData['username']];

            }else{
                return ['status' => false, 'message' => 'invalid token'];
            }

        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'invalid token'];
        }
    }

}
