@extends('layouts.app')
@section('title',$ticket->reference())
@section('content')
<div class="heading"><div><a class="text-link" href="{{ route('tickets.index') }}">← Back to tickets</a><div class="ticket-heading"><span class="eyebrow">{{ $ticket->reference() }}</span><span class="badge {{ $ticket->status->value }}">{{ $ticket->status->label() }}</span></div><h1 class="ticket-title">{{ $ticket->subject }}</h1><p>{{ $ticket->customer->name }} · Opened {{ $ticket->created_at->format('M j, Y') }}</p></div></div>
<div class="detail-grid"><section class="panel form-panel"><h2>Request details</h2><p class="muted">Keep the owner, status, and customer context up to date.</p>
@if(!$conflict)<form method="post" action="{{ route('tickets.update',$ticket) }}">@csrf @method('PATCH')<input type="hidden" name="version" value="{{ $ticket->version }}">@include('tickets._fields')
<div class="field"><label for="resolution">Resolution <small>Required when resolving</small></label><textarea id="resolution" name="resolution" maxlength="4000" rows="3" placeholder="What changed, and how was it verified?">{{ old('resolution',$ticket->resolution) }}</textarea></div>
<div class="field"><label for="note">Internal activity note <small>Optional</small></label><textarea id="note" name="note" maxlength="2000" rows="3" placeholder="Leave a helpful update for the next operator.">{{ old('note') }}</textarea></div>
<p class="fine-print">Resolve after work has started or a reply has been requested. Reopen a resolved request by moving it back to Open.</p><div class="form-actions"><button class="button primary">Save changes</button></div></form>@else<p class="muted">This ticket has a newer revision. <a class="text-link" href="{{ route('tickets.show',$ticket) }}">Reload the current details →</a></p>@endif</section>
<aside><section class="panel activity"><div class="panel-heading"><div><h2>Activity trail</h2><p>A shared record of what happened.</p></div></div>
@forelse($ticket->events as $event)<article class="activity-item"><span class="avatar tiny">{{ $event->actor ? substr($event->actor->name,0,1) : 'S' }}</span><div><strong>{{ $event->actor?->name ?? 'System' }}</strong><time datetime="{{ $event->created_at->toIso8601String() }}">{{ $event->created_at->diffForHumans() }}</time><p>{{ $event->summary }}</p>@if($event->note)<blockquote>{{ $event->note }}</blockquote>@endif</div></article>@empty<p class="muted pad">No activity yet.</p>@endforelse
</section><div class="customer-note"><span class="eyebrow">CUSTOMER CONTACT</span><h3>{{ $ticket->customer->contact_name }}</h3><p>{{ $ticket->customer->email }}</p><a class="text-link" href="{{ route('tickets.index',['customer_id'=>$ticket->customer_id]) }}">All customer requests →</a></div></aside></div>
@endsection

