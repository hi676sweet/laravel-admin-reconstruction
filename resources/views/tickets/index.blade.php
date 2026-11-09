@extends('layouts.app')
@section('title','Tickets')
@section('content')
<div class="heading"><div><span class="eyebrow">CUSTOMER OPERATIONS</span><h1>Every request, in one place.</h1><p>Find the details, set the priority, and keep things moving.</p></div><a class="button primary" href="{{ route('tickets.create') }}">＋ New ticket</a></div>
<section class="panel"><form class="filters" action="{{ route('tickets.index') }}" method="get">
<div class="search-field"><label class="sr-only" for="q">Search tickets</label><input id="q" name="q" placeholder="Search subject, customer, or OPS-0001..." value="{{ $filters['q'] ?? '' }}" maxlength="100"></div>
<div><label class="sr-only" for="status">Status</label><select id="status" name="status"><option value="">All statuses</option>@foreach($statuses as $status)<option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>@endforeach</select></div>
<div><label class="sr-only" for="priority">Priority</label><select id="priority" name="priority"><option value="">All priorities</option>@foreach($priorities as $priority)<option value="{{ $priority->value }}" @selected(($filters['priority'] ?? '') === $priority->value)>{{ $priority->label() }}</option>@endforeach</select></div>
<div><label class="sr-only" for="assignee_id">Owner</label><select id="assignee_id" name="assignee_id"><option value="">All owners</option>@foreach($agents as $agent)<option value="{{ $agent->id }}" @selected((string)($filters['assignee_id'] ?? '') === (string)$agent->id)>{{ $agent->name }}</option>@endforeach</select></div>
@if(!empty($filters['customer_id']))<input type="hidden" name="customer_id" value="{{ $filters['customer_id'] }}">@endif
@if(!empty($filters['active']))<input type="hidden" name="active" value="1">@endif
<button class="button secondary">Apply</button><a class="text-link" href="{{ route('tickets.index') }}">Reset</a></form>
<div class="list-meta">{{ $tickets->total() }} {{ Str::plural('ticket',$tickets->total()) }}@if(!empty($filters['customer_id'])) · Filtered by customer @endif</div>
@include('tickets._table')<div class="pagination">{{ $tickets->links('pagination') }}</div></section>
@endsection

