@extends('layouts.app')

@section('content')

<style>

:root {
--bg:#f1f5f9;
--card:#ffffff;
--text:#020617;
--border:#cbd5e1;
--primary:#22c55e;
}

.dark-mode{
--bg:#020617;
--card:#0f172a;
--text:#e5e7eb;
--border:#1e293b;
}

body{
background:var(--bg);
color:var(--text);
transition:.3s;
font-family:'Poppins',sans-serif;
}

.header{
display:flex;
justify-content:space-between;
align-items:center;
padding:20px 30px;
}

.title{
font-size:22px;
font-weight:600;
}

.main{
padding:20px 30px;
}

.card{
background:var(--card);
border:1px solid var(--border);
padding:20px;
border-radius:12px;
margin-bottom:15px;
transition:.3s;
}

.card:hover{
transform:translateY(-3px);
}

button{
padding:8px 14px;
border:none;
border-radius:8px;
cursor:pointer;
transition:.2s;
}

button:hover{
transform:scale(1.05);
}

.open{
background:#38bdf8;
color:#020617;
}

.download{
background:#22c55e;
color:#020617;
}

.back{
background:#6366f1;
color:white;
}

.theme{
background:#111827;
color:white;
}

</style>


<div class="header">

<div class="title">
Saved Programs
</div>

<div>

<button onclick="toggleMode()" id="themeBtn" class="theme">
🌙
</button>

<a href="/dashboard">
<button class="back">
← Dashboard
</button>
</a>

</div>

</div>


<div class="main">

@foreach($programs as $p)

<div class="card">

<li style="margin-bottom:20px;">

<strong>{{ $p->name }}</strong>
({{ $p->language }})

<br>

<a href="{{ route('editor.program',$p->id) }}">
Open →
</a>

<button onclick="downloadProgram({{ $p->id }})">
Download
</button>

<button onclick="deleteProgram({{ $p->id }})" style="background:red;color:white;">
Delete
</button>

</li>

</div>

@endforeach

</div>

@endsection


@section('scripts')

<script>

function downloadProgram(id){
window.location.href="/download/"+id;
}

function toggleMode(){

document.body.classList.toggle("dark-mode");

let btn=document.getElementById("themeBtn");

if(document.body.classList.contains("dark-mode")){
localStorage.setItem("theme","dark");
btn.innerHTML="☀️";
}else{
localStorage.setItem("theme","light");
btn.innerHTML="🌙";
}

}
function deleteProgram(id){

if(!confirm("Are you sure to delete this program?")){
return;
}

fetch("/delete/"+id,{
method:"DELETE",
headers:{
'X-CSRF-TOKEN': '{{ csrf_token() }}',
'Content-Type':'application/json'
}
})
.then(res=>res.json())
.then(data=>{
location.reload();
})
.catch(err=>{
console.log(err);
alert("Delete failed");
});

}

window.onload=function(){

let saved=localStorage.getItem("theme");
let btn=document.getElementById("themeBtn");

if(saved==="dark"){
document.body.classList.add("dark-mode");
btn.innerHTML="☀️";
}else{
btn.innerHTML="🌙";
}

}

</script>

@endsection