<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AI Compiler Login</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Consolas, monospace;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #020617, #0f172a);
    display: flex;
    justify-content: center;
    align-items: center;
    color: #e5e7eb;
}

.container {
    background: #020617;
    border: 1px solid #1e293b;
    border-radius: 12px;
    padding: 40px 50px;
    width: 350px;
    box-shadow: 0 0 20px rgba(34,197,94,0.2);
}

h1 {
    text-align: center;
    color: #22c55e;
    margin-bottom: 30px;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #1e293b;
    background: #0f172a;
    color: #e5e7eb;
}

button {
    width: 100%;
    padding: 12px;
    background: #22c55e;
    color: #020617;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #16a34a;
}

.error {
    color: #ef4444;
    font-size: 13px;
    margin-bottom: 10px;
    text-align: center;
}

.link {
    margin-top: 12px;
    text-align: center;
    font-size: 13px;
}

.link a {
    color: #22c55e;
    text-decoration: none;
}
</style>
</head>
<body>

<div class="container">
    <h1>Login</h1>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="link">
        Don’t have an account? <a href="/register">Register</a>
    </div>
</div>

</body>
</html>
