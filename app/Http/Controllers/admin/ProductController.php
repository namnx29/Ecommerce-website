<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ImportItem;
use App\Models\importProduct;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\TempImage;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ProductController extends Controller
{
    public function index(Request $request) {
        $products = Product::latest('id')->with('product_images');

        if(!empty($request->get('keyword'))) {
            $products = $products->where('title', 'like', '%'.$request->get('keyword').'%');
        }

        $products = $products->paginate(10);

        $data['products'] = $products;
        return view('admin.products.list', $data);
    }
    public function create() {
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $records = ImportItem::select('product_name', 'barcode')->orderBy('product_name', 'ASC')->distinct()->get();
        // $records = importProduct::orderBy('product_name', 'ASC')->get();
        // $data['recordsName'] = $recordsName;
        $data['records'] = $records;
        $data['categories'] = $categories;
        return view('admin.products.create', $data);
    }

    public function store(Request $request) {
        $rules = [
            'product_code' => 'required',
            'barcode' => 'required|unique:products',
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'unit' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];



        if (!empty($request->track_qty) && $request->track_qty == "Yes") {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->unit = $request->unit;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';

            $product->save();

            if(!empty($request->image_array)) {
                foreach ($request->image_array as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray);

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //Generate product thumb


                    //Large thumb
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/large/'.$imageName;
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($sourcePath);
                    $image->resize(1400, 1000, function($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    //Small thumb
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/small/'.$imageName;
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($sourcePath);
                    $image->scale(width:300, height:300);
                    $image->save($destPath);
                }
            }

            $request->session()->flash('success', 'Thêm sản phẩm thành công');

            return response()->json([
                'status' => true,
                'message'=> "Thêm sản phẩm thành công"
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($id, Request $request) {
        $product = Product::find($id);
        if (empty($product)) {
            $request->session()->flash('error', 'Không tìm thấy sản phẩm');

            return redirect()->route('products.index')->with('error','Không tìm thấy sản phẩm');
        }

        //Fetch product image
        $productImages = ProductImage::where('product_id',$product->id)->get();

        $categories = Category::orderBy('name', 'ASC')->get();
        $subCategories = SubCategory::where('category_id', $product->category_id)->get();

        //Fetch related products
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->with('product_images')->get();
        }

        // $records = ImportItem::orderBy('product_name', 'ASC')->get();
        $records = ImportItem::select('product_name', 'barcode')->orderBy('product_name', 'ASC')->distinct()->get();

        $data = [];
        // $data['records'] = $records;
        $data['categories'] = $categories;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImages'] = $productImages;
        $data['relatedProducts'] = $relatedProducts;
        $data['records'] = $records;

        return view('admin.products.edit', $data);
    }

    public function update($id, Request $request) {
        $product = Product::find($id);
        $rules = [
            'product_code' => 'required',
            'barcode' => 'required|unique:products,barcode,'.$product->id.',id',
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'unit' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == "Yes") {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->unit = $request->unit;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();



            $request->session()->flash('success', 'Cập nhật sản phẩm thành công');

            return response()->json([
                'status' => true,
                'message'=> "Cập nhật sản phẩm thành công"
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function delete($id ,Request $request) {
        $product = Product::find($id);
        if (empty($product)) {
            $request->session()->flash('error', 'Không tìm thấy sản phẩm');
            return response()->json([
                'status' => false,
                'notFound' => 'Image saved succesfully'
            ]);
        }

        $productImage = ProductImage::where('product_id',$id)->get();

        if (!empty($productImage)) {
            foreach($productImage as $image) {
                File::delete(public_path('uploads/product/large'.$image->image));
                File::delete(public_path('uploads/product/small'.$image->image));
            }

            ProductImage::where('product_id',$id)->delete();
        }

        $product->delete();
        $request->session()->flash('success', 'Xóa sản phẩm thành công');
        return response()->json([
            'status' => true,
            'notFound' => 'Xóa sản phẩm thành công'
        ]);
    }

    public function getProducts(Request $request) {
        $tempProduct = [];
        if ($request->term != "") {
            $products = Product::where('title', 'like','%'.$request->term.'%')->get();

            if ($products != null) {
                foreach ($products as $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }

        return response()->json([
            'tags' => $tempProduct,
            'status' => true
        ]);
    }

    public function productRatings(Request $request){
        $ratings = ProductRating::select('product_ratings.*','products.title as productTitle', 'products.slug as productSlug')->orderBy('product_ratings.created_at', 'DESC');
        $ratings = $ratings->leftjoin('products', 'products.id', 'product_ratings.product_id');

        if ($request->get('keyword') != "") {
            $ratings = $ratings->orWhere('products.title','like','%'.$request->keyword.'%');
            $ratings = $ratings->orWhere('product_ratings.username','like','%'.$request->keyword.'%');
        }

        $ratings = $ratings->paginate(10);
        return view('admin.products.ratings',[
            'ratings' => $ratings,
        ]);
    }

    public function changeRatingStatus(Request $request) {
        $productRating = ProductRating::find($request->id);
        $productRating->status = $request->status;
        $productRating->save();

        session()->flash('success', 'Thay đổi trạng thái thành công');
        return response()->json([
            'status' => true
        ]);
    }

}
