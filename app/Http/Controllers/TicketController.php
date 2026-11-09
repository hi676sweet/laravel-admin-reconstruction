<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\TicketRequest;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard', [
            'activeCount' => Ticket::where('status', '!=', 'resolved')->count(),
            'urgentCount' => Ticket::where('status', '!=', 'resolved')->where('priority', 'urgent')->count(),
            'waitingCount' => Ticket::where('status', 'waiting')->count(),
            'resolvedCount' => Ticket::where('resolved_at', '>=', now()->subDays(7))->count(),
            'tickets' => Ticket::with(['customer', 'assignee'])->where('status', '!=', 'resolved')->latest('updated_at')->limit(6)->get(),
            'statusCounts' => Ticket::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
            'priority' => ['nullable', Rule::enum(TicketPriority::class)],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'assignee_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'active' => ['nullable', 'boolean'],
        ]);
        $query = Ticket::with(['customer', 'assignee'])->latest('updated_at')->latest('id');
        if (! empty($filters['active'])) {
            $query->where('status', '!=', 'resolved');
        }
        foreach (['status', 'priority', 'customer_id', 'assignee_id'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (! empty($filters['q'])) {
            $term = $filters['q'];
            $query->where(function (Builder $search) use ($term): void {
                $search->where('subject', 'like', '%'.$term.'%')
                    ->orWhereHas('customer', fn (Builder $customer) => $customer->where('name', 'like', '%'.$term.'%'));
                if (preg_match('/^(?:OPS-)?0*(\d+)$/i', $term, $matches)) {
                    $search->orWhere('id', (int) $matches[1]);
                }
            });
        }

        return view('tickets.index', $this->formData() + ['tickets' => $query->paginate(12)->withQueryString(), 'filters' => $filters]);
    }

    public function create(): View
    {
        return view('tickets.create', $this->formData() + ['ticket' => new Ticket(['priority' => TicketPriority::Normal])]);
    }

    public function store(TicketRequest $request, TicketWorkflow $workflow): RedirectResponse
    {
        $ticket = $workflow->create($request->validated(), $request->user());

        return to_route('tickets.show', $ticket)->with('success', 'Ticket created. Your team can take it from here.');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['customer', 'assignee', 'events.actor']);

        return view('tickets.show', $this->formData() + ['ticket' => $ticket, 'conflict' => session('errors')?->has('version') ?? false]);
    }

    public function update(TicketRequest $request, Ticket $ticket, TicketWorkflow $workflow): RedirectResponse
    {
        $workflow->update($ticket, $request->validated(), $request->user());

        return to_route('tickets.show', $ticket)->with('success', 'Ticket updated and activity recorded.');
    }

    public function customers(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $customers = Customer::withCount(['tickets', 'tickets as active_tickets_count' => fn (Builder $query) => $query->where('status', '!=', 'resolved')])
            ->when($filters['q'] ?? null, fn (Builder $query, string $term) => $query->where('name', 'like', '%'.$term.'%'))
            ->orderBy('name')->paginate(12)->withQueryString();

        return view('customers.index', compact('customers', 'filters'));
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        return ['customers' => Customer::orderBy('name')->get(), 'agents' => User::orderBy('name')->get(), 'statuses' => TicketStatus::cases(), 'priorities' => TicketPriority::cases()];
    }
}
