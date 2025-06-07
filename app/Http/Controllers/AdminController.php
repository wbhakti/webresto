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

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == 'edit'){

                if ($request->hasFile('img_merchant')){
                    $file = $request->file('img_merchant');
                    $filename = $file->getClientOriginalName().'.jpg';
                    $file->move(public_path('img'), $filename);

                    DB::table('merchants')
                    ->where('id', $request->input('merchant_id'))
                    ->update([ 'nama' => $request->input('nama'), 'deskripsi' => $request->input('deskripsi'), 'image' => $filename]);

                    return redirect()->route('MasterMerchant')->with('success', 'berhasil edit data');

                }else{

                    DB::table('merchants')
                    ->where('id', $request->input('merchant_id'))
                    ->update([ 'nama' => $request->input('nama'), 'deskripsi' => $request->input('deskripsi'),]);

                    return redirect()->route('MasterMerchant')->with('success', 'berhasil edit data');
                }
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

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataMenu = DB::table('menus')->where('is_delete', '0')->get();
            $dataKategori = DB::table('categories')->where('is_delete', '0')->get();

            return view('sb-admin-2/mastermenu', [
                'data' => $dataMenu,
                'datakategori' => $dataKategori
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data menu: ' . $e->getMessage());
            return redirect()->route('MasterMerchant')->with('error', 'gagal load menu');
        }
    }

    public function postmenu(Request $request)
    {
        try {
            
            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == 'edit'){

                if ($request->hasFile('img_menu')){
                    $file = $request->file('img_menu');
                    $filename = $request->input('kategori').'_'.date('YmdHis').'.jpg';
                    $file->move(base_path('../public/img'), $filename);

                    DB::table('menus')
                    ->where('id', $request->input('menu_id'))
                    ->update([ 
                        'nama' => $request->input('nama'),
                        'harga' => $request->input('harga'),
                        'kategori' => $request->input('kategori'),
                        'image' => $filename,
                    ]);

                }else{

                    DB::table('menus')
                    ->where('id', $request->input('menu_id'))
                    ->update([ 
                        'nama' => $request->input('nama'),
                        'harga' => $request->input('harga'),
                        'kategori' => $request->input('kategori'),
                    ]);
                }

                return redirect()->route('MasterMenu')->with('success', 'berhasil edit data');
            }
            else if ($request->input('proses') == 'delete'){

                DB::table('menus')->where('id', $request->input('menu_id'))->update([ 'is_delete' => '1',]);

                return redirect()->route('MasterMenu')->with('success', 'berhasil hapus data');
            }
            else{
                
                $file = $request->file('img_menu');
                $filename = $request->input('kategori').'_'.date('YmdHis').'.jpg';
                //$file->move(public_path('img'), $filename);
                $file->move(base_path('../public/img'), $filename);

                DB::table('menus')->insert([
                    'nama' => $request->input('nama'),
                    'sku' => $request->input('sku'),
                    'harga' => $request->input('harga'),
                    'image' => $filename,
                    'kategori' => $request->input('kategori'),
                    'merchant_id' => '1',
                ]);

                return redirect()->route('MasterMenu')->with('success', 'berhasil tambah data');
            }

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterMenu')->with('error', 'gagal simpan data');
        }
    }

    public function MasterKategori()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataKategori = DB::table('categories')->where('is_delete', '0')->get();
            return view('sb-admin-2/masterkategori', [
                'data' => $dataKategori
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data kategori: ' . $e->getMessage());
            return redirect()->route('MasterMerchant')->with('error', 'gagal load kategori');
        }
    }

    public function postkategori(Request $request)
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == 'edit'){

                DB::table('categories')
                    ->where('id', $request->input('kategori_id'))
                    ->update([ 'nama' => $request->input('nama'),]);

                return redirect()->route('MasterKategori')->with('success', 'berhasil edit data');
            }
            else if ($request->input('proses') == 'delete'){

                //DB::table('categories')->where('id', $request->input('kategori_id'))->delete();
                DB::table('categories')
                    ->where('id', $request->input('kategori_id'))
                    ->update([ 'is_delete' => '1',]);

                return redirect()->route('MasterKategori')->with('success', 'berhasil hapus data');

            }
            else{

                DB::table('categories')->insert([
                    'nama' => $request->input('nama'),
                    'merchant_id' => '1',
                ]);

                return redirect()->route('MasterKategori')->with('success', 'berhasil tambah data');
            }

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterKategori')->with('error', 'gagal simpan data');
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
        try{

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

        }catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'gagal proses data');
        }
    }

    public function UpdateStatus(Request $request)
    {
        try {
            if (!session()->has('user_id')) {
                return response()->json(['success' => false, 'message' => 'You must be logged in to access the menu.'], 401);
            }
    
            $updated = DB::table('transactions')
                ->where('id_transaksi', $request->input('id'))
                ->update(['status' => 'LUNAS']);
    
            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
            } else {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
    
        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    public function ActivedMenu(Request $request)
    {
        try {
            if (!session()->has('user_id')) {
                return response()->json(['success' => false, 'message' => 'You must be logged in to access the menu.'], 401);
            }
    
            if ($request->input('proses') == 'actived'){
                DB::table('menus')
                ->where('id', $request->input('menu_id'))
                ->update(['is_active' => '0']);
                return redirect()->route('MasterMenu')->with('success', 'berhasil aktifkan menu');
            } else if ($request->input('proses') == 'not_actived'){
                DB::table('menus')
                ->where('id', $request->input('menu_id'))
                ->update(['is_active' => '1']);

                return redirect()->route('MasterMenu')->with('success', 'berhasil non-aktifkan menu');
            }
    
        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

}
