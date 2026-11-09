@extends('layouts.app')
@section('title','Customers')
@section('content')
<div class="heading"><div><span class="eyebrow">THE PEOPLE BEHIND THE REQUESTS</span><h1>A little more context.</h1><p>Find your customer accounts and their open conversations.</p></div></div>
<form class="customer-search" method="get" action="{{ route('customers.index') }}"><label class="sr-only" for="q">Search customers</label><input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Search customer name..."><button class="button secondary">Search</button><a class="text-link" href="{{ route('customers.index') }}">Reset</a></form>
<div class="customer-grid">@forelse($customers as $customer)<article class="panel customer-card"><span class="customer-avatar">{{ substr($customer->name,0,1) }}</span><span class="plan">{{ $customer->plan }}</span><h2>{{ $customer->name }}</h2><p>{{ $customer->contact_name }}<br><span class="muted">{{ $customer->email }}</span></p><div class="customer-metrics"><span><strong>{{ $customer->active_tickets_count }}</strong> active requests</span><span>{{ $customer->tickets_count }} total</span></div><a class="text-link" href="{{ route('tickets.index',['customer_id'=>$customer->id]) }}">View conversations →</a></article>@empty<div class="panel empty-state"><h2>No customers found.</h2><p>Try a different name.</p></div>@endforelse</div>
<div class="pagination">{{ $customers->links('pagination') }}</div>
@endsection

