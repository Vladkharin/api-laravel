<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileAccess;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileAccessConroller extends Controller
{
    public function showMyAccess()
    {

        $data = [];
        $relations = FileAccess::query()->where('user_id', Auth::id())->get();

        foreach ($relations as $relation) {
            $data [] = [
                "file_id" => $relation->file->file_id,
                "name" => $relation->file->name,
                "url" => route('download', $relation->file),
            ];
        }

        return $data;
    }

    public function addAccess(Request $request, File $file)
    {
        $user = Auth::user()->where('email', $request->email)->first();

        if ($user) {
            $exist = FileAccess::query()
                ->where('user_id', $user->id)
                ->where('file_id', $file->id)->exists();

            if (!$exist && $user->id != $file->user_id) {
                FileAccess::query()->create([
                    'user_id' => $user->id,
                    'file_id' => $file->id
                ]);
            }
        }

        return response()->json($file->getAccessArray(), 201);
    }

    public function deleteAccess(Request $request, File $file)
    {

        $user = User::query()->where('email', $request->email)->first();
        if (Auth::id() == $user->id) {
            throw new AuthenticationException();
        }

        if ($user) {

            $exist = FileAccess::query()->where('file_id', $file->id)->where('user_id', $user->id)->exists();

            if (!$exist) {
                throw new ModelNotFoundException();
            }


            FileAccess::query()
                ->where('user_id', $user->id)
                ->where('file_id', $file->id)->delete();

        }

        return response()->json($file->getFileAccesses(), 204);
    }
}
