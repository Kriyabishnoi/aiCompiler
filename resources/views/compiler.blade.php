<!DOCTYPE html>
<html>
<head>
    <title>Online Compiler</title>

    <!-- Monaco loader -->
    <script src="https://unpkg.com/monaco-editor/min/vs/loader.js"></script>
</head>

<body>

<h2>My C/C++ Compiler</h2>

<select id="lang">
    <option value="c">C</option>
    <option value="cpp">C++</option>
</select>

<div id="editor" style="height:400px;border:1px solid #ccc"></div>
<textarea id="input" placeholder="Enter input here"></textarea>
<button onclick="runCode()">Run</button>

<pre id="output"></pre>

<!-- 🔥 YAHI PAR TUMHARA JS CODE AAYEGA -->
<script>
require.config({ paths: { vs: 'https://unpkg.com/monaco-editor/min/vs' }});

require(['vs/editor/editor.main'], function () {

    window.editor = monaco.editor.create(document.getElementById('editor'), {
        value: `#include <iostream>
using namespace std;

int main(){
    cout << "Hello World";
    return 0;
}`,
        language: 'cpp',
        theme: 'vs-dark'
    });
});

function runCode() {
    fetch("/api/run-code", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            code: editor.getValue(),
            language: document.getElementById("lang").value
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("output").innerText =
            "Compile:\n" + data.compile + "\n\nOutput:\n" + data.output;
    });
}
</script>

</body>
</html>