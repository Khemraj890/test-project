<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth;
use Config;
use KSolutions\VideoToThumb\VideoToThumb;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class WebController extends Controller
{
    public function index(){
        $roles = Role::all();
        return view('users.index', compact('roles'));
    }
    public function getUsers(){
        $users = User::with('role')->paginate();
        return response()->json($users);
    }
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[6-9]\d{9}$/',
            'description' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'old' => $request->except('profile_image')
            ], 422);
        }

        $user = new User();
        $user->fill($request->all());
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = 'storage/app/public/'.$path;
        }
        $user->save();

        return response()->json(['user' => $user], 201);
    }

    
    // public function uploadVideo(Request $request)
    // {
    //     try {
    //         if (!$request->hasFile('video')) {
    //             return response()->json(['status' => 'failed', 'message' => 'No video file uploaded']);
    //         }
    //         $file = $request->file('video');
    //         $time = time();
    //         $videoPath = $file->storeAs('public/videos', $time.'-video.mp4');
    //         $videoFullPath = storage_path('app/' . $videoPath);
    //         $thumbnailPath = storage_path('app/public/thumbnails/'.$time.'-thumbnail.jpg');
    //         $videoToThumb = new VideoToThumb();
    //         $result = $videoToThumb->generateVideoThumbnail($videoFullPath, $thumbnailPath, 5);
    //         if ($result) {
    //             return response()->json(['status' => 'success', 'message' => 'Thumbnail generated successfully', 'thumbnail' => $thumbnailPath]);
    //         } else {
    //             return response()->json(['status' => 'failed', 'message' => 'Thumbnail generation failed']);
    //         }
    //     } catch (Exception $e) {
    //         // Catch and display the exception message
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    //     }
    // }
}
