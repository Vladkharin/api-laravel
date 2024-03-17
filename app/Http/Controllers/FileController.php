<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $data = [];
        $files = $request->file('files');

        $extensions = ['doc', 'pdf', 'docx', 'zip', 'jpeg', 'jpg', 'png'];

        foreach ($files as $file) {

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileId = substr(uniqid(), 2, 13);
            if ($file->getSize() > 2 * 1024 * 1024) {
                $data [] = [
                    "success" => false,
                    "message" => "File not loaded",
                    "name" => $originalName
                ];

                continue;
            }

            if (!in_array($extension, $extensions)) {
                $data [] = [
                    "success" => false,
                    "message" => "File not loaded",
                    "name" => $originalName
                ];

                continue;
            }

            $notOrigName = $originalName;

            $folder = 'uploads';

            $fileName = $fileId . '.' . $extension;

            $file->storeAs('public/' . $folder, $fileName);


            $exist = File::query()->where('name', $notOrigName)->exists();
            $number = 1;
            while ($exist) {
                $array = explode('.', $originalName);
                $notOrigName = $array[0] . '(' . $number . ').' . $extension;

                $number++;
                $exist = File::query()->where('name', $notOrigName)->exists();

            }


            $fileModel = Auth::user()->files()->create([
                'file_id' => $fileId,
                'path' => $folder . '/' . $fileName,
                'name' => $notOrigName
            ]);

            $data [] = [
                "success" => true,
                "message" => "Success",
                "name" => $notOrigName,
                "url" => $fileModel->url,
                "file_id" => $fileModel->file_id
            ];
        }

        return response()->json($data, 201);
    }

    public function download(File $file)
    {
        return response()->download(public_path() . '/storage/' . $file->path, $file->name);
    }

    public function edit(Request $request, File $file)
    {
        $request->validate([
            'name' => [
                'required',
                function (string $attribute, mixed $value, $fail) use ($file): void {
                    if (File::query()
                        ->where('name', $value)
                        ->where('user_id', Auth::id())
                        ->where('id', '!=', $file->id)
                        ->exists()) {
                        $fail('Введите уникальное имя');
                    }
                },
            ]
        ]);

        $file->name = $request->name;

        $file->save();
        return response()->json(
            [
                "success" => true,
                "message" => "Renamed",
            ]
        );
    }

    public function delete(File $file)
    {
        $file->delete();

        return response()->json(
            [
                "success" => true,
                "message" => "File already deleted",
            ], 200
        );
    }




    public function showMyFiles()
    {

        $data = [];
        $files = Auth::user()->files;

        foreach ($files as $file) {
            $data [] = [
                "file_id" => $file->file_id,
                "name" => $file->name,
                "url" => route('download', $file),
                'access' => $file->getAccessArray()
            ];
        }


        return $data;
    }


}
