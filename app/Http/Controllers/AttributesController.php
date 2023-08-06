<?php

namespace App\Http\Controllers;

use App\Models\Attributes;
use App\Http\Controllers\Controller;
use App\Models\AttributeEntity;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttributesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $attributes_list = Attributes::select('attributes.*','u.name as updated_by')
            ->leftJoin('users as u','u.id','attributes.created_by')
            ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->orWhere('attributes.attribute_name','like', '%'.$s.'%');
                    $query->orWhere('attributes.attribute_code','like', '%'.$s.'%');
                    $query->orWhere('attributes.attribute_display_name','like', '%'.$s.'%');
                    $query->get();
                }
            }]
        ])
        ->where([
            [function ($query) use ($request) {
                if ($request->filter_date) {
                    if($request->filter_date == 'created_at' && $request->date ) {
                        $query->whereBetween('attributes.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                    }
                }

                $query->get();
            }]
        ])
        ->orderByDesc('products.created_at')
        ->paginate(20);
        return view('maintenance/attributes/index', ['attributes_list' => $attributes_list]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::where('is_enabled',1)->get();
        $type = array(
                    array(
                        'datatype' => 'text_field',
                        'label'  =>'Text Field'
                    ),
                    array(
                        'datatype' => 'dropdown',
                        'label' => 'Dropdown',
                    ),
                    array(
                        'datatype' => 'select',
                        'label' => 'Multi Select',
                    ),
                    array(
                        'datatype' => 'textarea',
                        'label' => 'TextArea',
                    ),
                    array(
                        'datatype' => 'date',
                        'label' => 'Date'
                    ),
                    array(
                        'datatype' => 'checkbox',
                        'label' => 'Yes/No'
                    ),
                    array(
                        'datatype' => 'price',
                        'label' => 'Price'
                    ),
                    // array(
                    //     'datatype' => 'image',
                    //     'label' => 'Media Image'
                    // )
                );
        return view('maintenance/attributes/create',['category' => $category, 'input_type' => $type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::connection()->beginTransaction();
        $validator = Validator::make($request->all(), [
            'attribute_code' => 'required',
            'attribute_name' => 'required',
            'attribute_input_type' => 'required',
            'attribute_display_name' => 'required',
        ],[
            'attribute_code' => "Attribute code is required",
            'attribute_name' => "Attribute name is required",
            'attribute_input_type' => "Attribute type is required",
            'attribute_display_name' => "Label is required"
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->toArray() as $error){
                return response()->json(["status" => false, "message" => $error[0]],200);
            }
        }
        try {
            $attribute = Attributes::updateOrCreate(['attribute_id' => $request->id], [
                'attribute_code' => $request->attribute_code,
                'attribute_name' => $request->attribute_name,
                'attribute_input_type' => $request->attribute_input_type,
                'attribute_display_name' => $request->attribute_display_name,
                'is_enabled' => $request->is_enabled,
                'is_required' => $request->is_required,
                'created_by' => Auth::user()->id,
            ]);

            $entity_data =  json_decode($request->entity_data);
            foreach($entity_data as $entity){
                  AttributeEntity::updateOrCreate(['attribute_entity_id ' => $entity->attribute_entity_id], [
                  'attribute_id' => $attribute->attribute_id,
                  'attribute_entity_value' => $entity->attribute_entity_value,
                  'attribute_entity_name' => $entity->attribute_entity_name,
                  'attribute_entity_description' => $entity->attribute_entity_description,
                  'attribute_entity_position' => $entity->attribute_entity_position,
                  'is_default' => $entity->is_default,
                  'created_by' => Auth::user()->id,
              ]);
            }

            DB::connection()->commit();
            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $attribute,
                'id'=> _encode($attribute->attribute_id)
            ]);

        }catch(\Throwable $th){
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
     * @param  \App\Models\Attributes  $attributes
     * @return \Illuminate\Http\Response
     */
    public function show(Attributes $attributes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attributes  $attributes
     * @return \Illuminate\Http\Response
     */
    public function edit(Attributes $attributes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attributes  $attributes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attributes $attributes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attributes  $attributes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attributes $attributes)
    {
        //
    }
}
