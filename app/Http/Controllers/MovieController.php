<?php

namespace App\Http\Controllers;

use App\Services\MoviesService;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class MovieController extends Controller
{
    public function Add(Request $request)
    {
        try {
            $data1 = $request->only('description', 'CategoryId', 'title');
            $data = $request->only('path', 'resolution');
            $request->validate([
                'CategoryId' => ['numeric', 'required'],
                'description' => ['string', 'required'],
                'path' => ['file', 'mimes:mp4', 'required'],
                'resolution' => ['string', 'required'],
                'title' => ['string', 'required'],
            ], [...$data, ...$data1]);
            $data1['uniqueId'] = Str::random(32);
            $data['extension'] = $request->file('path')->clientExtension();
            $data['uniqueId'] = Str::random(32);
            $data['movieId'] = $data1['uniqueId'];
            if (!(new MoviesService())->save($data1)) {
                throw new \Exception('failed to move');
            }
            if ($request->has('path')) {
                $data['path'] = (new VideoService())->saveFile("path", "/videos", $request->allFiles());
            }
//            return $data;
            if (!(new VideoService())->save($data)) {
                throw new \Exception('filed to save video');
            }
            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
                'movieId'=>$data1['uniqueId'],
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
            $data = $request->only('description', 'CategoryId', 'uniqueId', 'title');
            $request->validate([
                'CategoryId' => ['numeric'],
                'description' => ['string'],
                'uniqueId' => ['string', 'required'],
                'title' => ['string'],
            ], $data);
            $moves = (new MoviesService())->getListQuery();
            $moveId = $moves->where('uniqueId', $data['uniqueId']);
            if ($moveId == true) {
                if (!(new MoviesService())->update($data, ['uniqueId' => $data['uniqueId']])) {
                    throw new \Exception('failed to 1');
                }
            } else {
                throw new \Exception('failed to uniqueId');
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

    public function view(Request $request, $uniqueId = null)
    {
        try {
            $data = $request->only( 'title', 'search');
            $request->validate([
                'title' => ['string'],
                'search' => ['string'],
            ], $data);
            $move = (new MoviesService())->getListQuery();
            if ($uniqueId != null) {
                $move = $move->where(['uniqueId', $uniqueId]);
            }
            if ($request->has('title')) {
                $move = $move->where(['title', $data['title']]);
            }
            if ($request->has('search')) {
                $move = (new MoviesService())->getListQuery(['keyword' => $data['search']]);
            }
            return response()->json([
                'success' => 1,
                'movies' => $move->get(),
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
            $data = $request->only('uniqueId');
            $request->validate([
                'uniqueId' => ['string', 'required'],
            ], $data);

            $moves = (new MoviesService())->getListQuery();
            $video = (new VideoService())->getFirst(['movieId' => $data['movieId']]);
            if (Storage::disk('public')->exists($video->path) &&
                Storage::disk('public')->delete($video->path)){
                (new VideoService())->delete(['movieId' => $data['movieId']]);
            }else {
                throw new NotFound('video not found');
            }
                if ($data != null) {
                    $move = (new MoviesService())->delete(['uniqueId' => $data['uniqueId']]);
                } else {
                    throw new NotFound('Movies not found');
                }
            return response()->json([
                'success' => 1,
                'move' =>'successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }
    }

}
