<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Program;


class ProjectController extends Controller
{
    private function safeExec($cmd)
{
    $cmd = "C:\\Windows\\System32\\cmd.exe /c " . $cmd . " 2>&1";

    $output = shell_exec($cmd);

    if ($output === null) {
        return "❌ Command Failed\nCMD:\n" . $cmd;
    }

    if (trim($output) === "") {
        return "⚠️ Program executed but no output";
    }

    return $output;
}
    // ✅ Saved Programs Dashboard
public function savedPrograms()
    {
        $programs = Project::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('saved-programs',compact('programs'));
    }

    // 📌 Dashboard – only logged-in user's projects
    public function index()
    {
        $projects = Project::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('dashboard', compact('projects'));
    }

    // ➕ Create project (form submit)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'language' => 'nullable|string|max:100',
        ]);

        $project = Project::create([
            'user_id' => auth()->id(), // ✅ unique per user
            ...$validated
        ]);

        return redirect()->route('editor', $project->id);
    }
    public function delete($id){

    $program = Program::find($id);

    if($program){
        $program->delete();
    }

    return response()->json([
        "status"=>"deleted"
    ]);
}
// Download Program
public function download($id)
{
    $project = Project::findOrFail($id);

    $ext = "txt";

    if($project->language == "c") $ext = "c";
    elseif($project->language == "cpp") $ext = "cpp";
    elseif($project->language == "java") $ext = "java";
    elseif($project->language == "python") $ext = "py";
    elseif($project->language == "scala") $ext = "scala";
    elseif($project->language == "ruby") $ext = "rb";
    elseif($project->language == "kotlin") $ext = "kt";

    $filename = $project->name . "." . $ext;

    return response($project->code)
        ->header('Content-Type','text/plain')
        ->header('Content-Disposition',"attachment; filename=$filename");
}
    // ⚡ Create project via AJAX
    public function createViaAjax(Request $request)
{
    $request->validate([
        'name' => 'required',
        'language' => 'required'
    ]);

    // ❌ duplicate check
    $exists = Project::where('user_id', auth()->id())
        ->where('name', $request->name)
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'Project with this name already exists!'
        ]);
    }
    
    

    // 🔥 SAMPLE CODE LOGIC
    $code = '';

    switch ($request->language) {
        case 'c':
            $code = '#include <stdio.h>

int main() {
    printf("Hello World");
    return 0;
}';
            break;

        case 'cpp':
            $code = '#include <iostream>
using namespace std;

int main() {
    cout << "Hello World";
    return 0;
}';
            break;

        case 'java':
            $code = 'public class Main {
    public static void main(String[] args) {
        System.out.println("Hello World");
    }
}';
            break;

        case 'python':
            $code = 'print("Hello World")';
            break;

        case 'scala':
            $code = 'object Main {
    def main(args: Array[String]) = {
        println("Hello World")
    }
}';
            break;

        case 'ruby':
            $code = 'puts "Hello World"';
            break;

        case 'kotlin':
            $code = 'fun main() {
    println("Hello World")
}';
            break;
    }

    // 🔥 SAVE PROJECT WITH CODE
    $project = Project::create([
        'user_id' => auth()->id(),
        'name' => $request->name,
        'language' => $request->language,
        'code' => $code
    ]);

    return response()->json([
        'success' => true,
        'project' => $project
    ]);
}

    // ✏️ Update project
    public function update(Request $request,$id)
{
    $project = Project::find($id);

    $project->name = $request->name;
    $project->language = $request->language;
    $project->code = $request->code;

    $project->save();

    return response()->json([
        "status"=>"saved"
    ]);
}
public function runCode(Request $request)
{
   

    $code = $request->code;
    $lang = $request->language;
    $input = $request->input ?? '';
    $debug = $request->debug ?? false;   // ✅ ADD THIS
    $compile = $request->compile ?? false;
    $tempDir = storage_path('app/code');
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $filename = uniqid();
    $inputFile = "$tempDir\\$filename.txt";
    file_put_contents($inputFile, $input);

    // C
    if ($lang == "c") {

    // ---------------- CLEAN PATH ----------------
    $tempDir = str_replace("\\", "/", $tempDir);

    $file = $tempDir . "/Main.c";
    $exe  = $tempDir . "/Main.exe";

    file_put_contents($file, $code);

    $gcc = "D:\\msys64\\ucrt64\\bin\\gcc.exe";

    // ---------------- COMPILE ----------------
    $compileCmd = "\"$gcc\" \"$file\" -o \"$exe\" 2>&1";
    $compileOutput = shell_exec($compileCmd);

    // ❌ Compile Failed
    if (!file_exists(str_replace("/", "\\", $exe))) {
        return response()->json([
            'output' => "❌ Compile Failed:\n" . trim($compileOutput)
        ]);
    }

    // ✅ Compile Only Mode
    if ($compile) {
        return response()->json([
            'output' => "✅ Compile Successful\n" . trim($compileOutput)
        ]);
    }

    // ---------------- RUN ----------------
    $descriptors = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open("\"$exe\"", $descriptors, $pipes, $tempDir);

    if (!is_resource($process)) {
        return response()->json([
            'output' => "❌ Execution Error"
        ]);
    }

    // ✅ USER INPUT (NO HARDCODE)
    if (!empty($input)) {
        fwrite($pipes[0], $input . "\n");
    }

    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    proc_close($process);

    // ❌ Runtime Error
    if (trim($stderr) !== "") {
        return response()->json([
            'output' => "❌ Runtime Error:\n" . trim($stderr)
        ]);
    }

    // ✅ SUCCESS OUTPUT
    return response()->json([
        'output' => trim($stdout)
    ]);
}
    // C
/* if ($lang == "c") {

    $tempDir = str_replace("\\", "/", $tempDir);

    $file = $tempDir . "/Main.c";
    $exe  = $tempDir . "/Main.exe";

    file_put_contents($file, $code);

    $gcc = "D:\\msys64\\ucrt64\\bin\\gcc.exe";

    // ---------------- COMPILE ----------------
    $compileCmd = "\"$gcc\" \"$file\" -o \"$exe\" 2>&1";

    $compileOutput = shell_exec($compileCmd);

    // ❗ REAL ERROR CHECK
    if (!file_exists($exe)) {
        return response()->json([
            'output' => "❌ Compile Error:\n" . trim($compileOutput)
        ]);
    }

    // ---------------- RUN ----------------
    $descriptors = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open("\"$exe\"", $descriptors, $pipes, $tempDir);

    if (!is_resource($process)) {
        return response()->json([
            'output' => "❌ Execution Error (process failed)"
        ]);
    }

    fwrite($pipes[0], "25\n");
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    $error  = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    proc_close($process);

    return response()->json([
        'output' => trim($output . $error)
    ]);
}*/
    // C++
    // C++
else if ($lang == "cpp") {

    $file = $tempDir . "\\Main.cpp";
    $exe  = $tempDir . "\\Main.exe";

    file_put_contents($file, $code, LOCK_EX);

    if (file_exists($exe)) {
        unlink($exe);
    }

    // ✅ FIXED COMPILER PATH
    $gpp = "D:\\msys64\\ucrt64\\bin\\g++.exe";

    // ---------------- COMPILE ----------------
    $compileCmd = "\"$gpp\" \"$file\" -o \"$exe\" 2>&1";
    $compileOutput = shell_exec($compileCmd);

    // Compile Only Mode
    if ($compile) {

        if (!file_exists($exe)) {
            return response()->json([
                'output' => "❌ Compile Error:\n" . trim($compileOutput)
            ]);
        }

        return response()->json([
            'output' => "✅ Compiled Successfully"
        ]);
    }

    // ---------------- RUN ----------------
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open("\"$exe\"", $descriptorspec, $pipes, $tempDir);

    if (!is_resource($process)) {
        return response()->json([
            'output' => "❌ Execution Error"
        ]);
    }

    // CLEAN INPUT
    $input = trim($input ?? "");

    if ($input !== "") {
        fwrite($pipes[0], $input . PHP_EOL);
    }

    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    $error  = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    proc_close($process);

    if ($debug) {
        $output = "Debug Mode\n\n" . $output;
    }

    return response()->json([
        'output' => trim($output . $error)
    ]);
}
//java
else if ($lang == "java") {

    preg_match('/public class\s+(\w+)/', $code, $matches);
    $className = $matches[1] ?? "Main";

    $file = "$tempDir\\$className.java";
    file_put_contents($file, $code);

    $javaHome = "C:\\Program Files\\Java\\jdk-21.0.10\\bin";
    $javac = "$javaHome\\javac.exe";
    $java  = "$javaHome\\java.exe";

    // STEP 1: COMPILE (NO CMD)
    $compileProcess = proc_open(
        "\"$javac\" \"$file\"",
        [
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ],
        $pipes
    );

    $compileOutput = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($compileProcess);

    $classFile = "$tempDir\\$className.class";

    if (!file_exists($classFile)) {
        return response()->json([
            'output' => "❌ Compile Error:\n" . $compileOutput
        ]);
    }

    // STEP 2: RUN (NO CMD)
    $runProcess = proc_open(
        "\"$java\" -cp \"$tempDir\" $className",
        [
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ],
        $pipes
    );

    $output = stream_get_contents($pipes[1]);
    $error  = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($runProcess);

    return response()->json([
        'output' => trim($output . $error) ?: "⚠️ No Output"
    ]);
}
    // Python
    else if ($lang == "python") {

    // ---------------- SAFE TEMP PATH ----------------
    $tempDir = str_replace("/", "\\", $tempDir);

    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $file = $tempDir . "\\$filename.py";
    file_put_contents($file, $code, LOCK_EX);

    $file = str_replace("/", "\\", $file);

    // ---------------- PYTHON PATH ----------------
    $python = "C:\\Users\\Kuki\\AppData\\Local\\Programs\\Python\\Python312\\python.exe";

    // ---------------- RUN COMMAND ----------------
    $cmd = "\"$python\" \"$file\"";

    $descriptors = [
        0 => ["pipe", "r"], // stdin
        1 => ["pipe", "w"], // stdout
        2 => ["pipe", "w"]  // stderr
    ];

    $process = proc_open($cmd, $descriptors, $pipes, $tempDir);

    if (!is_resource($process)) {
        return response()->json([
            'output' => "❌ Execution Error"
        ]);
    }

    // ---------------- INPUT FIX (IMPORTANT) ----------------
    $input = $input ?? "";

    // ALWAYS send something to avoid EOFError
    fwrite($pipes[0], $input . PHP_EOL);
    fflush($pipes[0]);
    fclose($pipes[0]);

    // ---------------- OUTPUT ----------------
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    proc_close($process);

    $output = trim($stdout . $stderr);

    // fallback safety
    if ($output === "") {
        $output = "⚠️ No Output (Program executed but no print OR input missing)";
    }

    if ($debug) {
        $output = "Debug Mode\n\n" . $output;
    }

    return response()->json([
        'output' => $output
    ]);
}
   // Scala
// Scala
else if ($lang == "scala") {

    $file = $tempDir . "\\Main.scala";
    $class = $tempDir . "\\Main.class";

    file_put_contents($file, $code, LOCK_EX);

    $jdkHome = "D:\\dev-tools\\jdk20\\jdk-20.0.1+9";
    $scalaHome = "D:\\dev-tools\\scala3-3.8.3-x86_64-pc-win32";

    $scalac = "$scalaHome\\bin\\scalac.bat";
    $java   = "$jdkHome\\bin\\java.exe";

    $env = [
        "JAVA_HOME" => $jdkHome,
        "PATH" => $jdkHome . "\\bin;" . getenv("PATH")
    ];

    // Compile
    $compileCmd = "\"$scalac\" \"$file\"";

    $descriptorspec = [
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open($compileCmd, $descriptorspec, $pipes, $tempDir, $env);

    $compileOutput = "";
    $compileError = "";
    $returnCode = 0;

    if (is_resource($process)) {

        $compileOutput = stream_get_contents($pipes[1]);
        $compileError  = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);
    }

    // Compile Only
    if ($compile) {

        if ($returnCode !== 0) {
            return response()->json([
                'output' => "❌ Compile Error:\n" . $compileOutput . $compileError
            ]);
        }

        return response()->json([
            'output' => "✅ Compiled Successfully"
        ]);
    }

    // Run
    $libPath = "$scalaHome\\lib\\*";

    $runCmd = "\"$java\" -cp \"$tempDir;$libPath\" Main";

    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open($runCmd, $descriptorspec, $pipes);

    if (is_resource($process)) {

        if (!empty($input)) {
            fwrite($pipes[0], $input . PHP_EOL);
        }

        fflush($pipes[0]);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $error  = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($process);
    }

    // Debug
    if ($debug) {
        $output = "Debug Mode\n\n" . $output;
    }

    return response()->json([
        'output' => trim($output . $error)
    ]);
}

 // Kotlin
else if ($lang == "kotlin") {

    $file = $tempDir . "\\Main.kt";
    $jar  = $tempDir . "\\Main.jar";

    file_put_contents($file, $code, LOCK_EX);

    if (file_exists($jar)) {
        unlink($jar);
    }

    $kotlinc = "D:\\dev-tools\\kotlin\\kotlinc\\bin\\kotlinc-jvm.bat";
    $javaHome = "D:\\dev-tools\\jdk20\\jdk-20.0.1+9";
    $java = $javaHome . "\\bin\\java.exe";

    $env = [
        "JAVA_HOME" => $javaHome,
        "PATH" => $javaHome . "\\bin;" . getenv("PATH")
    ];

    // Compile
    $compileCmd = "\"$kotlinc\" \"$file\" -include-runtime -d \"$jar\"";

    $descriptorspec = [
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open($compileCmd, $descriptorspec, $pipes, null, $env);

    $compileOutput = "";
    $compileError = "";
    $returnCode = 0;

    if (is_resource($process)) {

        $compileOutput = stream_get_contents($pipes[1]);
        $compileError  = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);
    }

    // 🔥 COMPILE ONLY
    if ($compile) {

        if ($returnCode !== 0) {
            return response()->json([
                'output' => "❌ Compile Error:\n" . $compileOutput . $compileError
            ]);
        }

        return response()->json([
            'output' => "✅ Compiled Successfully"
        ]);
    }

    // RUN
    $runCmd = "\"$java\" -jar \"$jar\"";

    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open($runCmd, $descriptorspec, $pipes);

    if (is_resource($process)) {

        if (!empty($input)) {
            fwrite($pipes[0], $input . PHP_EOL);
        }

        fflush($pipes[0]);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $error  = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($process);
    }

    // 🔥 DEBUG MODE
    if($debug){
        $output = "Debug Mode\n\n" . $output;
    }

    return response()->json([
        'output' => trim($output . $error)
    ]);
}
  // Ruby
    else if ($lang == "ruby") {

    // ---------------- SAFE PATH ----------------
    $tempDir = str_replace("/", "\\", $tempDir);

    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $file = "$tempDir\\$filename.rb";
    file_put_contents($file, $code, LOCK_EX);

    $file = str_replace("/", "\\", $file);

    if ($compile) {
        return response()->json([
            'output' => "✅ Ruby does not require compile"
        ]);
    }

    // ---------------- RUBY EXEC ----------------
    $ruby = "D:\\Ruby\\bin\\ruby.exe";

    $cmd = "\"$ruby\" \"$file\"";

    $descriptors = [
        0 => ["pipe", "r"], // stdin
        1 => ["pipe", "w"], // stdout
        2 => ["pipe", "w"]  // stderr
    ];

    $process = proc_open($cmd, $descriptors, $pipes, $tempDir);

    if (!is_resource($process)) {
        return response()->json([
            'output' => "❌ Execution Error"
        ]);
    }

    // ---------------- INPUT FIX ----------------
    $input = trim($input ?? "");

    if ($input !== "") {
        fwrite($pipes[0], $input . PHP_EOL);
        fflush($pipes[0]);
    }

    fclose($pipes[0]);

    // ---------------- OUTPUT FIX ----------------
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    $output = trim($stdout . $stderr);

    // fallback (IMPORTANT)
    if ($output === "") {
        $output = "⚠️ No Output (Program executed but nothing printed or no input received)";
    }

    if ($debug) {
        $output = "Debug Mode\n\n" . $output;
    }

    return response()->json([
        'output' => $output
    ]);
}
}
    // 🗑️ DELETE project (🔥 HARD DELETE – DB se gone)
    public function destroy($id)
    {
        Project::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail()
            ->forceDelete(); // 🔥 permanent delete

        return response()->json(['success' => true]);
    }
    

}
