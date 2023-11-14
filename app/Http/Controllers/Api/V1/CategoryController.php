<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\V1\Category;
use App\Http\Requests\Api\V1\CreateCategoryRequest;
use App\Http\Requests\Api\V1\UpdateCategoryRequest;
use Illuminate\Support\Facades\Gate;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories=Category::all();
        return response()->json([
            'status'=>1,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request)
    {
        try{
            Gate::authorize('isSuperAdmin');
            $data= array_merge($request->all(),['status' => 'active']);
            $category=Category::create($data);
            return $category;
            return response()->json([
                'status' => 1,
                'message' =>'Create Category Successfully',
                'category' =>$category,
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category=Category::findOrfail($id);
        return response()->json([
            'status' =>1,
            'category'=>$category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request,$id)
    {
      try{
        Gate::authorize('isSuperAdmin');
        $category=Category::findOrfail($id);
        $data=$request->all();
        $category->update($data);
        return response()->json([
            'status'=>1,
            'message'=>'Update Category Successfully'
        ]);
      }catch(\Exception $e){
        return response()->json([
            'status'=>0,
        ]);
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
      try{
        Gate::authorize('isSuperAdmin');
        $category=Category::findOrfail($id);
        $category->delete();
        return response()->json([
            'status' =>1,
            'message' =>'delete Category Successfully',
        ]);
      }catch(\Exception $e){
        return response()->json([
            'status'=>0,
        ]);
      }
    }
}
