<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign in · Opsdesk</title><link rel="stylesheet" href="{{ asset('opsdesk.css') }}"></head>
<body class="login-page"><main class="login-card">
<a class="brand dark-brand" href="{{ route('login') }}"><span class="brand-mark">o</span>opsdesk<span class="brand-dot">.</span></a>
<span class="eyebrow">A LITTLE ORDER. A LOT OF CLARITY.</span><h1>Welcome to your<br>team's working space.</h1>
<p class="lead">Keep customer requests moving, from first conversation to a thoughtful resolution.</p>
@if($errors->any())<div class="notice error" role="alert">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ route('login.store') }}">@csrf
<label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email','demo@example.test') }}" required autocomplete="username">
<label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password">
<button class="button primary full-width">Sign in to workspace <span>→</span></button></form>
<div class="demo-credentials"><strong>Explore the local demo</strong><span>Email: demo@example.test<br>Default password: <code>opsdesk-demo-2026</code></span></div>
<p class="fine-print">Independent portfolio reconstruction. All customers and tickets are fictional. Use only in a trusted local environment.</p></main>
<div class="login-art" aria-hidden="true"><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><div class="art-card"><span class="live-dot"></span>One workspace. Fewer loose ends.<div class="art-lines"><i></i><i></i><i></i></div><span class="badge resolved">Resolved, with care</span></div></div></body></html>

