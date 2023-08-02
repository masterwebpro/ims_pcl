<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category_list = Category::where('parent_id',0)
                            ->with('brand')
                            ->orderBy('sort_by','ASC')
                            ->get();
        $category_list  =  $this->parent($category_list,$request);
        // echo "<pre>";
        // print_r($category_list);
        // echo "</pre>";
        // die();
        $category = $this->category_list($category_list);
        $brand = Brand::all();
        return view('maintenance/category/index',['category_list' => $category_list, 'category' => $category, 'brand_list' => $brand]);
    }

    public function category_list($category_list){
        $ul = '<ul class="list-group">';
        foreach($category_list as $key => $category)
        {
            $ul .= '<li class="list-group-item" data-id="'.$key.'">';
            $ul .= '<div class="float-end">';
            $ul .= '<a href="'.URL::to('maintenance/category/'. _encode($category->category_id)).'" data-id="'.$category->category_id.'" class="mx-5"><i class="ri-eye-fill align-bottom me-1"></i> view</a>';
            $ul .= '<a href="'.URL::to('maintenance/category/'._encode($category->category_id)).'/edit" data-id="'.$category->category_id.'" class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> edit </a>';
            $ul .= '</div>';
            $ul .= '<a data-bs-toggle="collapse" href="#collapseExample'.$key.'" role="button" aria-expanded="true" aria-controls="collapseExample'.$key.'">';
            $ul .= '    <span class="file-list-link fs-4"><i class="ri-folder-fill align-middle me-2 text-warning"></i> '. $category->category_name .'</span>';
            if($category->brand)
            foreach($category->brand as $brand){
                $ul .= '<span class="badge badge-label bg-primary"><i class="mdi mdi-circle-medium"></i> '.$brand->brand_name.'</span>';
            }
            $ul .= '</a>';
            if(!empty($category->child))
            {
                $ul .= $this->addChild($category->child, $key);
            }
            $ul .= '</li>';
        }
        $ul .= '</ul>';
        return $ul;
    }

    public function addChild($child, $key){
        $ul = '<div class="collapse show" id="collapseExample'.$key.'">';
        $ul .= '    <ul class="sub-menu list-group">';
        foreach($child as $k => $ch){
            $ul .= '        <li class="list-group-item" data-id="'.$key.'">';
            $ul .= '<div class="float-end">';
            $ul .= '<a href="'.URL::to('maintenance/category/'. _encode($ch['category_id'])).'" data-id="'.$ch['category_id'].'" class="mx-5"><i class="ri-eye-fill align-bottom me-1"></i> view</a>';
            $ul .= '<a href="'.URL::to('maintenance/category/'._encode($ch['category_id'])).'/edit" data-id="'.$ch['category_id'].'" class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> edit </a>';
            $ul .= '</div>';
            $ul .= '            <a data-bs-toggle="collapse" href="#collapseExample'.$k.$key.'" role="button"';
            $ul .= '                aria-expanded="true" aria-controls="collapse'.$k.$key.'"';
            $ul .= '                href="#!"><i class="ri-menu-3-line align-middle me-2"></i> '.$ch['category_name'];
            if($ch['brand'])
            foreach($ch['brand'] as $brand){
                $ul .= '<span class="badge badge-label bg-success"><i class="mdi mdi-circle-medium"></i> '.$brand['brand_name'].'</span>';
            }
            $ul .= '</a>';

            if(!empty($ch['child']))
            {
                $ul .= $this->addChild($ch['child'], $k.$key);
            }
            $ul .= '        </li>';
        }
        $ul .= '    </ul>';
        $ul .= '</div>';
        return $ul;
    }


    private function parent($data,$request){
        foreach($data as &$res)
        {
            $res['category_name'] = $res['category_name'];
            $res['child'] = $this->children($res['category_id'],$request);
        }
        return $data;
    }

    private function children($parent_id,$request)
    {
        $child = Category::where('parent_id',$parent_id)
                ->with('brand')
                ->orderBy('sort_by','ASC')
                ->where('is_enabled',1);
        $child  = $child->get()->toArray();
        if(count($child) > 0)
        {
            foreach($child as &$res)
            {
                $res['category_name'] = $res['category_name'];
                $res['child'] = $this->children($res['category_id'],$request);
            }
            return $child;
        }
        else{
            return [];
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category_list = Category::all();
        $brand = Brand::all();
        return view('maintenance/category/create',['category_list' => $category_list, 'brand_list' => $brand]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        DB::connection()->beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'parent_id' => 'required',
            'sort_by' => 'required',
        ],[
            'category_name' => "Category name is required",
            'parent_id' => "Parent name is required",
            'sort_by' => "Sort by is required"
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->toArray() as $error){
                return response()->json(["status" => false, "message" => $error[0]],200);
            }
        }

        $sub_categories = json_decode($request->sub_categories);
        $brand_id = json_decode($request->brand_id);

        try {
            $category = Category::updateOrCreate(['category_id' => $request->id], [
                'category_name' => $request->category_name,
                'parent_id' => $request->parent_id,
                'sort_by' => $request->sort_by,
                'is_enabled' => $request->is_enabled,
            ]);

            if(!empty($sub_categories)){
                foreach($sub_categories as $key => $sub_category)
                {
                    Category::updateOrCreate(['category_id' => $sub_category->category_id], [
                        'category_name' => $sub_category->category_name,
                        'parent_id' => $category->category_id,
                        'sort_by' => $key + 1,
                        'is_enabled' => $request->is_enabled,
                    ]);
                }
            }
            else{
                Category::where('parent_id',$category->category_id)->delete();
            }

            if(!empty($brand_id)){
                foreach($brand_id as $key => $brand_id)
                {
                    CategoryBrand::updateOrCreate([
                            'category_id' => $category->category_id,
                            'brand_id' => $brand_id,
                        ], [
                        'category_id' => $category->category_id,
                        'brand_id' => $brand_id,
                        'is_enabled' => 1,
                    ]);
                }
            }
            else{
                CategoryBrand::where('category_id',$category->category_id)->delete();
            }

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $category,
                'id'=> _encode($category->category_id)
            ]);

        } catch (\Throwable $th) {
            DB::connection()->rollBack();
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find(_decode($id));
        $category_list = Category::all();
        $sub_category = Category::where('parent_id',$category->category_id)
                            ->orderBy('sort_by','ASC')
                            ->get();
        $brand_cat = CategoryBrand::select('brand_id')->where('category_id',$category->category_id)->get();
        $brand_id = $brand_cat->pluck('brand_id');
        $brand = Brand::whereIn('brand_id',$brand_id)->get();
        return view('maintenance/category/view', [
            'category'=>$category,
            'category_list'=>$category_list,
            'sub_category'=>$sub_category,
            'category_brand'=> $brand,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find(_decode($id));
        $category_list = Category::all();
        $sub_category = Category::where('parent_id',$category->category_id)
                            ->orderBy('sort_by','ASC')
                            ->get();
        $brand_cat = CategoryBrand::select('brand_id')->where('category_id',$category->category_id)->get();
        $brand_id = $brand_cat->pluck('brand_id')->toArray();
        $brand = Brand::all();
        return view('maintenance/category/edit', [
            'category'=>$category,
            'category_list'=>$category_list,
            'sub_category'=>$sub_category,
            'brand_list'=> $brand,
            'category_brand'=>$brand_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
