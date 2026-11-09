<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operator = User::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->actingAs($this->operator);
    }

    /** @return array<string, mixed> */
    private function payload(?Ticket $ticket = null): array
    {
        return [
            'customer_id' => $ticket?->customer_id ?? $this->customer->id,
            'subject' => $ticket?->subject ?? 'A customer cannot export a report',
            'description' => $ticket?->description ?? 'The export returns an empty file when the account has active records.',
            'priority' => $ticket?->priority->value ?? 'normal',
            'assignee_id' => $ticket?->assignee_id,
            'status' => $ticket?->status->value ?? 'open',
            'resolution' => $ticket?->resolution,
            'version' => $ticket?->version ?? 0,
        ];
    }

    public function test_creation_persists_a_new_open_ticket_and_attributes_the_audit_event(): void
    {
        $this->post(route('tickets.store'), $this->payload() + ['unexpected' => 'ignored'])
            ->assertRedirect();
        $ticket = Ticket::sole();
        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertSame($this->customer->id, $ticket->customer_id);
        $this->assertSame($this->operator->id, $ticket->events()->sole()->actor_id);
        $this->get(route('tickets.show', $ticket))->assertOk()->assertSee($ticket->subject);
    }

    public function test_creation_rejects_unknown_customers_and_incomplete_requests_without_writing_rows(): void
    {
        $this->post(route('tickets.store'), array_replace($this->payload(), [
            'customer_id' => 99999, 'subject' => 'Bad', 'description' => '', 'priority' => 'critical',
        ]))->assertSessionHasErrors(['customer_id', 'subject', 'description', 'priority']);
        $this->assertDatabaseCount('tickets', 0);
        $this->assertDatabaseCount('ticket_events', 0);
    }

    public function test_resolving_requires_a_valid_transition_an_owner_and_a_resolution_then_can_be_reopened(): void
    {
        $ticket = Ticket::factory()->create(['customer_id' => $this->customer->id]);
        $this->patch(route('tickets.update', $ticket), array_replace($this->payload($ticket), ['status' => 'resolved', 'resolution' => 'A fix was verified.']))
            ->assertSessionHasErrors('status');
        $this->patch(route('tickets.update', $ticket), array_replace($this->payload($ticket), ['status' => 'in_progress']))
            ->assertSessionHasErrors('assignee_id');
        $this->assertDatabaseCount('ticket_events', 0);

        $this->patch(route('tickets.update', $ticket), array_replace($this->payload($ticket), ['status' => 'in_progress', 'assignee_id' => $this->operator->id]))
            ->assertSessionHasNoErrors()->assertRedirect(route('tickets.show', $ticket));
        $ticket->refresh();
        $this->patch(route('tickets.update', $ticket), array_replace($this->payload($ticket), ['status' => 'resolved']))
            ->assertSessionHasErrors('resolution');
        $this->assertDatabaseCount('ticket_events', 1);

        $this->patch(route('tickets.update', $ticket), array_replace($this->payload($ticket), ['status' => 'resolved', 'resolution' => 'Export corrected and verified against three fixture records.']))
            ->assertSessionHasNoErrors();
        $ticket->refresh();
        $this->assertSame(TicketStatus::Resolved, $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertStringContainsString('Export corrected', $ticket->events()->first()->note);

        $this->patch(route('tickets.update', $ticket), array_replace($this->payload($ticket), ['status' => 'open']))
            ->assertSessionHasNoErrors();
        $ticket->refresh();
        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertNull($ticket->resolved_at);
        $this->assertNull($ticket->resolution);
        $this->assertDatabaseCount('ticket_events', 3);
    }

    public function test_a_stale_editor_cannot_overwrite_a_newer_change(): void
    {
        $ticket = Ticket::factory()->create();
        $stalePayload = $this->payload($ticket);
        $this->patch(route('tickets.update', $ticket), array_replace($stalePayload, ['priority' => 'high']))
            ->assertSessionHasNoErrors();
        $this->from(route('tickets.show', $ticket))->patch(route('tickets.update', $ticket), array_replace($stalePayload, ['description' => 'An old tab should never overwrite the latest revision.']))
            ->assertSessionHasErrors('version');
        $ticket->refresh();
        $this->assertSame('high', $ticket->priority->value);
        $this->assertSame($stalePayload['description'], $ticket->description);
        $this->assertSame(1, $ticket->version);
        $this->assertDatabaseCount('ticket_events', 1);
    }

    public function test_a_conflict_page_requires_reload_before_another_edit(): void
    {
        $ticket = Ticket::factory()->create();
        $errors = (new ViewErrorBag)->put('default', new MessageBag(['version' => 'Someone updated this ticket.']));
        $this->withSession(['errors' => $errors]);
        $this->app['session']->save();
        $this->withCookie(config('session.cookie'), $this->app['session']->getId())->get(route('tickets.show', $ticket))
            ->assertOk()->assertViewHas('conflict', true)->assertSee('Reload the current details')->assertDontSee('Save changes');
    }

    public function test_filters_combine_with_grouped_search_and_paginate(): void
    {
        $match = Ticket::factory()->create(['customer_id' => $this->customer->id, 'subject' => 'Export matching record', 'priority' => 'high']);
        Ticket::factory()->create(['subject' => 'Export wrong priority', 'priority' => 'low']);
        Ticket::factory()->create(['subject' => 'Unrelated high priority ticket', 'priority' => 'high']);
        $this->get(route('tickets.index', ['q' => 'Export', 'priority' => 'high']))
            ->assertOk()->assertSee($match->subject)->assertDontSee('Export wrong priority')->assertDontSee('Unrelated high priority ticket');
        Ticket::factory()->count(15)->create();
        $this->get(route('tickets.index'))->assertOk()->assertViewHas('tickets', fn ($tickets) => $tickets->count() === 12 && $tickets->total() === 18);
        $this->get(route('tickets.index', ['q' => $match->reference()]))->assertOk()->assertViewHas('tickets', fn ($tickets) => $tickets->total() === 1);
    }

    public function test_customer_input_is_escaped_in_the_ui_and_missing_tickets_return_404(): void
    {
        $ticket = Ticket::factory()->create(['subject' => '<script>alert(1)</script>']);
        $this->get(route('tickets.show', $ticket))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/tickets/999999')->assertNotFound();
    }

    public function test_dashboard_excludes_old_resolutions_and_customer_counts_are_correct(): void
    {
        Ticket::factory()->create(['customer_id' => $this->customer->id, 'status' => 'waiting', 'priority' => 'urgent']);
        Ticket::factory()->create(['customer_id' => $this->customer->id, 'status' => 'resolved', 'resolved_at' => now()->subDays(10)]);
        Ticket::factory()->create(['status' => 'resolved', 'resolved_at' => now()->subDay()]);
        $this->get(route('dashboard'))->assertOk()->assertViewHas('activeCount', 1)->assertViewHas('urgentCount', 1)->assertViewHas('resolvedCount', 1);
        $this->get(route('customers.index', ['q' => $this->customer->name]))->assertOk()->assertViewHas('customers', fn ($customers) => $customers->first()->active_tickets_count === 1);
        $this->get(route('tickets.index', ['active' => 1]))->assertViewHas('tickets', fn ($tickets) => $tickets->total() === 1);
    }

    public function test_put_updates_are_validated_like_patch_updates(): void
    {
        $ticket = Ticket::factory()->create();
        $this->put(route('tickets.update', $ticket), ['subject' => 'Missing the rest'])
            ->assertSessionHasErrors(['status', 'version', 'description']);
        $this->assertSame(0, $ticket->refresh()->version);
    }
}
