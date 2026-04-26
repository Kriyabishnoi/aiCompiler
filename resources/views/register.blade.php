<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AI Compiler Sign Up</title>
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

form {
    display: flex;
    flex-direction: column;
}

input {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #1e293b;
    background: #0f172a;
    color: #e5e7eb;
    font-size: 14px;
}

input::placeholder {
    color: #94a3b8;
}

button {
    padding: 12px;
    background: #22c55e;
    color: #020617;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #16a34a;
}

.footer {
    text-align: center;
    margin-top: 15px;
    font-size: 12px;
    color: #64748b;
}

.login-link {
    text-align: center;
    margin-top: 10px;
    font-size: 13px;
    color: #94a3b8;
}

.login-link a {
    color: #22c55e;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
    <h1>AI Compiler Sign Up</h1>
    @if ($errors->any())
    <div style="color:red; margin-bottom:15px;">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST">
    @csrf
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
    <button type="submit">Sign Up</button>
</form>

    <div class="login-link">
        Already have an account? <a href="/login">Login</a>
    </div>
    <div class="footer">
        Built with Laravel • AI • Secure Compiler
    </div>
</div>

</body>
</html>
