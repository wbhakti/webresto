<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function Login()
    {
        return view('sb-admin-2/login');
    }

    public function postlogin(Request $request)
    {
        try {

            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $passHash = base64_encode(hash_hmac('sha256', $request->input('username') . ':' . $request->input('password'), '#@R4dJaAN91n?#@', true));
            $user = DB::table('users')
                ->where('username', $request->input('username'))
                ->where('password', $passHash)
                ->first();

            if ($user) {
                session(['user_id' => $request->input('username'), 'role' => $user->role]);
                return redirect()->route('dashboard');
            } else {
                return back()->with('error', 'Username atau password salah');
            }
        } catch (\Exception $e) {
            //dd($e);
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan : ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
        }

        return view('sb-admin-2/dashboard');
    }

    public function MasterMerchant()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataMerchant = DB::table('merchants')->get();
            return view('sb-admin-2/mastermerchant', [
                'data' => $dataMerchant
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data merchant: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal memuat data merchant');
        }
    }

    public function postmerchant(Request $request)
    {
        try {

            if($request->input('proses') == 'edit'){

                if ($request->hasFile('img_merchant')){
                    $file = $request->file('img_merchant');
                    $fileName = $file->getClientOriginalName();

                    return redirect()->route('MasterMerchant')->with('success');

                }else{
                    return redirect()->route('MasterMerchant')->with('success');
                }
            }
            else if($request->input('proses') == 'delete'){
                
                return redirect()->route('MasterMerchant')->with('success');
            }
            else{
                return redirect()->route('MasterMerchant')->with('success', 'gagal');
            }

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterMerchant')->with('error', 'gagal save merchant');
        }
    }

    public function MasterMenu()
    {
        try {

            $dataMenu = DB::table('menus')->get();
            $dataKategori = DB::table('categories')->get();

            return view('sb-admin-2/mastermenu', [
                'data' => $dataMenu,
                'datakategori' => $dataKategori
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data menu: ' . $e->getMessage());
            return redirect()->route('MasterMerchant')->with('error', 'gagal load menu');
        }
    }

    public function MasterKategori()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataKategori = DB::table('categories')->get();
            return view('sb-admin-2/masterkategori', [
                'data' => $dataKategori
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data kategori: ' . $e->getMessage());
            return redirect()->route('MasterMerchant')->with('error', 'gagal load kategori');
        }
    }

    public function logout(Request $request)
    {
        // Menghapus semua data dari sesi
        $request->session()->flush();
        return redirect()->route('menu');
    }

    public function transaction(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
        }

        $dataTransaksi = DB::table('transactions')
            ->where('addtime', '>=', $request->date_start . ' 00:00:00')
            ->where('addtime', '<=', $request->date_end . ' 23:59:59')
            ->get();

            return view('sb-admin-2/mastertransaksi', [
                'data' => $dataTransaksi,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
            ]);
    }

    
}
