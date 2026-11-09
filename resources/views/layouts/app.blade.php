<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title','Overview') · Opsdesk</title><link rel="stylesheet" href="{{ asset('opsdesk.css') }}"></head>
<body><a class="skip" href="#main">Skip to content</a><div class="app-shell">
<aside class="sidebar">
<a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">o</span>opsdesk<span class="brand-dot">.</span></a>
<p class="workspace-label">WORKSPACE</p><div class="workspace"><span class="workspace-icon">F</span><div>Fieldnotes team<small>Demo workspace</small></div></div>
<nav aria-label="Main navigation">
<a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span aria-hidden="true">◫</span>Overview</a>
<a class="{{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}"><span aria-hidden="true">▤</span>Tickets</a>
<a class="{{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><span aria-hidden="true">◎</span>Customers</a>
</nav>
<div class="sidebar-footer"><span class="live-dot"></span>Synthetic data only<p>An independent portfolio reconstruction.</p></div>
<div class="profile"><span class="avatar">{{ substr(auth()->user()->name,0,1) }}</span><div>{{ auth()->user()->name }}<small>Workspace operator</small></div><form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form></div>
</aside><div class="main-shell"><header class="topbar"><span>Workspace <span class="separator">/</span><strong>@yield('title','Overview')</strong></span><span class="demo-tag">PORTFOLIO DEMO</span><form class="mobile-signout" method="post" action="{{ route('logout') }}">@csrf<button class="button secondary">Sign out</button></form></header>
<main id="main">@if(session('success'))<div class="notice success" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="notice error" role="alert"><strong>Please check the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@error('version')<a href="{{ url()->current() }}">Reload current ticket</a>@enderror</div>@endif
@yield('content')</main>
<footer class="page-footer">Built for the little details that keep a team moving.<span>Opsdesk · Independent reconstruction</span></footer></div></div></body></html>

