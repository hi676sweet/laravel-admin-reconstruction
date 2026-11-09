<div class="table-scroll"><table><thead><tr><th scope="col">Ticket / customer</th><th scope="col">Priority</th><th scope="col">Status</th><th scope="col">Owner</th><th scope="col">Updated</th></tr></thead><tbody>
@forelse($tickets as $item)<tr><td><a class="ticket-subject" href="{{ route('tickets.show',$item) }}"><small>{{ $item->reference() }}</small>{{ $item->subject }}</a><span class="table-secondary">{{ $item->customer->name }}</span></td>
<td><span class="priority {{ $item->priority->value }}"><i></i>{{ $item->priority->label() }}</span></td>
<td><span class="badge {{ $item->status->value }}">{{ $item->status->label() }}</span></td>
<td><span class="owner"><span class="avatar tiny">{{ $item->assignee ? substr($item->assignee->name,0,1) : '—' }}</span>{{ $item->assignee?->name ?? 'Unassigned' }}</span></td>
<td class="table-secondary"><time datetime="{{ $item->updated_at->toIso8601String() }}">{{ $item->updated_at->diffForHumans() }}</time></td></tr>
@empty<tr><td colspan="5"><div class="empty-state"><span>◎</span><h3>A little breathing room.</h3><p>No tickets match this view.</p><a href="{{ route('tickets.index') }}">Reset your filters</a></div></td></tr>@endforelse
</tbody></table></div>

