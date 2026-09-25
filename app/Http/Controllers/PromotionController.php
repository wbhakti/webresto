<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Promotion;
use App\Models\PromotionProduct;
use App\Models\PromotionCategory;
use App\Models\Product;
use App\Models\Category;

class PromotionController extends Controller {
    // ============== Discount ==============
    public function MasterPromotions()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataPromotion = Promotions::get();
            $dataProducts = Product::get();
            $dataCatagories = Category::get();

            return view('sb-admin-2/masterPromotions', [
                'promotions' => $dataPromotion,
                'products' => $dataProducts,
                'categories' => $dataCatagories
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data menu: ' . $e->getMessage());
            return redirect()->route('MasterPromotions')->with('error', 'gagal load promo');
        }
    }

    public function show($id)
    {
        $discount =  DB::table('promotions')->where('deleted_at', null)->get();
        return response()->json($discount);
    }

    public function AddPromotions(Request $request)
    {
        try {
            
            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $request->validate([
                'code'          => 'required',
                'name'          => 'required',
                'promo_type'    => 'required',
                'promo_value'   => 'required|numeric',
                'start_time'    => 'required',
                'end_time'      => 'required',
                'scope'         => 'required|in:all,category,product',
            ]);

            $promotion = Promotion::create([
                'code'          => $request->code,
                'name'          => $request->name,
                'description'   => $request->description,
                'type'          => $request->promo_type,
                'value'         => $request->promo_value,
                'start_at'      => $request->start_time,
                'end_at'        => $request->end_time,
                'is_active'     => true,
                'scope'         => $request->scope,
            ]);

            if ($request->scope === 'category') {
                foreach ($request->category_ids ?? [] as $categoryId) {
                    $promotion->categories()->create([
                        'category_id' => $categoryId
                    ]);
                }
            }
            
            if ($request->scope === 'product') {
                foreach ($request->product_ids ?? [] as $productId) {
                    $promotion->products()->create([
                        'product_id' => $productId
                    ]);
                }
            }


            return redirect()->route('MasterPromotions')->with('success', 'berhasil tambah promo');

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterPromotions')->with('error', 'gagal simpan promo');
        }
    }

    public function EditPromotions(Request $request)
    {
        try {
            
            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == 'edit'){

                $request->validate([
                    'code'          => 'required',
                    'name'          => 'required',
                    'promo_type'    => 'required',
                    'promo_value'   => 'required|numeric',
                    'start_time'    => 'required',
                    'end_time'      => 'required',
                    'scope'         => 'required|in:all,category,product',
                ]);

                DB::transaction(function () use ($request) {

                    $promotion = Promotion::findOrFail($request->id_promo);
            
                    // Update data promotion
                    $promotion->update([
                        'code'          => $request->code,
                        'name'          => $request->name,
                        'description'   => $request->description,
                        'type'          => $request->promo_type,
                        'value'         => $request->promo_value,
                        'scope'         => $request->scope,
                        'start_at'      => $request->start_time,
                        'end_at'        => $request->end_time,
                    ]);
            
                    // Hapus relasi lama
                    PromotionProduct::where('promotion_id', $promotion->id)->delete();
            
                    PromotionCategory::where('promotion_id', $promotion->id)->delete();
            
                    // Tambahkan relasi baru
                    if ($request->scope === 'product') {
            
                        foreach ($request->product_ids ?? [] as $productId) {
            
                            PromotionProduct::create([
                                'promotion_id' => $promotion->id,
                                'product_id'   => $productId,
                            ]);
                        }
                    }
            
                    if ($request->scope === 'category') {
            
                        foreach ($request->category_ids ?? [] as $categoryId) {
            
                            PromotionCategory::create([
                                'promotion_id' => $promotion->id,
                                'category_id'  => $categoryId,
                            ]);
                        }
                    }
                });   

                return redirect()->route('MasterPromotions')->with('success', 'berhasil edit data');
            }
            else if ($request->input('proses') == 'delete'){

                $promotion = Promotion::find($request->id_promo);
                $promotion->delete();

                return redirect()->route('MasterPromotions')->with('success', 'berhasil hapus data');
            }
        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterPromotions')->with('error', 'gagal simpan data');
        }
    }

    public function ActivedPromotions(Request $request)
    {
        try {
            if (!session()->has('user_id')) {
                return response()->json(['success' => false, 'message' => 'You must be logged in to access the menu.'], 401);
            }

            $promotion = Promotion::findOrFail($request->row_id);

            $promotion->update([
                'is_active' => !$promotion->is_active
            ]);

            return redirect()->route('MasterPromotions')->with('success', 'berhasil ubah status promo');

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }
}