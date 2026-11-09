@extends('layouts.app')
@section('title','New ticket')
@section('content')
<div class="heading"><div><a class="text-link" href="{{ route('tickets.index') }}">← Back to tickets</a><h1>Make the next step clear.</h1><p>Capture a customer request and give your team the context.</p></div></div>
<div class="detail-grid"><section class="panel form-panel"><h2>New customer request</h2><p class="muted">Fields marked * are required.</p><form method="post" action="{{ route('tickets.store') }}">@csrf @include('tickets._fields')<div class="form-actions"><a class="button secondary" href="{{ route('tickets.index') }}">Cancel</a><button class="button primary">Create ticket →</button></div></form></section>
<aside class="panel helpful"><span class="eyebrow">A GOOD HANDOFF</span><h2>A little context goes a long way.</h2><p>Describe what happened, what the customer expected, and what you've already tried.</p><hr><h3>What happens next?</h3><p>New tickets start as Open. Assign an operator and move to In progress when the team starts work.</p><p>Waiting keeps a request visible while you await a reply. Resolving requires an owner and a resolution note.</p></aside></div>
@endsection

