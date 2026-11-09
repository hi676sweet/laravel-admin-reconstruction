<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::firstOrCreate(['email' => 'demo@example.test'], ['name' => 'Alex Morgan', 'password' => config('opsdesk.demo_password')]);
        $secondOperator = User::firstOrCreate(['email' => 'casey@example.test'], ['name' => 'Casey Rivera', 'password' => config('opsdesk.demo_password')]);
        $customers = collect([
            ['name' => 'Cedar & Co.', 'contact_name' => 'Jordan Lee', 'email' => 'jordan@cedar.example.test', 'plan' => 'Scale'],
            ['name' => 'Northstar Studio', 'contact_name' => 'Sam Taylor', 'email' => 'sam@northstar.example.test', 'plan' => 'Growth'],
            ['name' => 'Fieldwork Supply', 'contact_name' => 'Robin Patel', 'email' => 'robin@fieldwork.example.test', 'plan' => 'Scale'],
            ['name' => 'Luma Workshop', 'contact_name' => 'Jamie Chen', 'email' => 'jamie@luma.example.test', 'plan' => 'Starter'],
        ])->map(fn (array $data) => Customer::firstOrCreate(['email' => $data['email']], $data));
        $fixtures = [
            ['Checkout confirmation email not arriving', 'urgent', 'in_progress'],
            ['Exported report includes duplicate rows', 'high', 'open'],
            ['Update billing contact for next invoice', 'normal', 'waiting'],
            ['Invite a new team member', 'low', 'resolved'],
            ['Dashboard totals differ from CSV export', 'high', 'in_progress'],
            ['Unable to update delivery instructions', 'normal', 'open'],
            ['Account access after email change', 'urgent', 'waiting'],
            ['Add a second warehouse location', 'normal', 'open'],
            ['Product images appear out of order', 'low', 'resolved'],
            ['Subscription renewal date clarification', 'normal', 'waiting'],
            ['Search results missing archived orders', 'high', 'in_progress'],
            ['Customer profile timezone preference', 'low', 'open'],
            ['Download receipt as PDF', 'normal', 'resolved'],
            ['Import validation error on empty rows', 'high', 'open'],
        ];
        foreach ($fixtures as $index => [$subject, $priority, $status]) {
            $ticket = Ticket::firstOrCreate(['subject' => $subject], [
                'customer_id' => $customers[$index % 4]->id,
                'assignee_id' => $status === 'open' ? null : ($index % 2 ? $secondOperator->id : $operator->id),
                'description' => 'Fictional demo request: '.$subject.'. Please investigate the reported workflow, verify the expected behavior, and share an update with the customer.',
                'priority' => $priority,
                'status' => $status,
                'resolution' => $status === 'resolved' ? 'The requested change was verified with the synthetic customer.' : null,
                'resolved_at' => $status === 'resolved' ? now()->subDays(2) : null,
                'created_at' => now()->subDays($index + 1),
                'updated_at' => now()->subMinutes($index * 47),
            ]);
            if ($ticket->wasRecentlyCreated) {
                $ticket->events()->create(['actor_id' => $operator->id, 'summary' => 'Synthetic demo ticket imported', 'note' => 'This is fictional portfolio data.']);
            }
        }
    }
}
