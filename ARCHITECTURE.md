# Architecture Guide

> Service Marketplace — Laravel Application Architecture

This document describes the layered architecture of the marketplace codebase, the responsibilities of each layer, and the conventions for keeping logic in the right place.

---

## Directory Structure

```text
app/
├── Actions/                    # Single-purpose business operations
│   ├── Connection/
│   │   ├── CreateConnectionRequest.php
│   │   ├── AcceptConnectionRequest.php
│   │   └── DeclineConnectionRequest.php
│   └── Payment/
│       ├── InitializeConnectionPayment.php
│       └── VerifyAndActivatePayment.php
│
├── Contracts/                  # Interfaces for external integrations
│   └── PaymentGateway.php
│
├── DataTransferObjects/        # Immutable value objects (DTOs)
│   ├── CreateConnectionRequestData.php
│   ├── PaymentInitiationData.php
│   └── PaymentVerificationResult.php
│
├── Enums/                      # PHP backed enums for statuses/types
│   ├── ConnectionStatus.php
│   ├── ConnectionType.php
│   ├── OpportunityStatus.php
│   ├── PaymentStatus.php
│   └── TeachingMode.php
│
├── Events/                     # Domain events
│   ├── ConnectionRequestCreated.php
│   ├── ConnectionRequestAccepted.php
│   ├── ConnectionRequestDeclined.php
│   └── ConnectionActivated.php
│
├── Exceptions/                 # Domain-specific exceptions
│   ├── DuplicateConnectionException.php
│   └── InvalidConnectionTransitionException.php
│
├── Http/
│   └── Controllers/            # Thin controllers (web + future API)
│
├── Listeners/                  # Event listeners for side effects
│   ├── SendConnectionRequestNotification.php
│   └── SendConnectionActivatedNotification.php
│
├── Livewire/                   # UI state management components
│
├── Models/                     # Eloquent models (relationships + lightweight domain)
│
├── Policies/                   # Authorization logic
│   ├── ConnectionRequestPolicy.php
│   ├── ConversationPolicy.php
│   └── OpportunityPolicy.php
│
├── Providers/
│   ├── AppServiceProvider.php  # Contract bindings
│   └── EventServiceProvider.php
│
└── Services/                   # Query services + orchestration
    ├── ChatService.php
    ├── OpportunityDiscoveryService.php
    ├── ProfessionalDiscoveryService.php
    └── PaymentProviders/
        └── PaystackGateway.php # Paystack-specific implementation
```

---

## Layer Responsibilities

### Livewire Components (`app/Livewire/`)

Livewire components are the **UI boundary**. They should:

- manage **UI state** (form inputs, loading flags, modals, pagination);
- authorize the current user via Policies before calling Actions;
- call Actions or Services to perform business logic;
- handle validation using Form Requests or `$this->validate()`;
- dispatch browser events for UI feedback (toasts, redirects);
- map Action results to view data.

Livewire components should **NOT**:

- contain business rules (e.g. connection state transitions);
- directly query the database for complex multi-join logic;
- call external APIs or payment providers;
- perform multi-step operations without delegating to an Action.

```php
// ✅ Good — Livewire delegates to Action
public function accept(int $connectionRequestId): void
{
    $this->authorize('accept', $connectionRequest);

    app(AcceptConnectionRequest::class)->execute($connectionRequestId, auth()->id());

    $this->dispatch('connection-accepted');
}

// ❌ Bad — business logic lives in Livewire
public function accept(int $connectionRequestId): void
{
    $request = ConnectionRequest::findOrFail($connectionRequestId);
    $request->status = 'payment_pending';
    $request->accepted_at = now();
    $request->save();
    $request->initiator->notify(new PaymentRequiredNotification($request));
}
```

### Actions (`app/Actions/`)

Actions are **single-purpose business operations**. They should:

- encapsulate one complete business operation (e.g. "accept a connection request");
- enforce business invariants (e.g. "cannot connect to yourself");
- wrap multi-step mutations in `DB::transaction()`;
- dispatch domain Events for secondary side effects;
- accept DTOs or simple typed parameters;
- return the resulting model or a DTO.

Actions should **NOT**:

- handle HTTP concerns (request parsing, response formatting);
- manage UI state;
- directly send notifications (dispatch an Event instead);
- depend on the web framework (no `request()`, no `session()`).

**Why Actions and not Service methods?** Actions are individually injectable, testable, and nameable. A `ConnectionService` with 8 methods becomes a god class. An `AcceptConnectionRequest` action is self-documenting and can be tested in isolation.

### Services (`app/Services/`)

Services are for **query logic** and **orchestration** that doesn't fit a single Action:

- complex search/filter queries shared across multiple callers;
- read-only data retrieval with joins and aggregations;
- coordinating multiple Actions when needed (rare);
- chat message persistence and broadcasting.

Services should **NOT**:

- duplicate logic that belongs in a single Action;
- handle authorization (that's a Policy concern);
- grow into god classes with dozens of methods.

### Controllers (`app/Http/Controllers/`)

Controllers are **thin HTTP adapters**. They should:

- parse HTTP input;
- call Policies for authorization;
- delegate to Actions or Services;
- return HTTP responses (views, JSON, redirects).

Future `app/Http/Controllers/Api/V1/` controllers will reuse the same Actions and Services as Livewire components — no business logic duplication.

```php
// Future API controller — reuses the same Action
class ConnectionRequestController extends Controller
{
    public function store(StoreConnectionRequest $request, CreateConnectionRequest $action)
    {
        $data = CreateConnectionRequestData::from($request->validated());

        $connectionRequest = $action->execute($data);

        return new ConnectionRequestResource($connectionRequest);
    }
}
```

### Models (`app/Models/`)

Models should:

- define Eloquent relationships;
- define `$fillable`, `$casts`, `$hidden`;
- contain lightweight domain helpers (scopes, accessors, simple queries);
- **not** grow beyond ~150 lines.

Models should **NOT**:

- contain complex business logic;
- send notifications;
- perform multi-step state transitions;
- call external APIs.

### Enums (`app/Enums/`)

Use PHP backed enums for any value with a finite set of allowed states:

- `ConnectionStatus` — pending, accepted, payment_pending, connected, declined, cancelled
- `PaymentStatus` — pending, successful, failed, cancelled, refunded
- `OpportunityStatus` — draft, open, filled, closed, cancelled
- `ConnectionType` — professional_request, opportunity_application
- `TeachingMode` — physical, online, both

Enums include helper methods for state transition validation, labels, and grouping. **Never use raw strings for statuses in application code.**

### DTOs (`app/DataTransferObjects/`)

Use DTOs when:

- an Action or Service accepts **3+ related parameters** that form a logical unit;
- data crosses a boundary (gateway response → application layer);
- you want **immutable, typed** input/output contracts.

DTOs are `final readonly class` with a constructor. They carry no behavior beyond construction.

```php
final readonly class CreateConnectionRequestData
{
    public function __construct(
        public int $initiatorId,
        public int $recipientId,
        public string $type,
        public ?int $professionalProfileId = null,
        public ?int $opportunityId = null,
    ) {}
}
```

**Don't** create a DTO for every method call. If an Action takes 1–2 obvious parameters, typed method arguments are fine.

### Events & Listeners (`app/Events/`, `app/Listeners/`)

Events represent **something that happened** in the domain. Listeners handle **secondary side effects**.

Use Events/Listeners when:

- sending notifications (email, database, push);
- updating denormalized data (average ratings);
- logging audit trails;
- triggering async jobs.

**Do NOT** put primary business logic in listeners. The Action should succeed even if the listener fails. Notifications are a secondary concern — they never block the core operation.

```text
Action: AcceptConnectionRequest
  → Primary: update connection status, set accepted_at
  → Dispatches: ConnectionRequestAccepted event

Listener: SendPaymentRequiredNotification
  → Sends notification to initiator
  → If this fails, the connection is still accepted
```

### Policies (`app/Policies/`)

Policies answer: **"Is this user allowed to perform this action on this resource?"**

- Every model-level authorization check should go through a Policy;
- Policies are called at the **boundary** (Livewire, Controller) before delegating to Actions;
- Actions may perform additional invariant checks (e.g. "no duplicate active request") that aren't authorization.

```php
// In Livewire or Controller:
$this->authorize('accept', $connectionRequest);

// Then delegate:
$action->execute($connectionRequest->id, auth()->id());
```

### Contracts (`app/Contracts/`)

Contracts (interfaces) abstract **external integrations**:

- `PaymentGateway` — abstracts Paystack, Flutterwave, or any future provider.
- Bound in `AppServiceProvider`. Swapping providers = changing one binding.

**The application layer never imports `PaystackGateway` directly.** It type-hints `PaymentGateway` and the container resolves the active implementation.

### Exceptions (`app/Exceptions/`)

Domain-specific exceptions for business rule violations:

- `DuplicateConnectionException` — duplicate active request between the same users
- `InvalidConnectionTransitionException` — invalid status transition

These are caught at the boundary layer (Livewire/Controller) and translated to user-facing error messages.

---

## How Future API Controllers Reuse the Application Layer

```text
┌──────────────────────────────────────────────────┐
│                 Delivery Layer                    │
│                                                  │
│  Livewire Components    API Controllers (future) │
│  ┌─────────────────┐    ┌─────────────────────┐  │
│  │ UI state mgmt   │    │ JSON parsing        │  │
│  │ $this->authorize │    │ $this->authorize    │  │
│  │ Call Action      │    │ Call same Action     │  │
│  │ Dispatch toast   │    │ Return Resource     │  │
│  └────────┬────────┘    └─────────┬───────────┘  │
│           │                       │               │
├───────────┼───────────────────────┼───────────────┤
│           ▼                       ▼               │
│  ┌─────────────────────────────────────────────┐  │
│  │             Application Layer               │  │
│  │                                             │  │
│  │  Actions ──→ Events ──→ Listeners           │  │
│  │  Services (queries)                         │  │
│  │  DTOs                                       │  │
│  │  Policies                                   │  │
│  │  Contracts ──→ Implementations              │  │
│  └─────────────────────────────────────────────┘  │
│                       │                           │
├───────────────────────┼───────────────────────────┤
│                       ▼                           │
│  ┌─────────────────────────────────────────────┐  │
│  │             Infrastructure Layer            │  │
│  │                                             │  │
│  │  Eloquent Models                            │  │
│  │  Migrations                                 │  │
│  │  PaystackGateway (concrete)                 │  │
│  │  Pusher/Echo                                │  │
│  │  MySQL                                      │  │
│  └─────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────┘
```

To add `/api/v1/connections` endpoints:

1. Create `app/Http/Controllers/Api/V1/ConnectionRequestController.php`
2. Create API Form Requests for validation
3. Create API Resources for response formatting
4. Call the **same Actions** (`CreateConnectionRequest`, `AcceptConnectionRequest`, etc.)
5. No business logic duplication

---

## Key Conventions

| Concern | Where it lives |
|---|---|
| UI state, form inputs, toasts | Livewire component |
| "Can this user do X?" | Policy |
| "Create/accept/decline a connection" | Action |
| "Search professionals with filters" | Service |
| "Send a notification after acceptance" | Event → Listener |
| "Is this status transition valid?" | Enum method |
| "Talk to Paystack" | Contract → PaystackGateway |
| "₦1,000 connection fee" | `config('marketplace.connection_fee')` |
| "Structured input to an Action" | DTO |
| "Connection status = pending" | Enum, never raw string |

---

## Database Transactions

Any operation that modifies multiple tables atomically **must** use `DB::transaction()`. This is enforced in Actions, never in Livewire or Controllers.

Examples:
- `VerifyAndActivatePayment` — updates Payment + ConnectionRequest + creates Conversation
- `CreateConnectionRequest` — creates request + dispatches event (event dispatch outside transaction if async)

---

## Testing Strategy

| Layer | Test Type |
|---|---|
| Actions | Unit/Integration tests (most critical) |
| Services | Integration tests with database |
| Policies | Unit tests |
| Enums | Unit tests (transition logic) |
| Livewire | Feature tests (Livewire testing utilities) |
| API Controllers (future) | Feature tests |
| PaystackGateway | Mocked behind `PaymentGateway` interface |
