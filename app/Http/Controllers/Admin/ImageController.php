<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($file_path)
    {
        $file_path = str_replace("-","/",$file_path);
        $local_path = storage_path('app') . DIRECTORY_SEPARATOR . $file_path;
        return response()->file($local_path);
    }
}
