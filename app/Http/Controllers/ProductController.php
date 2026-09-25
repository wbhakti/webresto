<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // ============== Products ==============
    public function MasterProducts()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataProducts = Product::get();
            $dataCatagories = Category::get();

            return view('sb-admin-2/mastermenu', [
                'products' => $dataProducts,
                'catagories' => $dataCatagories
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data menu: ' . $e->getMessage());
            return redirect()->route('MasterProducts')->with('error', 'gagal load menu');
        }
    }

    public function AddProducts(Request $request)
    {
        try {
            
            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $file = $request->file('img_menu');
            $filename = $request->input('kategori').'_'.date('YmdHis').'.jpg';
            $file->move(base_path('../public/img'), $filename);

            $maxOrder = DB::table('products')->max('sort_order');
            $mSortOrder= ($maxOrder ?? 0) + 10;

            DB::table('products')->insert([
                'category_id' => $request->input('category_id'),
                'sku' => $request->input('sku'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'cost_price' => $request->input('cost_price'),
                'image' => $filename,
                'sort_order' => $mSortOrder,
                'is_active' => true,
            ]);

            return redirect()->route('MasterProducts')->with('success', 'berhasil tambah data');

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterProducts')->with('error', 'gagal simpan data');
        }
    }

    public function EditProducts(Request $request)
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

                    $product = Product::findOrFail($request->row_id);
                    // Update data product
                    $product->update([
                        'category_id'   => $request->category_id,
                        'name'          => $request->name,
                        'description'   => $request->description,
                        'price'         => $request->price,
                        'cost_price'    => $request->cost_price,
                        'image'         => $filename,
                    ]);

                }else{

                    $product = Product::findOrFail($request->row_id);
                    // Update data product
                    $product->update([
                        'category_id'   => $request->category_id,
                        'name'          => $request->name,
                        'description'   => $request->description,
                        'price'         => $request->price,
                        'cost_price'    => $request->cost_price,
                    ]);
                }

                return redirect()->route('MasterProducts')->with('success', 'berhasil edit data');
            }
            else if ($request->input('proses') == 'delete'){

                $product = Product::findOrFail($request->input('row_id'));
                $product->delete();

                return redirect()->route('MasterProducts')->with('success', 'berhasil hapus data');
            }
        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterProducts')->with('error', 'gagal simpan data');
        }
    }

    public function ActivedProducts(Request $request)
    {
        try {
            if (!session()->has('user_id')) {
                return response()->json(['success' => false, 'message' => 'You must be logged in to access the menu.'], 401);
            }

            $product = Product::findOrFail($request->row_id);

            $product->update([
                'is_active' => !$product->is_active
            ]);
    
            return redirect()->route('MasterProducts')->with('success', 'berhasil ubah status produk');

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    // ============== Products ==============

    // ============== Categories ==============
    public function MasterCategories()
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $dataCatagories = Category::get();

            return view('sb-admin-2/masterkategori', [
                'data' => $dataCatagories
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memuat data kategori: ' . $e->getMessage());
            return redirect()->route('MasterMerchant')->with('error', 'gagal load kategori');
        }
    }

    public function AddCategories(Request $request)
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            $maxOrder = DB::table('categories')->max('sort_order');
            $mSortOrder= ($maxOrder ?? 0) + 10;

            DB::table('categories')->insert([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'sort_order' => $mSortOrder,
                'is_active' => true,
            ]);

            return redirect()->route('MasterCategories')->with('success', 'berhasil tambah data');

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterCategories')->with('error', 'gagal simpan data');
        }
    }

    public function EditCategories(Request $request)
    {
        try {

            if (!session()->has('user_id')) {
                return redirect()->route('Login')->with('error', 'You must be logged in to access the menu.');
            }

            if($request->input('proses') == 'edit'){

                $category = Category::findOrFail($request->categories_id);
                // Update data category
                $category->update([
                    'name'          => $request->name,
                    'description'   => $request->description,
                    'is_active'     => $request->status,
                ]);

                return redirect()->route('MasterCategories')->with('success', 'berhasil edit data');
            }
            else if ($request->input('proses') == 'delete'){

                $category = Category::findOrFail($request->input('categories_id'));
                $category->delete();

                return redirect()->route('MasterCategories')->with('success', 'berhasil hapus data');
            }

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return redirect()->route('MasterCategories')->with('error', 'gagal edit data');
        }
    }

    public function ActivedCategories(Request $request)
    {
        try {
            if (!session()->has('user_id')) {
                return response()->json(['success' => false, 'message' => 'You must be logged in to access the menu.'], 401);
            }

            $category = Category::findOrFail($request->row_id);
            $category->update([
                'is_active' => !$category->is_active
            ]);
    
            return redirect()->route('MasterProducts')->with('success', 'berhasil ubah status kategori');

        } catch (\Exception $e) {
            Log::error('Gagal proses data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    // ============== Categories ==============
}