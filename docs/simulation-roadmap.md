# Opsdesk: workbook roadmap and source map

**Synthetic planning reference. No historical Git data or completed-work record is asserted.**

This document imports Opsdesk's planning allocation from `portfolio_daily_activity_simulation_2023_2026.xlsx`. The workbook labels its dates and slots as a simulation. This repository is a new November 2026 reconstruction; its Git author and committer dates retain their actual values.

The supplied resume reports LET'S DO YOUR PLAN employment from August 2023 through March 2026. The workbook's narrower project allocations are hypothetical planning choices. They do not establish an individual assignment or an actual development period.

## Workbook allocation

| Field | Simulation value |
| --- | --- |
| Allocation window | 2023-08-01 through 2024-03-29 |
| Planned weekdays | 174 |
| Slots per planned weekday | 6 |
| Synthetic planned slots | 1044 |
| Time zone | JST, UTC+09:00 |
| Workbook status | Synthetic plan |

Slot counts describe the workbook's model. They are not counts of commits created, contributions made, or completed engineering tasks.

## Monthly roadmap

The following months are simulation labels copied from the workbook. The topic list is indicative; it does not mark those topics as historically completed.

| Simulation month | Planned weekdays | Indicative workbook topics |
| --- | ---: | --- |
| 2023-08 | 23 | operational requirements; customer records; SQLite setup; operator roles |
| 2023-09 | 21 | operator assignments; migration fixtures; ticket priorities; ticket status schema |
| 2023-10 | 22 | status transitions; ticket lifecycle service; assignment rules; resolution rules |
| 2023-11 | 22 | ticket updates; status transitions; ticket lifecycle service; assignment rules |
| 2023-12 | 21 | responsive Blade templates; priority filters; pagination; status filters |
| 2024-01 | 23 | priority filters; pagination; optimistic version checks; ticket validation |
| 2024-02 | 21 | escaped customer content; optimistic version checks; login rate limits; negative validation |
| 2024-03 | 21 | stale edit conflicts; end-to-end ticket workflow; negative validation; empty states |

## Phase allocation

| Planned phase | Simulated weekdays |
| --- | ---: |
| Architecture & scope | 17 |
| Core data model | 27 |
| Primary workflows | 41 |
| Search & usability | 26 |
| Integrity & security | 25 |
| Regression testing | 21 |
| Polish & documentation | 17 |

## Feature-to-source map

Use these links to inspect how the current demo addresses the workbook's themes. Test links identify existing regression sources; this document does not certify every generic workbook objective or substitute for running the checks.

| Workbook topics | Current implementation | Existing regression sources |
| --- | --- | --- |
| Customer records, ticket fields and references | [`app/Models/Customer.php`](../app/Models/Customer.php); [`app/Models/Ticket.php`](../app/Models/Ticket.php); [`database/migrations`](../database/migrations) | [`tests/Feature/TicketWorkflowTest.php`](../tests/Feature/TicketWorkflowTest.php) |
| Assignment, resolution, reopening and status rules | [`app/Services/TicketWorkflow.php`](../app/Services/TicketWorkflow.php); [`app/Enums/TicketStatus.php`](../app/Enums/TicketStatus.php) | [`tests/Feature/TicketWorkflowTest.php`](../tests/Feature/TicketWorkflowTest.php) |
| Inbox search, priority/owner filters and dashboard | [`app/Http/Controllers/TicketController.php`](../app/Http/Controllers/TicketController.php); [`resources/views/tickets`](../resources/views/tickets); [`resources/views/dashboard.blade.php`](../resources/views/dashboard.blade.php) | [`tests/Feature/TicketWorkflowTest.php`](../tests/Feature/TicketWorkflowTest.php) |
| Optimistic edits, validation and transactional events | [`app/Services/TicketWorkflow.php`](../app/Services/TicketWorkflow.php); [`app/Http/Requests/TicketRequest.php`](../app/Http/Requests/TicketRequest.php); [`app/Models/TicketEvent.php`](../app/Models/TicketEvent.php) | [`tests/Feature/TicketWorkflowTest.php`](../tests/Feature/TicketWorkflowTest.php) |
| Operator login, session controls and rate limits | [`app/Http/Controllers/AuthController.php`](../app/Http/Controllers/AuthController.php); [`routes/web.php`](../routes/web.php) | [`tests/Feature/AuthenticationTest.php`](../tests/Feature/AuthenticationTest.php) |
| Forms, empty states and responsive presentation | [`resources/views`](../resources/views); [`public/opsdesk.css`](../public/opsdesk.css) | [`tests/Feature/TicketWorkflowTest.php`](../tests/Feature/TicketWorkflowTest.php) |

## Review boundaries

Operators share one workspace. The workbook's operator-role topic does not establish a separate role-administration or customer-login feature. There is no email delivery or attachment workflow.

The workbook includes broad polish, accessibility and review topics. There is no formal accessibility certification or performance result attached to this roadmap. Run the checks documented in the [README](../README.md) and inspect the linked cases before making new coverage claims.

## Source traceability

- Workbook: `portfolio_daily_activity_simulation_2023_2026.xlsx`.
- SHA-256: `2603bf41d899d5216e935309785acdd4b4748bccf722b1b5fd2e5161b621b9de`.
- Imported sheets: Overview, Daily Plan, Commit Schedule, Monthly Roadmap, and Repository Notes.
- Machine-readable summary: [simulation-source.json](simulation-source.json).
- Documentation prepared: November 10, 2026, Asia/Tokyo.

Only planning summaries and source references are imported. No scheduled commit messages are executed, no artificial commit sequence is generated, and the workbook does not control repository history.
