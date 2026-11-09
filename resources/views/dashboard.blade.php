@extends('layouts.app')
@section('title','Overview')
@section('content')
<div class="heading"><div><span class="eyebrow">{{ now()->format('l, F j') }}</span><h1>A clear view of the day.</h1><p>Here's what's happening across your customer workspace.</p></div><a class="button primary" href="{{ route('tickets.create') }}">＋ New ticket</a></div>
<div class="stats">
<a class="stat" href="{{ route('tickets.index',['active'=>1]) }}"><div>Active requests<span class="stat-icon teal">↗</span></div><strong>{{ $activeCount }}</strong><small>Across your customer accounts</small></a>
<a class="stat" href="{{ route('tickets.index',['priority'=>'urgent','active'=>1]) }}"><div>Urgent attention<span class="stat-icon coral">!</span></div><strong>{{ $urgentCount }}</strong><small>Unresolved, highest priority</small></a>
<a class="stat" href="{{ route('tickets.index',['status'=>'waiting']) }}"><div>Waiting for a reply<span class="stat-icon amber">◷</span></div><strong>{{ $waitingCount }}</strong><small>Keep the conversation going</small></a>
<a class="stat" href="{{ route('tickets.index',['status'=>'resolved']) }}"><div>Resolved this week<span class="stat-icon green">✓</span></div><strong>{{ $resolvedCount }}</strong><small>In the last seven days</small></a>
</div><div class="overview-grid">
<section class="panel"><div class="panel-heading"><div><h2>Requests in motion</h2><p>The most recently updated active tickets.</p></div><a class="text-link" href="{{ route('tickets.index') }}">View all tickets →</a></div>@include('tickets._table')</section>
<aside class="panel pipeline"><div class="panel-heading"><div><h2>The bigger picture</h2><p>Your full ticket pipeline.</p></div></div>
@foreach(\App\Enums\TicketStatus::cases() as $status)<div class="pipeline-row"><div><span class="status-dot {{ $status->value }}"></span>{{ $status->label() }}<strong>{{ $statusCounts[$status->value] ?? 0 }}</strong></div><div class="progress-track"><span class="{{ $status->value }}" style="width:{{ (($statusCounts[$status->value] ?? 0)/max(1,$statusCounts->sum()))*100 }}%"></span></div></div>@endforeach
<div class="calm-note"><span>✦</span><h3>Small steps. Better service.</h3><p>Assign an owner, leave a useful note, and make the next step clear.</p><a class="text-link" href="{{ route('tickets.index',['status'=>'open']) }}">Find the next request →</a></div></aside></div>
@endsection

