<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class CategoriesController extends Controller
{
    public function Add(Request $request)
    {
        try {
            $data = $request->only('name', 'description');
            $request->validate([
                'name' => ['string', 'required'],
                'description' => ['string', 'required'],
            ], $data);
//            if (!(new CategoryService())->save($data)) {
//                throw new \Exception('failed to save');
//            }
            $category=new Category($data);
            $category->save();

            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
                'categoryId'=>$category->id,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }

    }

    public function edit(Request $request)
    {
        try {
            $data = $request->only('id', 'name', 'description');
            $request->validate([
                'id' => ['required', 'numeric'],
                'name' => ['string'],
                'description' => ['string'],
            ], $data);
            if (!(new CategoryService())->update($data, ['id' => $data['id']])) {
                throw new \Exception('failed to edit');
            }
            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }
    }

    public function view(Request $request,$id=null)
    {
        try {
            $data = $request->only( 'name', 'search');
            $request->validate([
                'name' => ['string'],
                'search' => ['string'],
            ], $data);
            $Category = (new CategoryService())->getListQuery();
            if ($id!=null) {
              $Category = $Category->where(['id' , $id]);
            }
            if ($request->has('name')) {
                $Category = $Category->where(['name' , $data['name']]);
            }
            if ($request->has('search')) {
                $Category = (new CategoryService())->getListQuery(['keyword' => $data['search']]);
            }
            return response()->json([
                'success' => 1,
                'categories' => $Category->get(),
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = $request->only('id');
            $request->validate([
                'id' => ['numeric', 'required'],
            ], $data);

           if(!(new CategoryService())->delete(['id' => $data['id']])){
               throw new \Exception('the category not deleted');
           }

            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
            ]);
        } catch (\Exception $exception) {
            $exception->getMessage();
        }
    }


}
