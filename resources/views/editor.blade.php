@extends('layouts.app')

@section('content')
<style>
body{
    margin:0;
    background:#1e1e1e;
    color:#d4d4d4;
    font-family:Segoe UI;
}

.app{
    height:100vh;
    display:flex;
    flex-direction:column;
}

/* TOPBAR */
.topbar{
    height:40px;
    background:#2d2d2d;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 10px;
    border-bottom:1px solid #111;
}

/* MAIN SPLIT */
.main{
    flex:1;
    display:flex;
}

/* LEFT EDITOR */
.left{
    flex:2;
    border-right:1px solid #111;
    display:flex;
    flex-direction:column;
}

/* RIGHT OUTPUT */
.right{
    flex:1;
    display:flex;
    flex-direction:column;
    background:#111;
}

#console{
    flex:1;
    display:flex;
    flex-direction:column;
}

#terminal{
    flex:1;
    background:#0c0c0c;
    color:#00ff9d;
    font-family:Consolas, monospace;
    padding:10px;
    display:flex;
    flex-direction:column;
    overflow:hidden;
}

#terminalOutput{
    flex:1;
    overflow:auto;
    white-space:pre-wrap;
}

.terminal-line{
    display:flex;
    align-items:center;
    border-top:1px solid #222;
    padding-top:6px;
}

.prompt{
    margin-right:6px;
    color:#00ff9d;
}

#terminalInput{
    flex:1;
    background:transparent;
    border:none;
    outline:none;
    color:#00ff9d;
    font-family:Consolas;
    font-size:14px;
}
#userInput{
    height:70px;
    background:#1e1e1e;
    border:none;
    border-top:1px solid #333;
    color:white;
    padding:10px;
    outline:none;
    resize:none;
    font-family:Consolas;
}

/* EDITOR */
#editor{
    flex:1;
}

/* OUTPUT */
#output{
    flex:1;
    padding:10px;
    overflow:auto;
    white-space:pre-wrap;
    font-family:Consolas;
    font-size:13px;
    color:#00ff9d;
}

/* BOTTOM TERMINAL */
.bottom{
    height:200px;
    border-top:1px solid #111;
    background:#111;
    display:flex;
    flex-direction:column;
}

/* TERMINAL */
#console{
    flex:1;
    display:flex;
    flex-direction:column;
}

/* TERMINAL OUTPUT */
#terminalOutput{
    flex:1;
    padding:10px;
    overflow:auto;
    color:#00ff9d;
    font-family:Consolas;
}

/* INPUT */
#userInput{
    height:70px;
    background:#1e1e1e;
    border:none;
    border-top:1px solid #333;
    color:white;
    padding:10px;
    outline:none;
    resize:none;
    font-family:Consolas;
}

/* PANEL TITLE */
.panel-title{
    padding:6px 10px;
    font-size:12px;
    background:#2d2d2d;
    border-bottom:1px solid #111;
}

/* BUTTONS */
.actions button{
    background:#0e639c;
    color:white;
    border:none;
    padding:5px 10px;
    margin-left:5px;
    cursor:pointer;
}
#aiFab{
    position:fixed;
    right:20px;
    bottom:20px;
    width:55px;
    height:55px;
    border-radius:50%;
    background:#0e639c;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    cursor:pointer;
    box-shadow:0 5px 15px rgba(0,0,0,0.4);
    z-index:99999;
}
#aiFab:hover{
    background:#1177cc;
}
.ai-panel{
    position:fixed;
    right:0;
    top:0;
    width:320px;
    height:100vh;
    background:#111;
    border-left:1px solid #333;
    display:flex;
    flex-direction:column;
    transition:0.3s ease;
    z-index:999999;
    pointer-events:auto; /* always clickable */
}

.ai-panel.hidden{
    transform:translateX(100%);
    pointer-events:none;   /* 🔥 prevents ghost click issue */
}

.ai-header{
    padding:10px;
    background:#2d2d2d;
    color:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:14px;
}

.ai-header span{
    cursor:pointer;
}

#aiMessages{
    flex:1;
    padding:10px;
    overflow:auto;
    color:#00ff9d;
    font-family:Consolas;
    font-size:12px;
}

.ai-input-box{
    display:flex;
    border-top:1px solid #333;
}

#aiInput{
    flex:1;
    background:#1e1e1e;
    border:none;
    color:white;
    padding:8px;
    outline:none;
}

.ai-input-box button{
    background:#0e639c;
    border:none;
    color:white;
    padding:8px 12px;
    cursor:pointer;
}
.ai-msg{
    margin-bottom:12px;
    padding:8px;
    border-radius:6px;
    line-height:1.4;
}

.ai-user{
    background:#1e293b;
    color:#fff;
}

.ai-bot{
    background:#020617;
    border:1px solid #333;
    color:#00ff9d;
}

.ai-thinking{
    opacity:0.6;
    font-style:italic;
}

.ai-msg pre{
    background:#000;
    padding:8px;
    border-radius:6px;
    overflow:auto;
    color:#22c55e;
}
.ai-highlight-line {
    background: rgba(0,255,0,0.15) !important;
    border-left: 3px solid #00ff88;
}
</style>

<div class="app">
    <div id="explainPopup" style="
position:fixed;
top:50%;
left:50%;
transform:translate(-50%,-50%);
background:#1e1e1e;
padding:20px;
border:1px solid #333;
display:none;
z-index:999999;
">

<h3 style="margin-top:0;color:white;">Explain Code</h3>

<button onclick="explainCode('english')">English</button>
<button onclick="explainCode('hindi')">Hindi</button>
<button onclick="explainCode('hinglish')">Hinglish</button>

<br><br>

<button onclick="closeExplain()">Cancel</button>

</div>

    <!-- TOP BAR -->
    <div class="topbar">
        
        <div>📁 {{ $project->name }}</div>
        <div class="actions">
            <button onclick="saveCode()">Save</button>
            <button onclick="compileCode()">Compile</button>
            <button onclick="runCode()">Run</button>
            <button onclick="clearTerminal()">Clear</button>
            <!--<button onclick="debugCode()">Debug</button>-->
            <button onclick="downloadCode()">Download</button>
            <button onclick="showExplainOptions()">Explain Code</button>
            <button onclick="stopSpeaking()">Stop</button>
            <button onclick="pauseSpeaking()">Pause</button>
<button onclick="resumeSpeaking()">Resume</button>
<button onclick="restartSpeaking()">Restart</button>
            
        </div>
        
    </div>

    <div class="main">

    <!-- LEFT: EDITOR -->
    <div class="left">
        <div id="editor"></div>
    </div>

    <!-- RIGHT SIDE -->
<div class="right">

    <!-- 🤖 FLOAT BUTTON -->
    <div id="aiFab" onclick="toggleAIPanel()">
        🤖
    </div>

    <!-- 🤖 AI PANEL -->
    <div id="aiPanel" class="ai-panel hidden">

        <div class="ai-header">
            🤖 AI Assistant
            <span onclick="closeAIPanel()">✖</span>
        </div>

        <div id="aiMessages"></div>

        <div class="ai-input-box">
            <input type="text" id="aiInput" placeholder="Ask AI..." />
            <button onclick="sendAI()">Send</button>
        </div>

    </div>

    <!-- TERMINAL -->
    <div class="panel-title">Terminal</div>

    <div id="terminal">
        <div id="terminalOutput"></div>

        <div class="terminal-line">
            <span class="prompt">></span>
            <input id="terminalInput" type="text" autocomplete="off" />
        </div>
    </div>

</div>
</div>

</div>

   

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs/loader.js"></script>

<script>
    
axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs' }});

require(['vs/editor/editor.main'], function () {

    const languageMap = {
        c: 'c',
        cpp: 'cpp',
        java: 'java',
        python: 'python',
        scala: 'scala',
        ruby: 'ruby',
        kotlin: 'kotlin'
    };
    
    let existingCode = `{!! addslashes($project->code ?? '') !!}`;
    let lang = "{{ $project->language }}";

    let defaultCode = "";
    

    if (!existingCode || existingCode.trim() === "") {

        if (lang === "c") {
            defaultCode = `#include <stdio.h>

int main() {
    printf("Hello World");
    return 0;
}`;
        }

        else if (lang === "cpp") {
            defaultCode = `#include <iostream>
using namespace std;

int main() {
    cout << "Hello World";
    return 0;
}`;
        }

        else if (lang === "java") {
            let className = "{{ $project->name }}".replace(/[^a-zA-Z0-9]/g, '');
            defaultCode = `
            import java.util.*
            public class ${className} {
    public static void main(String[] args) {
        System.out.println("Hello World");
    }
}`;
        }

        else if (lang === "python") {
            defaultCode = `print("Hello World")`;
        }

        else if (lang === "scala") {
            defaultCode = `object Main {
    def main(args: Array[String]) = {
        println("Hello World")
    }
}`;
        }

        else if (lang === "ruby") {
            defaultCode = `puts "Hello World"`;
        }

        else if (lang === "kotlin") {
            defaultCode = `fun main() {
    println("Hello World")
}`;
        }

    } else {
        defaultCode = existingCode;
    }

    window.editor = monaco.editor.create(document.getElementById('editor'), {
    value: defaultCode,
    language: languageMap[lang] || 'plaintext',
    theme: "vs-dark",
    automaticLayout: true,
    fontSize: 14,
    minimap: { enabled: true },
    scrollBeyondLastLine: false

    
});
    
// 🔥 AI REAL TIME SUGGESTIONS
let aiTimeout;

window.editor.onDidChangeModelContent(function () {

    clearTimeout(aiTimeout);

    aiTimeout = setTimeout(async () => {

        let code = window.editor.getValue();
        let position = window.editor.getPosition();

        try {

            let res = await axios.post("/ai-chat", {
                message: `
Continue this code.
Return only next few lines.

${code}
`
            });

            showInlineSuggestion(res.data.reply, position);

        } catch (e) {
            console.log("AI error");
        }

    }, 800);

});

// 🔥 SHORTCUTS
window.editor.addCommand(
    monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS,
    function () {
        saveCode();
    }
);

window.editor.addCommand(
    monaco.KeyMod.CtrlCmd | monaco.KeyMod.Shift | monaco.KeyCode.KeyS,
    function () {
        downloadCode();
    }
);

}); 

// SAVE
let projectId = "{{ $project->id }}";

function saveCode(){

    printLine("> Saving...");

    axios.put(`/project/${projectId}`, {
        name: "{{ $project->name }}",
        language: "{{ $project->language }}",
        code: window.editor.getValue()
    })
    .then(() => {

        printLine("✅ Program saved successfully");
        terminal.scrollTop = terminal.scrollHeight;

    })
    .catch(err => {

        console.log(err);
        printLine("❌ Save Error");

    });
}
function clearTerminal(){
    terminalOutput.innerText = "";
    printLine("> Ready...");
}
function compileCode(){

    printLine("> Compiling...");

    axios.post('/run-code', {
        code: window.editor.getValue(),
        language: "{{ $project->language }}",
        compile: true
    })
    .then(res => {

        let output = res?.data?.output;

        if (!output || output.trim() === "") {
            output = "✅ Compile Success";
        }

        printLine(output);
        terminalInput.focus();
    })
    .catch(() => {
        printLine("❌ Compile Error");
        terminalInput.focus();
    });
}
function downloadCode(){

    let code = window.editor.getValue();
    let name = "{{ $project->name }}";
    let lang = "{{ $project->language }}";

    let ext = "txt";

    if (lang === "c") ext = "c";
    else if (lang === "cpp") ext = "cpp";
    else if (lang === "java") ext = "java";
    else if (lang === "python") ext = "py";
    else if (lang === "scala") ext = "scala";
    else if (lang === "ruby") ext = "rb";
    else if (lang === "kotlin") ext = "kt";

    let blob = new Blob([code], { type: "text/plain" });

    let a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = name + "." + ext;
    a.click();
}

// CLEAR
function clearEditor(){
    if(confirm('Clear editor?')){
        window.editor.setValue('');
    }
}
/*function runCode(){
    axios.post('/run-code', {
        code: window.editor.getValue(),
        language: "{{ $project->language }}"
    }).then(res => {
        document.getElementById("output").innerText = res.data.output;
    }).catch(err => {
        document.getElementById("output").innerText = "Error running code";
    });
}*/

function closeAIPanel(){
    document.getElementById("aiPanel").classList.add("hidden");
}
function toggleAIPanel(){

    let panel = document.getElementById("aiPanel");
    panel.classList.toggle("hidden");

    // 🔥 important: delay focus after animation
    setTimeout(() => {
        if(!panel.classList.contains("hidden")){
            document.getElementById("aiInput").focus();
        }
    }, 300);
}
function appendAI(text, type="bot"){

    let box = document.getElementById("aiMessages");

    let cls = "ai-bot";
    if(type === "user") cls = "ai-user";
    if(type === "thinking") cls = "ai-thinking";

    let code = extractCode(text);

    let button = "";

    if(code){
        button = `
        <button onclick='insertAIToEditor(${JSON.stringify(code)})'
        style="margin-top:6px;background:#0e639c;color:white;border:none;padding:5px 8px;cursor:pointer;">
        Insert to Editor
        </button>
        `;
    }

    box.innerHTML += `
    <div class="ai-msg ${cls}">
        ${formatAI(text)}
        ${button}
    </div>
    `;

    box.scrollTop = box.scrollHeight;
}
function formatAI(text){

    // code block
    text = text.replace(/```([\s\S]*?)```/g, "<pre>$1</pre>");

    // new line
    text = text.replace(/\n/g,"<br>");

    return text;
}
async function sendAI(){

    let input = document.getElementById("aiInput");
    let message = input.value.trim();
    if(!message) return;

    appendAI(message, "user");
    
    input.value = "";

    appendAI("Thinking...", "thinking");

    try {

        let res = await axios.post("/ai-chat", {
            message: message
        });

        let box = document.getElementById("aiMessages");
        box.lastChild.remove();

        let reply = res.data.reply || "No response";

        appendAI(reply, "bot");

        // ❌ REMOVE AUTO INSERT

    } 
    catch(err){
        console.log(err);
        let box = document.getElementById("aiMessages");
        box.lastChild.remove();
        appendAI("❌ AI error");
    }
}
function extractCode(text){

    let match = text.match(/```([\s\S]*?)```/);

    if(match){
        return match[1].trim();
    }

    return null;
}
function insertAIToEditor(code){

    if(!code) return;

    if(confirm("Are you sure you want to replace code?")){

        window.editor.setValue(code);

        printLine("✅ AI Code Inserted");

    }
}
function insertCodeToEditor(text){

    if(!window.editor) return;

    const position = window.editor.getPosition();

    window.editor.executeEdits("", [{
        range: new monaco.Range(
            position.lineNumber,
            position.column,
            position.lineNumber,
            position.column
        ),
        text: "\n" + text + "\n"
    }]);

}
let terminalOutput = document.getElementById("terminalOutput");
let terminalInput = document.getElementById("terminalInput");

terminalOutput.innerText = "> Ready...\n";
terminalInput.focus();

let history = [];
let historyIndex = -1;

/* ENTER HANDLER */
terminalInput.addEventListener("keydown", function(e){

    if(e.key === "Enter"){
        e.preventDefault();

        let input = terminalInput.value.trim();
        if(!input) return;

        // save history
        history.push(input);
        historyIndex = history.length;

        printLine("> " + input);

        runCode(input);

        terminalInput.value = "";
    }

    // HISTORY UP
    if(e.key === "ArrowUp"){
        e.preventDefault();
        if(historyIndex > 0){
            historyIndex--;
            terminalInput.value = history[historyIndex];
        }
    }

    // HISTORY DOWN
    if(e.key === "ArrowDown"){
        e.preventDefault();
        if(historyIndex < history.length - 1){
            historyIndex++;
            terminalInput.value = history[historyIndex];
        } else {
            terminalInput.value = "";
        }
    }

});

/* PRINT TO TERMINAL */
function printLine(text){
    terminalOutput.innerText += text + "\n";
    terminalOutput.scrollTop = terminalOutput.scrollHeight;
    terminalInput.focus(); // 🔥 keep input alive
}
// RUN
function runCode(input = "") {

    terminalOutput.innerText = "";

    printLine("> Running...");
    printLine(""); // 🔥 blank line

    axios.post('/run-code', {
        code: window.editor.getValue(),
        language: "{{ $project->language }}",
        input: input
    })
    .then(res => {

        let output = res?.data?.output;

        if (!output || output.trim() === "") {
            output = "⚠️ No Output";
        }

        printLine(output);
        printLine("");
        printLine("> Ready...");

        terminalInput.focus();
    })
    .catch(err => {
        console.log(err);
        printLine("❌ Execution Error");
        terminalInput.focus();
    });
}

// DEBUG
function debugCode(){

    printLine("> Debugging...");

    axios.post('/run-code', {
        code: window.editor.getValue(),
        language: "{{ $project->language }}",
        debug: true
    })
    .then(res => {

        printLine("---------------- DEBUG ----------------");
        printLine(res?.data?.output || "No Debug Output");
        printLine("--------------------------------------");

        terminalInput.focus();
    })
    .catch(() => {
        printLine("❌ Debug Error");
        terminalInput.focus();
    });
}
let lastExplanation = "";

function showExplainOptions(){
    document.getElementById("explainPopup").style.display="block";
}

function closeExplain(){
    document.getElementById("explainPopup").style.display="none";
}
async function explainCode(lang){

    lastLang = lang;

    closeExplain();

    let code = window.editor.getValue();

    printLine("> Analyzing code...");

    try{

        let res = await axios.post("/ai-chat",{
            message: `
Explain this code line by line in ${lang}.

Format:
Line 1: explanation
Line 2: explanation

Do NOT use backticks
Do NOT show code block

Code:
${code}
`
        });

        lastExplanation = res.data.reply || res.data.message || "No response";

        appendAI(lastExplanation,"bot");

        speakExplanation(lang);

    }catch(e){
        console.log(e.response?.data || e);
        printLine("❌ Explain error");
    }

}
let speech = null;
let explanationLines = [];
let currentLine = 0;
let decorations = [];
let lastLang = "english";

let codeLines = [];

function speakExplanation(lang){

    speechSynthesis.cancel();

    let model = window.editor.getModel();
    let total = model.getLineCount();

    codeLines = [];

    for(let i=1;i<=total;i++){
        let text = model.getLineContent(i).trim();
        if(text !== ""){
            codeLines.push(i);
        }
    }

    explanationLines = lastExplanation
        .split("\n")
        .filter(l => l.trim() !== "");

    currentLine = 0;

    speakNextLine(lang);
}
function pauseSpeaking(){
    speechSynthesis.pause();
    isPaused = true;
}
function resumeSpeaking(){
    if(isPaused){
        speechSynthesis.resume();
    }
}
function restartSpeaking(){

    speechSynthesis.cancel();

    currentLine = 0;

    speakNextLine(lastLang);

}
function stopSpeaking(){

    speechSynthesis.cancel();

    if(window.editor){
        window.editor.deltaDecorations(decorations,[]);
    }

}

function speakNextLine(lang){

    if(currentLine >= explanationLines.length){
        return;
    }

    let lineText = explanationLines[currentLine];

    let lineNumber = codeLines[currentLine] || 1;

    highlightEditorLine(lineNumber);

    let text = lineText.replace(/Line\s*\d+:/i,"");

    speech = new SpeechSynthesisUtterance(text);

    if(lang === "hindi"){
        speech.lang = "hi-IN";
    }
    else if(lang === "hinglish"){
        speech.lang = "hi-IN";
    }
    else{
        speech.lang = "en-US";
    }

    speech.rate = 1;

    speech.onend = function(){
        currentLine++;
        speakNextLine(lang);
    }

    speechSynthesis.speak(speech);
}
function highlightEditorLine(line){

    decorations = window.editor.deltaDecorations(decorations,[
        {
            range: new monaco.Range(line,1,line,1),
            options:{
                isWholeLine:true,
                className:"ai-highlight-line"
            }
        }
    ]);

}
</script>

@endsection