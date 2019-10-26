<?php

namespace App\Http\Controllers;

use App\DevicePh;
use Illuminate\Http\Request;

class DevicePhController extends Controller
{
    public function getData()
    {
        $data = DevicePh::all();
        $json = [
            'error' => 'false',
            'message' => 'Berhasil Ambil Data',
            'listDeviceIot' => [
                'id' => $data[0]->id
            ]
        ];
        return response()->json($json, 200);
    }
}
