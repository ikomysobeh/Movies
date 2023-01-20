<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class VideoController extends Controller
{
    public function Add(Request $request)
    {
        try {
            $data = $request->only( 'path', 'resolution','movieId');
            $request->validate([
                'path' => ['file', 'mimes:mp4', 'required'],
                'resolution' => ['string'],
                'movieId' => ['string', 'required'],

            ], $data);
            $data['extension']=$request->file('path')->clientExtension();
            $data['uniqueId'] = $data['movieId'];
           $data['path']=(new VideoService())->saveFile("path", "/videos", $request->allFiles());
            if (!(new VideoService())->save($data)
            ) {
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
            $data = $request->only('movieId', 'path', 'resolution');
            $request->validate([
                'movieId' => ['string', 'required'],
                'path' => ['file', 'mimes:mp4','required'],
                'resolution' => ['string'],
            ], $data);
                $data['extension']=$request->file('path')->clientExtension();
                $video = (new VideoService())->getFirst(['movieId' => $data['movieId']]);
                if (Storage::disk('public')->exists($video->path) &&
                    Storage::disk('public')->delete($video->path)){
                    (new VideoService())->delete(['movieId' => $data['movieId']]);
                }else{
                    throw new NotFound('video not found');
                }
                if(!(new VideoService())->saveFile("path", "/videos", $request->allFiles())){
                    throw new \Exception('failed to save video');
                }

            if (!(new VideoService())->update($data, ['movieId' => $data['movieId']],[])) {
                throw new \Exception('failed to edit');
            }
            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
//                'new movieId' => $data['movieId'],

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
            $data = $request->only('movieId');
            $request->validate([
                'movieId' => ['string', 'required'],
            ], $data);

              $video = (new VideoService())->getFirst(['movieId' => $data['movieId']]);


            return response()->json([
                'success' => 1,
                'video' => $video,
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
            $data = $request->only('movieId');
            $request->validate([
                'movieId' => ['string', 'required'],
            ], $data);
            $video = (new VideoService())->getFirst(['movieId' => $data['movieId']]);
            if (Storage::disk('public')->exists($video->path) &&
                Storage::disk('public')->delete($video->path)){
               (new VideoService())->delete(['movieId' => $data['movieId']]);
        }else{
                throw new NotFound('video not found');
            }
            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
            ]);
        }catch ( \Exception $exception){
            $exception->getMessage();
        }
    }
}
