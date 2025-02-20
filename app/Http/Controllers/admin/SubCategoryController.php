<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request) {
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                            ->latest('sub_categories.id')
                            ->leftJoin('categories', 'categories.id','sub_categories.category_id');

        if(!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name', 'like', '%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orWhere('categories.name', 'like', '%'.$request->get('keyword').'%');
        }

        $subCategories = $subCategories->paginate(10);
        return view('admin.sub_category.list', compact('subCategories'));
    }
    public function create() {
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        return view('admin.sub_category.create', $data);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success', 'Thêm danh mục con thành công');
            return response()->json ([
                'status' => true,
                'message' => "Thêm danh mục con thành công!",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request) {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            $request->session()->flash('error', 'Không tìm thấy bản ghi');
            return redirect()->route('sub-categories.index');
        }

        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;
        return view('admin.sub_category.edit', $data);
    }

    public function update($id, Request $request) {
        $subCategory = SubCategory::find($id);
        if (empty($subCategory)) {
            $request->session()->flash('error', 'Không tìm thấy bản ghi');
            //return redirect()->route('sub-categories.index');
            return response()->json([
                "status" => false,
                'notFound' => true,
                'message' => 'Không tìm thấy danh mục'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$subCategory->id.',id',
            'category' => 'required'
        ]);

        if ($validator->passes()) {
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success', 'Cập nhật danh mục thành công');
            return response()->json ([
                'status' => true,
                'message' => "Cập nhật danh mục thành công!",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function delete($id, Request $request) {
        $subCategory = SubCategory::find($id);
        if (empty($subCategory)) {
            $request->session()->flash('error', 'Không tìm thấy danh mục');
            return response()->json([
                'status' => true,
                'message' => 'Không tìm thấy danh mục'
            ]);
        }

        $subCategory->delete();

        $request->session()->flash('success', 'Xóa danh mục thành công');

        return response()->json([
            'status' => true,
            'message' => 'Xóa danh mục thành công'
        ]);
    }
}
