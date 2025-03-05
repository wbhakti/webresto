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
                'email' => 'required|string',
                'password' => 'required|string'
            ]);

            $passHash = base64_encode(hash_hmac('sha256', $validatedData['email'] . ':' . $validatedData['password'], '#@R4dJaAN91n?#@', true));
            $user = DB::table('customers')
                ->where('email', $validatedData['email'])
                ->where('password', $passHash)
                ->first();

            if ($user) {

                $expiresAt = Carbon::now()->addHours(1)->timestamp;
                $tokenData = json_encode([
                    'user_id' => $user->email,
                    'expires_at' => $expiresAt
                ]);
                $token = $this->encryptAES128($tokenData);

                //update ke DB
                DB::table('customers')->where('rowid', $user->rowid)->update([ 'token' => $token]);

                return response()->json([
                    'endpoint' => 'login',
                    'responseCode' => '0',
                    'responseMessage' => 'login success',
                    'token' => $token
                ], 200);
                
            } else {
                return response()->json([
                    'endpoint' => 'login',
                    'responseCode' => '1',
                    'responseMessage' => 'login failed [user tidak ditemukan]',
                    'token' => null
                ], 200);
            }
        } catch (\Exception $e) {
            //dd($e);
            Log::error($request->input('username') . ' Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'login',
                'responseCode' => '1',
                'responseMessage' => 'login failed [exception error]',
                'token' => null
            ], 200);
        }
    }

    public function RegistrationUser(Request $request)
    {
        try {

            // Validasi input
            $validatedData = $request->validate([
                'email' => 'required|string|max:100',
                'password' => 'required|string|max:6'
            ]);

            $passHash = base64_encode(hash_hmac('sha256', $validatedData['email'] . ':' . $validatedData['password'], '#@R4dJaAN91n?#@', true));
            
            DB::table('customers')->insert([
                'email' => $validatedData['email'],
                'password' => $passHash
            ]);

            return response()->json([
                'endpoint' => 'register',
                'responseCode' => '0',
                'responseMessage' => 'register success'
            ], 200);

        } catch (\Exception $e) {
            //dd($e);
            Log::error('registrationuser Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'register',
                'responseCode' => '1',
                'responseMessage' => 'register failed [exception error]'
            ], 200);
        }
    }

    public function History(Request $request)
    {
        try {

            $tokenCheck = $this->validateToken($request->input('token'));
            if ($tokenCheck['status']) {

                $dataTransaksi = DB::table('transactions')->where('id_transaksi', $request->input('id_transaksi'))->first();
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
                    'responseCode' => '1',
                    'responseMessage' => $tokenCheck['message'],
                    'data' => null
                ], 200);

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
            ->where('email', $tokenData['user_id'])
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
