<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

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
                session(['user_id' => $user->id, 'user_name' => $request->input('username'), 'role' => $user->role]);
                if ($user->role == "kasir") {
                    return redirect()->route('dayTransaction');
                } else {
                    return redirect()->route('dashboard');
                }
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

    public function dayTransaction()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }
            $today = Carbon::now('Asia/Jakarta');

            $dataTransaksi = DB::table('transactions')
                ->whereDate('addtime', $today )
                ->orderByDesc('addtime')
                ->get();

            return view('sb-admin-2/transaksi', [
                'data' => $dataTransaksi
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data transaksi: ' . $e->getMessage());
            return redirect()->route('MasterMerchant')->with('error', 'gagal load menu');
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
                ->update(['status' => $request->input('status')]);
    
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

    public function settingorder()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $closeorder = DB::table('configuration')->where('parameter', 'close_order')->first();
            return view('sb-admin-2/settingorder', [
                'data' => $closeorder
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data setting: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'gagal load setting');
        }
    }

    public function CloseOrder(Request $request)
    {
        try {
            if (!session()->has('user_id')) {
                return response()->json(['success' => false, 'message' => 'You must be logged in to access the menu.'], 401);
            }
    
            if ($request->input('proses') == 'open'){
                DB::table('configuration')
                ->where('parameter', 'close_order')
                ->update(['value' => 'open']);

                return redirect()->route('settingorder')->with('success', 'berhasil open order');
            } else if ($request->input('proses') == 'closed'){
                DB::table('configuration')
                ->where('parameter', 'close_order')
                ->update(['value' => 'closed']);

                return redirect()->route('settingorder')->with('success', 'berhasil close order');
            }
    
        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    public function MasterPromo()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataPromo = DB::table('configuration')->where('parameter', 'popup_banner')->get();
            return view('sb-admin-2/masterpromo', [
                'data' => $dataPromo
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data promo: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal memuat data merchant');
        }
    }

    public function postPopupPromo(Request $request)
    {
        try {
            
            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == 'edit'){

                if ($request->hasFile('img_promo')){
                    $file = $request->file('img_promo');
                    $filename = 'promo_'.date('YmdHis').'.jpg';
                    $file->move(base_path('../public/img'), $filename);

                    DB::table('configuration')
                    ->where('id', $request->input('id_promo'))
                    ->update([ 
                        'value' => $filename,
                        'description' => $request->input('editTitle')
                    ]);

                }else{

                    DB::table('configuration')
                    ->where('id', $request->input('id_promo'))
                    ->update([ 
                        'description' => $request->input('editTitle')
                    ]);
                }

                return redirect()->route('MasterPromo')->with('success', 'berhasil edit promo');
            }
            else if ($request->input('proses') == 'delete'){

                DB::table('configuration')->where('id', $request->input('id_promo'))->delete();

                return redirect()->route('MasterPromo')->with('success', 'berhasil hapus promo');
            }
            else{
                $file = $request->file('img_promo');
                $filename = 'promo_'.date('YmdHis').'.jpg';
                $file->move(base_path('../public/img'), $filename);

                DB::table('configuration')->insert([
                    'description' => $request->input('description'),
                    'parameter' => 'popup_banner',
                    'value' => $filename
                ]);
                return redirect()->route('MasterPromo')->with('success', 'berhasil tambah data');
            }

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterPromo')->with('error', 'gagal simpan promo');
        }
    }

    public function MasterDiskon()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataPromo = DB::table('configuration')->where('parameter', 'diskon')->get();
            return view('sb-admin-2/masterdiskon', [
                'data' => $dataPromo
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data diskon: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal memuat data merchant');
        }
    }

    public function postDiskon(Request $request)
    {
        try {
            
            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == 'edit'){

                $dateStart = $request->input('editAwal');
                $dateEnd = $request->input('editAkir');
                $diskon = $request->input('editDiskon');
                
                $description = $dateStart.'-'.$dateEnd;

                DB::table('configuration')
                    ->where('id', $request->input('id_diskon'))
                    ->update([ 
                        'value' => $diskon,
                        'description' => $description
                    ]);

                return redirect()->route('MasterDiskon')->with('success', 'berhasil edit diskon');
            }
            else if ($request->input('proses') == 'delete'){

                DB::table('configuration')->where('id', $request->input('id_promo'))->delete();

                return redirect()->route('MasterDiskon')->with('success', 'berhasil hapus diskon');
            }
            else{
                $dateStart = $request->input('startdiskon');
                $dateEnd = $request->input('enddiskon');
                $description = $dateStart.'-'.$dateEnd;

                DB::table('configuration')->insert([
                    'description' => $description,
                    'parameter' => 'diskon',
                    'value' => $request->input('diskon')
                ]);
                return redirect()->route('MasterDiskon')->with('success', 'berhasil tambah data');
            }

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterDiskon')->with('error', 'gagal simpan diskon');
        }
    }

    public function subscribe(Request $request)
    {
        $adminId = session('user_id');

        if (!$adminId) {
            return response()->json([
                'message' => 'Admin belum login'
            ], 401);
        }

        $admin = User::find($adminId);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin tidak ditemukan'
            ], 404);
        }

        $admin->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth'],
            $request->contentEncoding ?? 'aesgcm'
        );

        return response()->json([
            'success' => true
        ]);
    }
}
