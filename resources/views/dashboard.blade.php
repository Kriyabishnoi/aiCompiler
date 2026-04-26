{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
:root {
    --bg: #f1f5f9;
    --card: #ffffff;
    --text: #0f172a;
    --border: #cbd5e1;
    --primary: #22c55e;
}

/* DARK MODE */
.dark-mode {
    --bg: #020617;
    --card: #0f172a;
    --text: #e5e7eb;
    --border: #1e293b;
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    color: var(--text);
    transition: 0.3s;
}

/* HEADER */
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

/* CENTER TITLE FIX */
.title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
}

/* LEFT */
.left {
    z-index: 1;
}

/* RIGHT */
.right {
    display: flex;
    gap: 10px;
    z-index: 1;
}

/* PROFILE IMAGE */
.profile-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #22c55e;
    cursor: pointer;
}
/* BUTTONS */
button {
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    transform: scale(1.05);
}

.logout-btn { background: #ef4444; color: #fff; }
.edit-btn { background: #38bdf8; color: #020617; }
.delete-btn { background: #f87171; color: #020617; }

/* MAIN */
.main { padding: 30px; }

/* CARD */
.card {
    background: var(--card);
    padding: 25px;
    border-radius: 12px;
    border: 1px solid var(--border);
    margin-bottom: 20px;
    transition: 0.3s;
}

.card:hover { transform: translateY(-3px); }

/* INPUT */
input, select {
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg);
    color: var(--text);
}

/* PROJECT LIST */
li {
    background: var(--bg);
    padding: 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    margin-bottom: 10px;
}
</style>
<div class="header">

    <!-- LEFT (PROFILE) -->
    <div class="left">
        <a href="{{ route('profile') }}">
            <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://via.placeholder.com/40' }}"
                 class="profile-img">
        </a>
    </div>

    <!-- CENTER (TITLE) -->
    <h2 class="title">AI Compiler Dashboard</h2>

    <!-- RIGHT (BUTTONS) -->
    <div class="right">
        <button onclick="toggleMode()" id="themeBtn" style="background:#6366f1;color:white;">
    🌙
</button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

</div>

    <div class="main">

        
        <div class="card">
            <h3>Create New Project</h3>

            <input type="text" id="projectName" placeholder="Project Name">
            <select id="languageSelect">
                    <option value="">Select Language</option>
                    <option value="c">C</option>
                    <option value="cpp">C++</option>
                    <option value="java">Java</option>
                    <option value="python">Python</option>
                    <option value="scala">Scala</option>
                    <option value="ruby">Ruby</option>
                    <option value="kotlin">Kotlin</option>
            </select>
    
            <button id="createBtn" style="background:#22c55e;color:#020617;">
                Create
            </button>
            <script>
const nameInput = document.getElementById("projectName");
const langSelect = document.getElementById("languageSelect");

function updateExtension() {
    let lang = langSelect.value;
    let name = nameInput.value.split('.')[0];

    if (!lang) return;

    let ext = "";

    if (lang === "c") ext = ".c";
    else if (lang === "cpp") ext = ".cpp";
    else if (lang === "java") ext = ".java";
    else if (lang === "python") ext = ".py";
    else if (lang === "scala") ext = ".scala";
    else if (lang === "ruby") ext = ".rb";
    else if (lang === "kotlin") ext = ".kt";

    nameInput.value = name + ext;
}

// jab language change ho
langSelect.addEventListener("change", updateExtension);

// jab user name likh ke bahar click kare
nameInput.addEventListener("blur", updateExtension);
</script>
            <hr style="margin:20px 0; border-color:#1e293b;">

            <h3>My Projects</h3>
            <a href="{{ route('saved.programs') }}">
<button style="background:#22c55e;color:#020617;">
Saved Programs
</button>
</a>
            <ul id="projectsList">
                @foreach($projects as $project)
                    <li id="project-{{ $project->id }}" style="margin-bottom:14px;">
                        <strong>{{ $project->name }}</strong>
                        ({{ $project->language }})

                        <br>
                        <small>
                             {{ $project->created_at->format('d M Y, h:i A') }}
                        </small>

                        <br>

                        <a href="{{ route('editor', $project->id) }}">Open Editor →</a>

                        <button class="edit-btn"
                            onclick="editProject({{ $project->id }}, '{{ $project->name }}', '{{ $project->language }}')">
                             Edit
                        </button>

                        <button class="delete-btn"
                            onclick="deleteProject({{ $project->id }})">
                            Delete
                        </button>
                    </li>
                @endforeach
            </ul>

        
    

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

// ✅ CREATE (AJAX SAFE)
document.getElementById('createBtn').addEventListener('click', function () {
    let name = document.getElementById('projectName').value.trim();
    let language = document.getElementById('languageSelect').value;

    if (!name || !language) {
        alert('Please enter project name and select language!');
        return;
    }

    axios.post("{{ route('project.create.ajax') }}", { name, language })
        .then(res => {
            if (res.data.success) {
                location.reload();
            } else {
                alert(res.data.message); // duplicate error
            }
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong!');
        });

}); // ✅ YE IMPORTANT CLOSE HAI

// DELETE
function deleteProject(id) {
    if (!confirm('Are you sure you want to delete this project?')) return;

    axios.delete(`/project/${id}`)
        .then(res => {
            if (res.data.success) {
                document.getElementById(`project-${id}`).remove();
            }
        });
}

// EDIT
function editProject(id, oldName, oldLang) {

let name = prompt('Edit project name:', oldName);
if (!name) return;

let language = prompt('Edit language:', oldLang);
if (!language) return;

let code = "";

// default code
if(language === "c"){
code = `#include <stdio.h>

int main(){
    printf("Hello World");
    return 0;
}`;
}

else if(language === "cpp"){
code = `#include <iostream>
using namespace std;

int main(){
    cout << "Hello World";
}`;
}

else if(language === "java"){
code = `public class Main {
    public static void main(String[] args){
        System.out.println("Hello World");
    }
}`;
}

else if(language === "python"){
code = `print("Hello World")`;
}

else if(language === "scala"){
code = `object Main {
    def main(args: Array[String]) = {
        println("Hello World")
    }
}`;
}

else if(language === "ruby"){
code = `puts "Hello World"`;
}

else if(language === "kotlin"){
code = `fun main(){
    println("Hello World")
}`;
}

axios.put(`/project/${id}`, {
name,
language,
code
})
.then(res => {
if(res.data.success){
location.reload();
}
});

}
</script>
@endsection
<script>
function toggleMode(){
    document.body.classList.toggle("dark-mode");

    let btn = document.getElementById("themeBtn");

    if(document.body.classList.contains("dark-mode")){
        localStorage.setItem("theme", "dark");
        btn.innerHTML = "☀️";
    } else {
        localStorage.setItem("theme", "light");
        btn.innerHTML = "🌙";
    }
}

window.onload = function(){
    let saved = localStorage.getItem("theme");
    let btn = document.getElementById("themeBtn");

    if(saved === "dark"){
        document.body.classList.add("dark-mode");
        btn.innerHTML = "☀️";
    } else {
        btn.innerHTML = "🌙";
    }
}
</script>