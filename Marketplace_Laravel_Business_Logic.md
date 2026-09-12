# Service Marketplace — Laravel Business Logic & 4-Week Architecture

> **Working note:** The platform name has **not yet been decided or approved**. Use neutral labels such as **Marketplace**, **Platform**, or **Service Marketplace** in code, documentation, UI placeholders, repositories, and infrastructure until the client approves a final brand name.

## 1. Product Vision

The application is a broad marketplace connecting:

- customers and businesses looking for skilled professionals;
- professionals and artisans offering services;
- professionals looking for jobs, gigs, contracts, or other opportunities;
- people or organizations posting opportunities;
- parents/students looking for teachers or private tutors;
- academic professionals looking for tutoring and teaching opportunities.

The platform must **not** be structured as an academic-only marketplace. **Education & Tutoring** is a specialized vertical within the broader service marketplace.

Browsing, creating a profile, and posting an opportunity are free. The platform monetizes successful introductions through a configurable **connection fee**, initially **₦1,000**.

---

# 2. Core Product Principles

## 2.1 One Universal User Account

Do **not** create isolated account systems such as:

```text
Parent Account
Teacher Account
Customer Account
Professional Account
Business Account
```

These identities overlap.

A single `User` can:

- find professionals;
- offer services;
- create a professional profile;
- post opportunities;
- browse opportunities;
- request connections;
- receive connection requests;
- pay to activate a connection when they initiated it;
- chat after connection activation;
- review users after an eligible connection.

A teacher can hire a photographer. A parent can also be an accountant. A plumber can post a job. The application should therefore authorize **actions**, not permanently box people into rigid identities.

## 2.2 Onboarding Intent Is Not a Permanent Role

During onboarding ask:

```text
What would you like to do?

[ Find a professional ]
[ Offer my services ]
[ Post jobs / opportunities ]
```

This selection determines the user's **initial journey**, not their permanent account type.

Users should later be able to access all marketplace capabilities that apply to them.

---

# 3. Marketplace Verticals

The marketplace should use a category-driven structure.

Example:

```text
Marketplace
│
├── Home & Technical Services
│   ├── Electricians
│   ├── Plumbers
│   ├── Carpenters
│   └── Technicians
│
├── Creative & Digital
│   ├── Designers
│   ├── Developers
│   ├── Photographers
│   └── Videographers
│
├── Business & Professional
│   ├── Accountants
│   ├── Consultants
│   └── Other professionals
│
├── Beauty & Lifestyle
│   └── ...
│
└── Education & Tutoring
    ├── Primary School Tutors
    ├── Secondary School Tutors
    ├── WAEC / NECO Tutors
    ├── JAMB Tutors
    ├── University Tutors
    ├── Music Teachers
    ├── Language Teachers
    └── Other Academic Professionals
```

Categories should be database-driven so new verticals can be introduced without redesigning the marketplace engine.

---

# 4. Education & Tutoring Vertical

Education is a **specialized category**, not a separate application.

It uses the same:

- users;
- professional profiles;
- opportunities;
- connection engine;
- payment engine;
- conversations;
- reviews.

It adds education-specific metadata and discovery filters.

## 4.1 Tutor / Academic Professional Profile

A tutor still has a normal `professional_profile`.

Standard fields may include:

```text
user_id
category_id
display_name
bio
location
years_of_experience
phone
contact_email
profile_photo
availability_status
average_rating
```

When the professional belongs to Education & Tutoring, attach specialized details.

Suggested table:

```text
education_profiles
```

Suggested fields:

```text
id
professional_profile_id
teaching_mode
qualifications nullable
rate_min nullable
rate_max nullable
created_at
updated_at
```

Subjects and levels should preferably be relational rather than stored as comma-separated strings.

Suggested supporting tables:

```text
subjects
education_levels
education_profile_subject
education_profile_level
```

Example subjects:

```text
Mathematics
English
Physics
Chemistry
Biology
French
Programming
Music
```

Example levels:

```text
Primary
Junior Secondary
Senior Secondary
WAEC / NECO
JAMB
Undergraduate
Adult Learning
```

Possible teaching modes:

```text
physical
online
both
```

## 4.2 Academic Discovery

The Education & Tutoring section can provide specialized filters:

```text
Subject
Education Level
Teaching Mode
Location
Rating
```

Example:

```text
Find a Tutor

Subject: Mathematics
Level: Senior Secondary
Teaching Mode: Physical
Location: Ilorin
```

This specialized UI should still query the same marketplace professional system.

---

# 5. Authentication

Support:

- email/password registration;
- login;
- logout;
- password reset;
- email verification where appropriate;
- Google signup/login using Laravel Socialite.

Recommended authentication stack:

```text
Laravel Authentication
Laravel Socialite
Google OAuth
```

## 5.1 Social Account Structure

Use:

```text
users
social_accounts
```

Suggested `social_accounts` fields:

```text
id
user_id
provider
provider_user_id
created_at
updated_at
```

Do not store Google access tokens unless the application later requires access to another Google service.

After first authentication:

```text
Authenticate
    ↓
Check onboarding status
    ↓
Incomplete → Onboarding
Complete   → Dashboard
```

---

# 6. Onboarding Architecture

## Step 1 — Account

Collect or obtain:

```text
name
email
password OR Google identity
```

## Step 2 — Initial Intent

Ask:

```text
What would you like to do?

○ Find a professional
○ Offer my services
○ Post jobs / opportunities
```

This may be stored as an onboarding preference for personalization, but must not permanently restrict capabilities.

## Step 3A — User Wants to Find Someone

Collect only essential customer information:

```text
name
location
phone where required
```

Then direct them to marketplace discovery.

Do **not** ask whether they are a "parent" unless the current action requires education-specific information.

A parent is simply a user seeking an education service.

## Step 3B — User Wants to Offer Services

Ask for:

```text
service category
bio
skills
experience
location
photo
availability
private contact information
```

If category is **Education & Tutoring**, trigger specialized onboarding:

```text
Subjects taught
Education levels
Teaching mode
Qualifications
Experience
Location
Availability
Optional rate/range
```

## Step 3C — User Wants to Post an Opportunity

Take them into the opportunity creation flow.

---

# 7. Professional Profiles

A user may have one professional profile for the MVP.

Suggested tables:

```text
professional_profiles
categories
skills
professional_profile_skill
```

Public information:

- display name;
- photo;
- bio;
- category;
- skills;
- location;
- rating;
- reviews;
- relevant category-specific information.

Private until a connection is active:

- phone;
- private email;
- other direct contact details.

The backend must enforce this privacy. Do not simply hide the information with CSS or Livewire state.

---

# 8. Opportunities — Replace the Narrow "Jobs" Concept

Use the broader domain term:

```text
opportunities
```

instead of structuring the core feature only around `jobs`.

An opportunity may represent:

```text
full-time job
part-time job
freelance gig
contract
one-off service need
private tutoring request
teaching opportunity
other marketplace opportunity
```

Suggested fields:

```text
id
user_id
category_id
title
description
location
opportunity_type
budget_min nullable
budget_max nullable
status
application_deadline nullable
created_at
updated_at
```

Possible statuses:

```text
draft
open
filled
closed
cancelled
```

## 8.1 Education Opportunity Details

For an academic opportunity such as:

> Looking for a Mathematics tutor for my SS2 child.

Use specialized metadata rather than adding education columns to every opportunity.

Suggested:

```text
education_opportunity_details
```

Fields:

```text
id
opportunity_id
subject_id
education_level_id
teaching_mode
schedule_notes nullable
student_notes nullable
created_at
updated_at
```

Example:

```text
Category: Education & Tutoring
Subject: Mathematics
Level: Senior Secondary / SS2
Teaching Mode: Physical
Location: Ilorin
Schedule: Weekends
```

---

# 9. Discovery

## 9.1 Find Professionals

General filters:

```text
category
skill
location
rating
```

Flow:

```text
Search
  ↓
Professional Results
  ↓
Profile
  ↓
Request Connection
```

## 9.2 Find Tutors

Education-specific filters:

```text
subject
level
teaching mode
location
rating
```

Flow:

```text
Education & Tutoring
  ↓
Find Tutor
  ↓
Tutor Results
  ↓
Tutor Profile
  ↓
Request Connection
```

## 9.3 Find Opportunities

Filters may include:

```text
keyword
category
location
opportunity type
```

For Education:

```text
subject
education level
teaching mode
location
```

---

# 10. Universal Connection Rule

The most important monetization rule is:

> **The person who initiates a connection pays the connection fee after the receiving party accepts the request.**

Current fee:

```text
₦1,000
```

Do not hard-code the amount throughout the codebase.

Example:

```php
config('marketplace.connection_fee')
```

or use a settings table.

---

# 11. Connection Flow — Hiring a Professional

```text
Customer
   ↓
Find Professional
   ↓
View Profile
   ↓
Request Connection
   ↓
Professional Receives Request
   ↓
Accept / Decline
```

If declined:

```text
Declined
No payment
No contact details
No private chat
```

If accepted:

```text
Accepted
   ↓
Payment Pending
   ↓
Initiator Pays ₦1,000
   ↓
Server Verifies Payment
   ↓
Connected
   ↓
Chat + Contact Details
```

---

# 12. Connection Flow — Applying to an Opportunity

```text
Professional
   ↓
Browse Opportunities
   ↓
View Opportunity
   ↓
Connect / Apply
   ↓
Opportunity Owner Receives Request
   ↓
Accept / Decline
```

If accepted:

```text
Professional Pays ₦1,000
        ↓
Payment Verified
        ↓
Connection Active
        ↓
Chat + Contact Details
```

The opportunity poster does not pay to receive/respond to the request.

---

# 13. Academic Example A — Parent Searches for Tutor

```text
Parent/User
   ↓
Education & Tutoring
   ↓
Search Mathematics Tutors
   ↓
View Tutor
   ↓
Request Connection
   ↓
Tutor Accepts
   ↓
Parent Pays ₦1,000
   ↓
Payment Verified
   ↓
Connected
   ↓
Chat + Contact
```

The parent does not need a special "Parent Account".

---

# 14. Academic Example B — Parent Posts Tutor Requirement

```text
Parent/User
   ↓
Post Opportunity
   ↓
Education & Tutoring
   ↓
"SS2 Mathematics Tutor Needed"
   ↓
Tutor Finds Opportunity
   ↓
Tutor Requests Connection
   ↓
Parent Accepts
   ↓
Tutor Pays ₦1,000
   ↓
Payment Verified
   ↓
Connected
   ↓
Chat + Contact
```

The same connection engine handles both flows.

---

# 15. Connection Statuses

Recommended statuses:

```text
pending
accepted
payment_pending
connected
declined
cancelled
```

| Status | Meaning |
|---|---|
| `pending` | Initiator has sent a request |
| `accepted` | Recipient has accepted |
| `payment_pending` | Initiator must pay to activate |
| `connected` | Payment verified and relationship active |
| `declined` | Recipient rejected request |
| `cancelled` | Request cancelled before activation |

State transitions should be controlled by application services, not arbitrary UI requests.

---

# 16. Connection Request Table

Suggested:

```text
connection_requests
```

Fields:

```text
id
initiator_id
recipient_id
professional_profile_id nullable
opportunity_id nullable
type
status
accepted_at nullable
connected_at nullable
declined_at nullable
cancelled_at nullable
created_at
updated_at
```

Possible `type` values:

```text
professional_request
opportunity_application
```

Rules:

- initiator cannot connect to themselves;
- only recipient can accept or decline;
- only initiator pays;
- payment cannot be required before acceptance;
- private contacts remain hidden until `connected`;
- duplicate active requests should be prevented;
- `connected` requires a verified successful payment.

---

# 17. Payments

Recommended initial provider:

```text
Paystack
```

Suggested:

```text
payments
```

Fields:

```text
id
user_id
connection_request_id
reference
provider
amount
currency
status
provider_reference nullable
paid_at nullable
metadata nullable
created_at
updated_at
```

Statuses:

```text
pending
successful
failed
cancelled
refunded
```

Currency:

```text
NGN
```

---

# 18. Payment Flow

```text
Connection Accepted
       ↓
Payment Pending
       ↓
Initiator clicks Pay
       ↓
Laravel creates Payment
       ↓
Unique reference generated
       ↓
Paystack checkout
       ↓
Callback / Webhook
       ↓
Laravel verifies with Paystack
       ↓
Validate reference + amount + status
       ↓
Payment Successful
       ↓
Connection Activated
       ↓
Conversation Created/Enabled
       ↓
Private Contact Details Unlocked
```

## Critical Payment Security

Never trust browser success alone.

```text
Frontend says success
        ≠
Verified payment
```

Only server-side verification may transition:

```text
payment_pending → connected
```

## Idempotency

Callbacks/webhooks can occur multiple times.

Payment references must be unique and processing must safely handle duplicate notifications.

If already successful:

```text
do not charge again
do not create another conversation
do not activate twice
return safely
```

## Failed Payment

```text
payment = failed
connection = payment_pending
```

Allow the user to retry with a new transaction reference.

## Refunds

Automatic refunds are not part of the initial MVP unless later approved.

The accept-before-payment design substantially reduces rejection-related refund cases.

---

# 19. Chat

Use:

```text
Laravel
Livewire
Pusher Channels
Laravel Echo
MySQL
```

Chat is available only when:

```text
connection.status === connected
```

Flow:

```text
Livewire
   ↓
Laravel authorization
   ↓
Store message in MySQL
   ↓
Broadcast Laravel event
   ↓
Pusher
   ↓
Laravel Echo
   ↓
Recipient UI updates
```

Suggested tables:

```text
conversations
messages
```

### conversations

```text
id
connection_request_id
created_at
updated_at
```

### messages

```text
id
conversation_id
sender_id
body
read_at nullable
created_at
updated_at
```

All chat access must be protected by backend authorization/policies.

---

# 20. Reviews & Ratings

A review should require an activated connection.

Suggested:

```text
reviews
```

Fields:

```text
id
connection_request_id
reviewer_id
reviewee_id
rating
comment
created_at
updated_at
```

Rules:

- rating is 1–5;
- users cannot review themselves;
- reviewer must belong to the connection;
- one review per reviewer per connection;
- reviews can be public;
- professional average ratings should be calculated efficiently.

---

# 21. Notifications

Use Laravel Notifications.

MVP notifications:

- new connection request;
- accepted request;
- declined request;
- payment required;
- payment confirmed;
- connection activated;
- new message;
- new review.

Initial channels:

```text
database
email where appropriate
```

SMS/push notifications can be added later.

---

# 22. Suggested Database Tables

Core:

```text
users
social_accounts

categories
skills

professional_profiles
professional_profile_skill

opportunities

connection_requests
payments

conversations
messages

reviews
notifications
```

Education specialization:

```text
education_profiles
subjects
education_levels
education_profile_subject
education_profile_level
education_opportunity_details
```

---

# 23. High-Level Relationships

```text
User
 ├── hasOne ProfessionalProfile
 ├── hasMany Opportunities
 ├── hasMany InitiatedConnectionRequests
 ├── hasMany ReceivedConnectionRequests
 ├── hasMany Payments
 └── hasMany Messages

ProfessionalProfile
 ├── belongsTo User
 ├── belongsTo Category
 ├── belongsToMany Skills
 └── mayHave EducationProfile

EducationProfile
 ├── belongsTo ProfessionalProfile
 ├── belongsToMany Subjects
 └── belongsToMany EducationLevels

Opportunity
 ├── belongsTo User
 ├── belongsTo Category
 ├── hasMany ConnectionRequests
 └── mayHave EducationOpportunityDetails

ConnectionRequest
 ├── belongsTo Initiator(User)
 ├── belongsTo Recipient(User)
 ├── mayBelongTo ProfessionalProfile
 ├── mayBelongTo Opportunity
 ├── hasMany Payments
 └── hasOne Conversation

Payment
 ├── belongsTo User
 └── belongsTo ConnectionRequest

Conversation
 ├── belongsTo ConnectionRequest
 └── hasMany Messages
```

---

# 24. Recommended Laravel Code Structure

Keep Livewire components and controllers thin.

```text
app/
├── Actions/
├── Events/
├── Livewire/
├── Models/
├── Notifications/
├── Policies/
├── Services/
│   ├── ConnectionService.php
│   ├── PaymentService.php
│   ├── PaystackService.php
│   ├── ChatService.php
│   ├── OpportunityService.php
│   ├── ProfessionalProfileService.php
│   └── ReviewService.php
├── Listeners/
└── Providers/
```

### `ConnectionService`

Responsible for:

```text
create request
accept
decline
cancel
transition to payment pending
activate after verified payment
```

### `PaymentService`

Responsible for:

```text
create payment
initialize payment
verify payment
handle successful payment
trigger connection activation
```

### `PaystackService`

Contains provider-specific API communication.

This prevents core marketplace logic from being tightly coupled to Paystack.

### `OpportunityService`

Responsible for opportunity creation/update/closure and category-specific metadata.

### `ChatService`

Responsible for conversation creation, authorization, message persistence, and broadcasting.

### `ReviewService`

Responsible for review eligibility and creation.

---

# 25. Suggested Livewire Areas

```text
Authentication / Onboarding
├── OnboardingIntent
├── CustomerOnboarding
├── ProfessionalOnboarding
└── EducationProfessionalOnboarding

Professionals
├── ProfessionalDirectory
├── ProfessionalSearchFilters
├── ProfessionalProfileView
└── ProfessionalProfileEditor

Education
├── TutorDirectory
├── TutorFilters
└── EducationProfileFields

Opportunities
├── OpportunityList
├── OpportunityFilters
├── OpportunityDetails
├── OpportunityCreate
├── OpportunityEdit
└── MyOpportunities

Connections
├── SendConnectionRequest
├── IncomingRequests
├── OutgoingRequests
├── ConnectionDetails
└── PaymentPrompt

Payments
└── ConnectionPayment

Chat
├── ConversationList
└── ChatWindow

Reviews
├── ReviewForm
└── ReviewList

Dashboard
└── DashboardOverview
```

---

# 26. UI Connection States

No request:

```text
[ Request Connection ]
```

Pending:

```text
Request Pending
```

Accepted / awaiting payment:

```text
[ Pay ₦1,000 to Connect ]
```

Connected:

```text
[ Message ]
[ View Contact Details ]
```

Declined:

```text
Request Declined
```

The backend remains the source of truth for all states.

---

# 27. Privacy & Authorization

Before connection activation, private contact information must not appear in:

- rendered Blade HTML;
- Livewire payloads;
- JSON responses;
- hidden fields;
- page source;
- unauthorized endpoints.

Policies must protect:

```text
professional contact details
connections
payments
conversations
messages
opportunity management
reviews
```

Never rely solely on hidden buttons.

---

# 28. Four-Week MVP Scope

The MVP includes:

- universal user accounts;
- email/password authentication;
- Google authentication via Socialite;
- intent-based onboarding;
- professional profiles;
- category-driven marketplace;
- Education & Tutoring vertical;
- tutor-specific profile fields;
- professional/tutor discovery;
- opportunities;
- education-specific opportunity metadata;
- search and filtering;
- connection requests;
- accept/decline;
- configurable ₦1,000 connection fee;
- Paystack integration;
- server-side payment verification;
- private contact unlocking;
- Pusher real-time chat;
- reviews and ratings;
- database notifications;
- responsive interface;
- QA/security testing;
- production deployment.

---

# 29. Deferred Features

Unless explicitly approved, keep these outside the four-week MVP:

- escrow;
- professional payouts through the platform;
- commissions on service payments;
- subscriptions;
- wallet;
- bidding;
- automatic refunds;
- advanced disputes;
- AI matching;
- map/radius search;
- native mobile apps;
- multilingual support;
- SMS/push notifications;
- video calls;
- advanced analytics;
- complex identity verification.

---

# 30. Four-Week Development Plan

## PHASE 1 — WEEK 1
### Foundation, Authentication, Universal Onboarding & Profiles

**Goal:** Establish the architecture that every marketplace vertical will share.

### Day 1 — Architecture & Project Setup

- create Laravel application;
- configure MySQL;
- install/configure Livewire;
- Tailwind/Vite setup;
- Git repository;
- environment setup;
- finalize migrations/relationships;
- seed initial categories;
- use neutral project naming.

### Day 2 — Authentication

- registration;
- login/logout;
- password reset;
- email verification;
- policies/auth foundation.

### Day 3 — Socialite & Universal Onboarding

- Google OAuth;
- `social_accounts`;
- onboarding status;
- intent selection;
- dashboard routing.

### Day 4 — Professional Profiles

- standard professional profile;
- categories;
- skills;
- profile image;
- location;
- private contacts;
- edit/view profile.

### Day 5 — Education Specialization

- `education_profiles`;
- subjects;
- education levels;
- teaching mode;
- education-specific onboarding;
- tutor profile display.

### Day 6 — UI Foundation & Dashboard

Reusable:

- navbar;
- sidebar;
- buttons;
- cards;
- inputs;
- modals;
- badges;
- alerts;
- empty states;
- loading states;
- dashboard shell.

### Day 7 — Week 1 QA / Catch-Up

**Week 1 deliverable:**

```text
Register/Login/Google
        ↓
Choose Initial Intent
        ↓
Standard User OR Professional
        ↓
Education specialization where applicable
        ↓
Dashboard
```

---

## PHASE 2 — WEEK 2
### Discovery, Opportunities & Connection Engine

### Day 8 — Professional Directory

- listing;
- pagination;
- public profiles;
- general search.

### Day 9 — Search + Education Discovery

General:

```text
category
skill
location
rating
```

Education:

```text
subject
level
teaching mode
location
rating
```

### Day 10 — Opportunities

- create;
- edit;
- close;
- listing;
- detail page;
- ownership authorization.

### Day 11 — Education Opportunities

- tutor requirement posting;
- subject;
- level;
- teaching mode;
- education filters;
- opportunity search.

### Day 12 — Connection Requests

- professional requests;
- opportunity applications;
- incoming;
- outgoing;
- duplicate prevention.

### Day 13 — Connection State Machine + Privacy

- accept;
- decline;
- cancel;
- payment-pending transition;
- contact privacy;
- policies.

### Day 14 — Week 2 QA / Catch-Up

**Week 2 deliverable:**

```text
Discover Professional/Tutor/Opportunity
              ↓
        Request Connection
              ↓
        Accept / Decline
              ↓
      Await Connection Payment
```

---

## PHASE 3 — WEEK 3
### Payments & Real-Time Communication

### Day 15 — Payment Architecture

- `payments` migration/model;
- configurable fee;
- PaymentService;
- PaystackService;
- references/statuses.

### Day 16 — Paystack Initialization

- initialize transaction;
- checkout;
- metadata;
- return flow.

### Day 17 — Verification & Webhooks

- server verification;
- webhook;
- reference validation;
- amount validation;
- idempotency;
- failed payment retry.

### Day 18 — Connection Activation

```text
verified payment
      ↓
connected
      ↓
contact unlock
      ↓
conversation enabled
```

### Day 19 — Pusher Setup

- Pusher Channels;
- Laravel Echo;
- broadcast configuration;
- private channel authorization.

### Day 20 — Chat

- conversation list;
- chat window;
- send/receive;
- persistence;
- timestamps;
- unread state.

### Day 21 — Week 3 QA / Catch-Up

**Week 3 deliverable:**

```text
Accepted Request
      ↓
Pay ₦1,000
      ↓
Server Verification
      ↓
Connected
      ↓
Chat + Contact
```

---

## PHASE 4 — WEEK 4
### Reviews, Notifications, Security, Deployment

### Day 22 — Reviews

- review eligibility;
- stars;
- written review;
- average ratings;
- display.

### Day 23 — Notifications

- request;
- accept/decline;
- payment required;
- payment success;
- new message;
- review.

### Day 24 — Dashboard & UX Polish

Dashboard sections:

```text
pending requests
active connections
professional profile
posted opportunities
applications
recent messages
notifications
```

### Day 25 — Full Journey QA

Test both:

```text
Customer → Professional
Parent → Tutor
Professional → General Opportunity
Tutor → Tutoring Opportunity
```

### Day 26 — Security & Payment QA

Test:

- forged callbacks;
- duplicate webhooks;
- wrong amount;
- invalid references;
- unauthorized contact access;
- conversation ID manipulation;
- opportunity ownership;
- direct URL access;
- validation;
- CSRF.

### Day 27 — Deployment

Configure:

- production environment;
- MySQL;
- domain;
- SSL;
- storage;
- Pusher;
- Paystack live credentials;
- email;
- cron/queues where supported;
- caching;
- logging;
- backups.

### Day 28 — Production QA & Client Review

- smoke test;
- mobile testing;
- complete user journeys;
- payment test;
- chat test;
- final client review;
- document known post-MVP improvements.

---

# 31. Critical Business Invariants

These rules should be protected by tests.

### 1. Browsing is free.

### 2. Posting an opportunity is free.

### 3. Receiving/responding to a connection request is free.

### 4. The connection initiator is the payer.

### 5. Payment occurs only after the recipient accepts.

### 6. No verified payment means no active connection.

### 7. No active connection means no private contact details.

### 8. No active connection means no private chat.

### 9. Only server-verified payments can activate connections.

### 10. A user may operate on both sides of the marketplace.

### 11. "Parent", "teacher", "customer", and "professional" are not mutually exclusive account identities.

### 12. Education & Tutoring uses the common marketplace engine and adds specialized metadata.

### 13. The final brand name must not be assumed until client approval.

---

# 32. Core User Journeys

## General Customer Hiring a Professional

```text
Homepage
  ↓
Find Professional
  ↓
View Profile
  ↓
Request Connection
  ↓
Professional Accepts
  ↓
Pay Connection Fee
  ↓
Payment Verified
  ↓
Connected
  ↓
Chat + Contact
  ↓
Review
```

## Professional Finding an Opportunity

```text
Browse Opportunities
  ↓
View Opportunity
  ↓
Apply / Connect
  ↓
Poster Accepts
  ↓
Professional Pays Connection Fee
  ↓
Payment Verified
  ↓
Connected
  ↓
Chat + Contact
  ↓
Review
```

## Parent Finding a Tutor

```text
Education & Tutoring
  ↓
Search by Subject / Level / Location
  ↓
Tutor Profile
  ↓
Request Connection
  ↓
Tutor Accepts
  ↓
Parent Pays Connection Fee
  ↓
Payment Verified
  ↓
Connected
  ↓
Chat + Contact
```

## Tutor Finding a Parent's Opportunity

```text
Education Opportunities
  ↓
"Mathematics Tutor Needed"
  ↓
Tutor Applies
  ↓
Parent Accepts
  ↓
Tutor Pays Connection Fee
  ↓
Payment Verified
  ↓
Connected
  ↓
Chat + Contact
```

---

# 33. Recommended Implementation Order

```text
1. Laravel project + neutral placeholder branding
2. Database architecture
3. Authentication
4. Socialite
5. Universal onboarding
6. Categories + skills
7. Professional profiles
8. Education profile specialization
9. Professional/tutor discovery
10. Opportunities
11. Education opportunity specialization
12. Connection state machine
13. Payment architecture
14. Paystack
15. Contact unlocking
16. Pusher/Echo
17. Chat
18. Reviews
19. Notifications
20. QA
21. Deployment
```

Do not begin Paystack or Pusher integration until the connection state machine is stable.

---

# 34. Definition of MVP Success

The general marketplace journey succeeds when:

```text
Create Account
    ↓
Discover or Offer Services
    ↓
Find Professional / Opportunity
    ↓
Request Connection
    ↓
Get Accepted
    ↓
Pay Connection Fee
    ↓
Payment Verified
    ↓
Chat + Contact
    ↓
Review
```

The academic vertical succeeds when:

```text
Parent can find Tutor
        AND
Tutor can find Tutoring Opportunity
        ↓
Both flows reuse the same connection/payment engine
```

This is the architectural principle that keeps the platform broad, scalable, and feasible within the four-week MVP window.
