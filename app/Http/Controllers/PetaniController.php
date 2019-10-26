<?php

namespace App\Http\Controllers;

use App\Petani;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PetaniController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255|unique:petanis',
            'username' => 'required|string|max:255|unique:petanis',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required',
            'address' => 'required',
            'age' => 'required',
        ]);

        $error = "true";
        $message = "";
        $data = null;
        $code = 400;
        if($validator->fails()){
            $errors = $validator->errors();
            $message = $errors;
        }
        else {
            $user = Petani::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'age' => $request->age,
                'ktp_photo' => $request->ktp_photo,
                'with_ktp_photo' => $request->with_ktp_photo
            ]);
            if($user){
                Auth::login($user);
                $user->generateToken();
                $error = "false";
                $message = "Pendaftaran Berhasil";
                $code = 201;
            }
            else{
                return response()->json([
                    'error' => 'true',
                    'message' => $message,
                ], 401);
            }
        }

        return response()->json([
            'error' => $error,
            'message' => $message,
            'created_at' => Carbon::now()->toDateString(),
            'updated_at' => "-"
        ], $code);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = Petani::where('username', '=', $request->username)->firstOrFail();
        $error = 'true';
        $data = null;

        if($user) {
            if (Hash::check($request->password, $user->password)) {
                $user->generateToken();
                $error = 'false';
                $data = $user;
            }
            else {
                return response()->json([
                    'error' => 'true',
                    'message' => 'Gagal Login, Credentials not valid'
                ], 401);
            }
        }
        else {
            return response()->json([
                'error' => 'true',
                'message' => 'Gagal Login, Credentials not valid'
            ], 401);
        }

        return response()->json([
            'error' => $error,
            'profile' => [
                'idUser' => $data->id,
                'nama' => $data->name,
                'username' => $data->username,
                'no telp' => $data->phone,
                'lokasi pertanian' => $data->address,
                'usia' => $data->age,
                'foto profile' => $data->avatar
            ],
        ], 200);
    }

    public function updateAvatar(Request $request, $id)
    {
        $avatar = $request->file($request->avatar);
        $avatarImage = $avatar->getFilename(). '.' .$avatar->getClientOriginalExtension();
        Storage::disk('foto_profil')->put($avatarImage, File::get($avatar));

        $data = Petani::where('id', $id)->update([
            'avatar' => $avatarImage
        ]);

        return response()->json([
            'error' => 'false',
            'message' => 'Berhasil diperbarui',
            'data' => $data
        ], 200);
    }
}
