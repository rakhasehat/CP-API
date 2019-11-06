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
            'listDeviceIot' => $data
        ];
        return response()->json($json, 200);
    }
}
