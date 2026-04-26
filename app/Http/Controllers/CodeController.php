<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function run(Request $request)
    {
        $code = $request->code;
        $lang = $request->language;

        // temp file location (safe)
        $filePath = storage_path("app/code");
        $outputPath = storage_path("app/output");

        if ($lang == "c") {
            $filePath .= ".c";

            file_put_contents($filePath, $code);

            $exePath = $outputPath . ".exe";

            // compile
            $compile = shell_exec("gcc \"$filePath\" -o \"$exePath\" 2>&1");

            // run
            $output = shell_exec("\"$exePath\" 2>&1");

        } else {
            $filePath .= ".cpp";

            file_put_contents($filePath, $code);

            $exePath = $outputPath . ".exe";

            // compile
            $compile = shell_exec("g++ \"$filePath\" -o \"$exePath\" 2>&1");

            // run
            $output = shell_exec("\"$exePath\" 2>&1");
        }

        return response()->json([
            "compile" => $compile,
            "output" => $output
        ]);
    }
}