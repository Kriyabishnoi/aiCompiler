@extends('layouts.app')

@section('content')

<style>
body {
    font-family: 'Poppins', sans-serif;
}

/* CARD */
.profile-card {
    max-width: 650px;
    margin: 50px auto;
    padding: 30px;
    border-radius: 16px;
    background: var(--card);
    border: 1px solid var(--border);
    text-align: center;
    box-shadow: 0 0 25px rgba(0,0,0,0.2);
    transition: 0.3s;
}
.profile-card:hover {
    transform: translateY(-5px);
}

/* AVATAR */
.profile-img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 3px solid #22c55e;
    padding: 3px;
    margin-bottom: 15px;
}

/* NAME */
.profile-name {
    font-size: 22px;
    font-weight: 600;
}

/* STATS */
.stats {
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 15px;
    margin-top: 20px;
}

.stat-box {
    background: var(--bg);
    padding: 15px;
    border-radius: 10px;
    border: 1px solid var(--border);
}

/* FORM */
input, button {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid var(--border);
    margin-top: 10px;
    width: 100%;
}

button {
    background: #22c55e;
    color: #020617;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #16a34a;
}
</style>

<div class="profile-card">

    <!-- PROFILE IMAGE -->
    <img class="profile-img"
         src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://via.placeholder.com/110' }}">

    <!-- NAME -->
    <div class="profile-name">{{ $user->name }}</div>
    <p>{{ $user->email }}</p>
    <p>Joined: {{ $user->created_at->format('d M Y') }}</p>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-box">📊 Total<br><strong>{{ $stats['total'] }}</strong></div>
        <div class="stat-box">💻 C<br><strong>{{ $stats['c'] }}</strong></div>
        <div class="stat-box">💻 C++<br><strong>{{ $stats['cpp'] }}</strong></div>
        <div class="stat-box">☕ Java<br><strong>{{ $stats['java'] }}</strong></div>
        <div class="stat-box">🐍 Python<br><strong>{{ $stats['python'] }}</strong></div>
        <div class="stat-box">⚡ Scala<br><strong>{{ $stats['scala'] }}</strong></div>
        <div class="stat-box">💎 Ruby<br><strong>{{ $stats['ruby'] }}</strong></div>
        <div class="stat-box">📱 Kotlin<br><strong>{{ $stats['kotlin'] }}</strong></div>
    </div>

    <hr style="margin:20px 0;">

    <!-- UPDATE FORM -->
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <input type="text" name="name" value="{{ $user->name }}" placeholder="Your Name">

        <input type="file" name="avatar">

        <button type="submit">Update Profile</button>
    </form>

</div>

@endsection