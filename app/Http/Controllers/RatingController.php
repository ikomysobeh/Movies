<?php

namespace App\Http\Controllers;

use App\Services\RatingService;
use Illuminate\Http\Request;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class RatingController extends Controller
{
    public function Add(Request $request)
    {
        try {
            $data = $request->only('userId', 'movieId', 'score');
            $request->validate([
                'userId' => ['string', 'required'],
                'movieId' => ['string', 'required'],
                'score' => ['numeric'],
            ], $data);
            if (!(new RatingService())->save($data)) {
                throw new \Exception('failed to save');
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

    public function edit(Request $request)
    {
        try {
            $data = $request->only('userId', 'movieId', 'score','id');
            $request->validate([
                'userId' => ['string','required'],
                'movieId' => ['string','required'],
                'score' => ['string'],
                'id' => ['numeric'],
            ], $data);
            if (!(new RatingService())->update($data, ['id' => $data['id']])) {
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

    public function view(Request $request)
    {
        try {
            $data = $request->only('id');
            $request->validate([
                'id' => ['numeric', 'required'],
            ], $data);
            if ($data != null) {
                $Rating= (new RatingService())->getFirst(['id' => $data['id']]);
            } else {
                throw new NotFound('thar is no rating');
            }
            return response()->json([
                'success' => 1,
                'Rating' => $Rating->get(),
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }
    }
    public function delete(Request $request){
        try {
            $data = $request->only('id');
            $request->validate([
                'id' => ['numeric', 'required'],
            ], $data);
            if ($data != null) {
                $Rating = (new RatingService())->delete(['id' => $data['id']]);
            } else {
                throw new NotFound('thar is no rating');
            }
            return response()->json([
                'success' => 1,
                'rating' => 'successfully',
            ]);
        }catch ( \Exception $exception){
            return response()->json([
                'success'=>0,
                'msg'=>$exception->getMessage()
            ]);
        }
    }
}
