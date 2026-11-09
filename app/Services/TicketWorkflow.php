<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketWorkflow
{
    /** @param array<string, mixed> $data */
    public function create(array $data, User $actor): Ticket
    {
        return DB::transaction(function () use ($data, $actor): Ticket {
            $ticket = Ticket::create(Arr::only($data, ['customer_id', 'assignee_id', 'subject', 'description', 'priority']) + ['status' => TicketStatus::Open]);
            $ticket->events()->create(['actor_id' => $actor->id, 'summary' => 'Ticket created']);

            return $ticket;
        });
    }

    /** @param array<string, mixed> $data */
    public function update(Ticket $ticket, array $data, User $actor): void
    {
        DB::transaction(function () use ($ticket, $data, $actor): void {
            $ticket->refresh();
            if ($ticket->version !== (int) $data['version']) {
                throw ValidationException::withMessages(['version' => 'Someone updated this ticket. Reload the page before saving again.']);
            }

            $nextStatus = TicketStatus::from($data['status']);
            if ($ticket->status !== $nextStatus && ! in_array($nextStatus, $ticket->status->transitions(), true)) {
                throw ValidationException::withMessages(['status' => 'This status change is not allowed. Start work or wait for a reply before resolving.']);
            }
            if (in_array($nextStatus, [TicketStatus::InProgress, TicketStatus::Resolved], true) && empty($data['assignee_id'])) {
                throw ValidationException::withMessages(['assignee_id' => 'Assign an operator before starting work or resolving the ticket.']);
            }
            if ($nextStatus === TicketStatus::Resolved && trim($data['resolution'] ?? '') === '') {
                throw ValidationException::withMessages(['resolution' => 'Describe the resolution before resolving this ticket.']);
            }

            $changes = [];
            foreach (['subject', 'description', 'priority', 'customer_id', 'assignee_id', 'status', 'resolution'] as $field) {
                $old = $ticket->getRawOriginal($field);
                $new = $data[$field] ?? null;
                if ((string) $old !== (string) $new) {
                    $changes[] = str_replace('_', ' ', ucfirst($field)).' updated';
                }
            }

            $updates = Arr::only($data, ['customer_id', 'assignee_id', 'subject', 'description', 'priority', 'status']);
            $updates['resolution'] = $nextStatus === TicketStatus::Resolved ? $data['resolution'] : null;
            $updates['resolved_at'] = $nextStatus === TicketStatus::Resolved ? ($ticket->resolved_at ?? now()) : null;
            $updates['version'] = $ticket->version + 1;

            $written = Ticket::query()->whereKey($ticket->id)->where('version', $ticket->version)->update($updates);
            if ($written !== 1) {
                throw ValidationException::withMessages(['version' => 'This ticket changed while you were saving. Reload and try again.']);
            }

            $eventNote = $data['note'] ?? null;
            if ($nextStatus === TicketStatus::Resolved && ($ticket->status !== TicketStatus::Resolved || $ticket->resolution !== $data['resolution'])) {
                $eventNote = 'Resolution: '.$data['resolution'].($eventNote ? "\n\n".$eventNote : '');
            }

            $ticket->events()->create([
                'actor_id' => $actor->id,
                'summary' => $changes === [] ? 'Ticket reviewed' : implode(' · ', $changes),
                'note' => $eventNote,
            ]);
        });
    }
}
