<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use DataTables;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = Category::select('id','name','type');
        if($request->ajax()) {
            return DataTables::of($categories)->addColumn('action',function($row){
                return '<a href="javascript:void(0)" class="btn btn-success editButton" data-id="'.$row->id.'">Edit</a>
                <a href="javascript:void(0)" class="btn btn-danger deleteButton" data-id="'.$row->id.'">Delete</a>';
            })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function create() {
        return view('categories.create');
    }
    public function store(Request $request)
    {
        if ($request->category_id) {
            $category = Category::find($request->category_id);

            if (! $category) {
                return response()->json(['error' => 'Category not found'
                ]);
            }
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ]);
            $category->update([
                'name' => $request->name,
                'type' => $request->type,
            ]);

            return response()->json(['success' => 'Category Updated Successfully'
            ]);

        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ]);

            Category::create([
                'name' => $request->name,
                'type' => $request->type
            ]);

            return response()->json([
                'success' => 'Category Saved Successfully'
            ]);
        }
    }
    public function edit($id){
        $category = category::findOrFail($id);
        if(! $category){
            abort(404);
        }

        $types = Category
           ::all()
            ->pluck('type','id')
            ->unique()
            ->toArray();

        $category->types = $types;
        return $category;

    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => 'Category Deleted Successfully'
        ]);
    }

}
