<?php 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/run-code', function (Request $request) {

    $code = $request->input('code');
    $lang = $request->input('language');

    $dir = storage_path("app/code");
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    $filename = "code_" . time();

    if ($lang == "c") {
        $file = "$dir/$filename.c";
        file_put_contents($file, $code);

        $exe = "$dir/$filename.exe";

        $compile = shell_exec("gcc \"$file\" -o \"$exe\" 2>&1");
        $output = shell_exec("\"$exe\" 2>&1");

    } else {
        $file = "$dir/$filename.cpp";
        file_put_contents($file, $code);

        $exe = "$dir/$filename.exe";

        $compile = shell_exec("g++ \"$file\" -o \"$exe\" 2>&1");
        $output = shell_exec("\"$exe\" 2>&1");
    }

    return response()->json([
        "compile" => $compile,
        "output" => $output
    ]);
});