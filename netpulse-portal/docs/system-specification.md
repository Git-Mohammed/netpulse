# NetPulse Portal: System Specification & Architectural Design Document

## 1. System Scope & Purpose

This document outlines the technical and functional requirements for the NetPulse Ticketing Portal. The portal acts as a dedicated operational interface for support engineers to monitor operational incidents discovered automatically via the central Java server and manage their complete lifecycle until resolution.

### 1.1 Core Architectural Principles

- **Decoupling:** The web portal operates independently from real-time network processing. Network orchestration is handled via the Java backend and Oracle database, whereas ticket lifecycles are managed through PHP and MySQL.
- **Source of Truth Separation:**
  - **Oracle DB:** Maintains device inventories (`DEVICE`), telemetry alerts (`ALERT`), and operational incidents (`INCIDENT`).
  - **MySQL DB:** Manages tickets (`TICKET`), comments (`TICKET_COMMENT`), audit history (`TICKET_HISTORY`), and portal users (`WEB_USER`).
- **Automated Escalation:** Critical incidents bypass manual creation; reports are automatically ingested from the central server via an API webhook.

## 2. Technical Stack & Software Architecture

| **Component**             | **Technology / Standard**    | **Operational Description**                                                               |
| ------------------------- | ---------------------------- | ----------------------------------------------------------------------------------------- |
| **Backend Language**      | PHP 8.2+                     | Object-Oriented Programming (OOP) with strict typing enforced.                            |
| **Database Engine**       | MySQL 8.0+                   | InnoDB storage engine ensuring full ACID compliance.                                      |
| **Database Abstraction**  | PDO (PHP Data Objects)       | Parameterized queries via Prepared Statements exclusively to prevent SQL injection.       |
| **Architectural Pattern** | Clean MVC + Service Layer    | Strict separation between Controllers, Models, Repositories, and Business Logic Services. |
| **Frontend Framework**    | Custom CSS                   | Geometric grid layout built on Swiss Design principles.                                   |
| **Typography Standard**   | Inter & IBM Plex Sans Arabic | `Inter` for numbers/code blocks, and `IBM Plex Sans Arabic` for typography.               |

## 3. Database Schema Specification

### 3.1 Data Dictionary

#### A. Web Users (`WEB_USER`)

Stores credentials and profiles for authorized engineers and administrators.

| **Column Name** | **Data Type** | **Constraints**                      | **Description**                              |
| --------------- | ------------- | ------------------------------------ | -------------------------------------------- |
| `user_id`       | BIGINT        | PRIMARY KEY, AUTO_INCREMENT          | Unique identifier for the user               |
| `username`      | VARCHAR(100)  | UNIQUE, NOT NULL                     | Unique login handle                          |
| `password_hash` | VARCHAR(255)  | NOT NULL                             | BCrypt secure password hash                  |
| `full_name`     | VARCHAR(150)  | NOT NULL                             | Engineer's full name                         |
| `role`          | VARCHAR(50)   | NOT NULL, DEFAULT 'SUPPORT_ENGINEER' | Role (ADMIN, NOC_ENGINEER, SUPPORT_ENGINEER) |
| `status`        | VARCHAR(20)   | NOT NULL, DEFAULT 'ACTIVE'           | Account status (ACTIVE, DISABLED)            |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP            | Account creation timestamp                   |

#### B. Tickets (`TICKET`)

The primary ledger for operational incidents and service requests.

| **Column Name** | **Data Type** | **Constraints**                   | **Description**                                                          |
| --------------- | ------------- | --------------------------------- | ------------------------------------------------------------------------ |
| `ticket_id`     | BIGINT        | PRIMARY KEY, AUTO_INCREMENT       | Internal primary key                                                     |
| `ticket_number` | VARCHAR(50)   | UNIQUE, NOT NULL                  | Virtual display reference (e.g., TKT-2026-0001)                          |
| `incident_id`   | BIGINT        | NULLABLE                          | External reference to Oracle incident ID                                 |
| `title`         | VARCHAR(255)  | NOT NULL                          | Summary or fault title                                                   |
| `description`   | TEXT          | NOT NULL                          | Detailed technical description of the fault                              |
| `priority`      | VARCHAR(20)   | NOT NULL, DEFAULT 'MEDIUM'        | Priority level (CRITICAL, HIGH, MEDIUM, LOW)                             |
| `status`        | VARCHAR(30)   | NOT NULL, DEFAULT 'OPEN'          | Lifecycle state (OPEN, ASSIGNED, IN_PROGRESS, WAITING, RESOLVED, CLOSED) |
| `assigned_to`   | BIGINT        | NULLABLE, FK -> WEB_USER(user_id) | Assigned support engineer                                                |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP         | Creation timestamp                                                       |
| `updated_at`    | DATETIME      | ON UPDATE CURRENT_TIMESTAMP       | Last modification timestamp                                              |
| `closed_at`     | DATETIME      | NULLABLE                          | Final resolution/closure timestamp                                       |

#### C. Ticket Comments (`TICKET_COMMENT`)

Stores ongoing updates and notes logged by engineers during diagnostics and resolution.

| **Column Name** | **Data Type** | **Constraints**                   | **Description**             |
| --------------- | ------------- | --------------------------------- | --------------------------- |
| `comment_id`    | BIGINT        | PRIMARY KEY, AUTO_INCREMENT       | Unique comment identifier   |
| `ticket_id`     | BIGINT        | NOT NULL, FK -> TICKET(ticket_id) | Associated ticket reference |
| `user_id`       | BIGINT        | NOT NULL, FK -> WEB_USER(user_id) | Authorizing user            |
| `comment_text`  | TEXT          | NOT NULL                          | Technical update body       |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP         | Timestamp of creation       |

#### D. Ticket History (`TICKET_HISTORY`)

Maintains an immutable audit trail of state mutations to guarantee transparency and accountability.

| **Column Name** | **Data Type** | **Constraints**                   | **Description**             |
| --------------- | ------------- | --------------------------------- | --------------------------- |
| `history_id`    | BIGINT        | PRIMARY KEY, AUTO_INCREMENT       | Unique log entry identifier |
| `ticket_id`     | BIGINT        | NOT NULL, FK -> TICKET(ticket_id) | Associated ticket reference |
| `old_status`    | VARCHAR(30)   | NOT NULL                          | Previous lifecycle state    |
| `new_status`    | VARCHAR(30)   | NOT NULL                          | Updated lifecycle state     |
| `changed_by`    | BIGINT        | NOT NULL, FK -> WEB_USER(user_id) | User executing the mutation |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP         | Mutation timestamp          |

## 4. Functional Requirements

### 4.1 Authentication & Authorization Module

- `[FR-AUTH-01]`: Support secure credential-based authentication using hashed passwords.
- `[FR-AUTH-02]`: Implement secure session management to prevent unauthorized access to internal views.
- `[FR-AUTH-03]`: Enforce Role-Based Access Control (RBAC):
  - **ADMIN:** Full control over system administration, users, and ticket workflows.
  - **SUPPORT_ENGINEER / NOC_ENGINEER:** View tickets, assign ownership, post comments, and advance states.

### 4.2 Dashboard Module

- `[FR-DASH-01]`: Render real-time metric cards displaying total counts for OPEN, IN_PROGRESS, WAITING, and CLOSED tickets.
- `[FR-DASH-02]`: Display a dedicated quick-access table highlighting the latest CRITICAL tickets requiring immediate action.

### 4.3 Ticket Management Module

- `[FR-TCK-01]`: Present a structured tabular view of all tickets utilizing semantic color-coded badges for status and priority.
- `[FR-TCK-02]`: Support dynamic data filtering by Status, Priority, or Assigned Engineer.
- `[FR-TCK-03]`: Provide capability to assign tickets to specific support personnel.
- `[FR-TCK-04]`: Restrict ticket transitions to follow the defined lifecycle sequence:
  `OPEN -> ASSIGNED -> IN_PROGRESS -> RESOLVED -> CLOSED`
- `[FR-TCK-05]`: Prevent state transition to `CLOSED` unless a mandatory resolution comment is provided, automatically recording the `closed_at` timestamp.

### 4.4 Comments & Audit History Module

- `[FR-CMT-01]`: Enable engineers to append technical comments tied to the ticket alongside author tracking and timestamps.
- `[FR-HIS-01]`: Automatically dispatch a transaction log to `TICKET_HISTORY` via `TicketService` upon every state mutation, capturing prior status, new status, and the accountable user.

## 5. API Integration & Mock Interface (Webhook)

The central Java server automatically provisions a ticket when an event escalates to a `CRITICAL` severity level. Web integration is handled via a dedicated API endpoint.

### 5.1 API Endpoint Specifications

- **Endpoint URL:** `/api/webhook.php`
- **HTTP Method:** `POST`
- **Request Headers:** `Content-Type: application/json`

### 5.2 JSON Payload Schema

```JSON
{
  "incident_id": 1052,
  "title": "Core-R2 Link Failure - Interface Gi0/1",
  "description": "Interface GigabitEthernet0/1 changed state to down on Core Router 2. Operational uplink lost.",
  "priority": "CRITICAL"
}
```

### 5.3 PHP Webhook Processing Logic

1. **Input Validation:** Verify presence of core parameters (`title`, `description`, `priority`).
2. **Reference Generation:** Generate a sequential identifier using the format `TKT-{YEAR}-{AUTO_INCREMENT}` (e.g., `TKT-2026-0089`).
3. **Database Persistence:** Insert a new record into `TICKET` with state `OPEN` and `assigned_to = NULL`.
4. **Initial Audit Logging:** Record the system genesis state in `TICKET_HISTORY` (`SYSTEM_CREATED -> OPEN`).
5. **HTTP Response:** Return `HTTP 201 Created` with the payload confirmation:

```JSON
{
  "status": "success",
  "ticket_number": "TKT-2026-0089",
  "message": "Ticket generated successfully from central NOC engine."
}
```

## 6. Non-Functional Requirements & Design Standards

### 6.1 UI/UX -

- **Grid System:** Structured geometric layouts (e.g., ticket detail workspace split into a 70% primary inspection column and 30% control/timeline sidebar).

- **Semantic Color Coding:**
  - **CRITICAL / OPEN:** `#DC2626` (Crimson Red)
  - **IN_PROGRESS / WAITING:** `#D97706` (Amber Yellow)
  - **RESOLVED / CLOSED:** `#16A34A` (Emerald Green)
- **Contrast & Legibility:** Clean neutral backgrounds (`#F8FAFC`) paired with high-contrast dark typography to eliminate visual fatigue.

### 6.2 Security Architecture

- **SQL Injection Defense:** Strict reliance on PDO Prepared Statements.

- **XSS Mitigation:** Output sanitization implemented universally via `htmlspecialchars()` on all user-submitted text fields and payloads.

- **Credential Security:** Secure password storage enforced via `PASSWORD_BCRYPT`.

### 6.3 Performance & Reliability

- **Lightweight Footprint:** Zero heavy framework dependencies to optimize page load latency.
- **Database Indexing:** Optimized indexing on high-frequency filter columns (`status`, `priority`, `assigned_to`).

## 7. Project Directory Structure

Plaintext

```
netpulse-portal/
├── core/                  # Core system bootstrapping components
│   ├── Database.php       # PDO Singleton database connection provider
│   └── Router.php         # Request router dispatcher
├── src/                   # Isolated business domain logic
│   ├── Controllers/       # HTTP request handlers and response formatters
│   ├── Models/            # Domain entities (Ticket, User)
│   ├── Repositories/      # Data persistence layer (Raw SQL queries via PDO)
│   └── Services/          # Business logic orchestration and transaction managers
├── views/                 # Presentation templates (Decoupled from business logic)
│   ├── layouts/           # Global templates (Header, Footer, Navigation wrappers)
│   ├── tickets/           # Ticket operational views
│   └── dashboard/         # Executive metrics and status overview screens
├── public/                # Web-accessible public root directory
│   ├── index.php          # Unified Front Controller entry point
│   └── assets/            # CSS stylesheets (Swiss Design system), JS, and images
└── api/                   # Integration webhooks and automated endpoints
```

# NetPulse Portal: System Specification & Architectural Design Document

## 1. System Scope & Purpose

This document outlines the technical and functional requirements for the NetPulse Ticketing Portal. The portal acts as a dedicated operational interface for support engineers to monitor operational incidents discovered automatically via the central Java server and manage their complete lifecycle until resolution.

### 1.1 Core Architectural Principles

- **Decoupling:** The web portal operates independently from real-time network processing. Network orchestration is handled via the Java backend and Oracle database, whereas ticket lifecycles are managed through PHP and MySQL.
- **Source of Truth Separation:**
  - **Oracle DB:** Maintains device inventories (`DEVICE`), telemetry alerts (`ALERT`), and operational incidents (`INCIDENT`).
  - **MySQL DB:** Manages tickets (`TICKET`), comments (`TICKET_COMMENT`), audit history (`TICKET_HISTORY`), and portal users (`WEB_USER`).
- **Automated Escalation:** Critical incidents bypass manual creation; reports are automatically ingested from the central server via an API webhook.

## 2. Technical Stack & Software Architecture

| **Component**             | **Technology / Standard**    | **Operational Description**                                                               |
| ------------------------- | ---------------------------- | ----------------------------------------------------------------------------------------- |
| **Backend Language**      | PHP 8.2+                     | Object-Oriented Programming (OOP) with strict typing enforced.                            |
| **Database Engine**       | MySQL 8.0+                   | InnoDB storage engine ensuring full ACID compliance.                                      |
| **Database Abstraction**  | PDO (PHP Data Objects)       | Parameterized queries via Prepared Statements exclusively to prevent SQL injection.       |
| **Architectural Pattern** | Clean MVC + Service Layer    | Strict separation between Controllers, Models, Repositories, and Business Logic Services. |
| **Frontend Framework**    | Custom CSS                   | Geometric grid layout built on Swiss Design principles.                                   |
| **Typography Standard**   | Inter & IBM Plex Sans Arabic | `Inter` for numbers/code blocks, and `IBM Plex Sans Arabic` for typography.               |

## 3. Database Schema Specification

### 3.1 Data Dictionary

#### A. Web Users (`WEB_USER`)

Stores credentials and profiles for authorized engineers and administrators.

| **Column Name** | **Data Type** | **Constraints**                      | **Description**                              |
| --------------- | ------------- | ------------------------------------ | -------------------------------------------- |
| `user_id`       | BIGINT        | PRIMARY KEY, AUTO_INCREMENT          | Unique identifier for the user               |
| `username`      | VARCHAR(100)  | UNIQUE, NOT NULL                     | Unique login handle                          |
| `password_hash` | VARCHAR(255)  | NOT NULL                             | BCrypt secure password hash                  |
| `full_name`     | VARCHAR(150)  | NOT NULL                             | Engineer's full name                         |
| `role`          | VARCHAR(50)   | NOT NULL, DEFAULT 'SUPPORT_ENGINEER' | Role (ADMIN, NOC_ENGINEER, SUPPORT_ENGINEER) |
| `status`        | VARCHAR(20)   | NOT NULL, DEFAULT 'ACTIVE'           | Account status (ACTIVE, DISABLED)            |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP            | Account creation timestamp                   |

#### B. Tickets (`TICKET`)

The primary ledger for operational incidents and service requests.

| **Column Name** | **Data Type** | **Constraints**                   | **Description**                                                          |
| --------------- | ------------- | --------------------------------- | ------------------------------------------------------------------------ |
| `ticket_id`     | BIGINT        | PRIMARY KEY, AUTO_INCREMENT       | Internal primary key                                                     |
| `ticket_number` | VARCHAR(50)   | UNIQUE, NOT NULL                  | Virtual display reference (e.g., TKT-2026-0001)                          |
| `incident_id`   | BIGINT        | NULLABLE                          | External reference to Oracle incident ID                                 |
| `title`         | VARCHAR(255)  | NOT NULL                          | Summary or fault title                                                   |
| `description`   | TEXT          | NOT NULL                          | Detailed technical description of the fault                              |
| `priority`      | VARCHAR(20)   | NOT NULL, DEFAULT 'MEDIUM'        | Priority level (CRITICAL, HIGH, MEDIUM, LOW)                             |
| `status`        | VARCHAR(30)   | NOT NULL, DEFAULT 'OPEN'          | Lifecycle state (OPEN, ASSIGNED, IN_PROGRESS, WAITING, RESOLVED, CLOSED) |
| `assigned_to`   | BIGINT        | NULLABLE, FK -> WEB_USER(user_id) | Assigned support engineer                                                |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP         | Creation timestamp                                                       |
| `updated_at`    | DATETIME      | ON UPDATE CURRENT_TIMESTAMP       | Last modification timestamp                                              |
| `closed_at`     | DATETIME      | NULLABLE                          | Final resolution/closure timestamp                                       |

#### C. Ticket Comments (`TICKET_COMMENT`)

Stores ongoing updates and notes logged by engineers during diagnostics and resolution.

| **Column Name** | **Data Type** | **Constraints**                   | **Description**             |
| --------------- | ------------- | --------------------------------- | --------------------------- |
| `comment_id`    | BIGINT        | PRIMARY KEY, AUTO_INCREMENT       | Unique comment identifier   |
| `ticket_id`     | BIGINT        | NOT NULL, FK -> TICKET(ticket_id) | Associated ticket reference |
| `user_id`       | BIGINT        | NOT NULL, FK -> WEB_USER(user_id) | Authorizing user            |
| `comment_text`  | TEXT          | NOT NULL                          | Technical update body       |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP         | Timestamp of creation       |

#### D. Ticket History (`TICKET_HISTORY`)

Maintains an immutable audit trail of state mutations to guarantee transparency and accountability.

| **Column Name** | **Data Type** | **Constraints**                   | **Description**             |
| --------------- | ------------- | --------------------------------- | --------------------------- |
| `history_id`    | BIGINT        | PRIMARY KEY, AUTO_INCREMENT       | Unique log entry identifier |
| `ticket_id`     | BIGINT        | NOT NULL, FK -> TICKET(ticket_id) | Associated ticket reference |
| `old_status`    | VARCHAR(30)   | NOT NULL                          | Previous lifecycle state    |
| `new_status`    | VARCHAR(30)   | NOT NULL                          | Updated lifecycle state     |
| `changed_by`    | BIGINT        | NOT NULL, FK -> WEB_USER(user_id) | User executing the mutation |
| `created_at`    | DATETIME      | DEFAULT CURRENT_TIMESTAMP         | Mutation timestamp          |

## 4. Functional Requirements

### 4.1 Authentication & Authorization Module

- `[FR-AUTH-01]`: Support secure credential-based authentication using hashed passwords.
- `[FR-AUTH-02]`: Implement secure session management to prevent unauthorized access to internal views.
- `[FR-AUTH-03]`: Enforce Role-Based Access Control (RBAC):
  - **ADMIN:** Full control over system administration, users, and ticket workflows.
  - **SUPPORT_ENGINEER / NOC_ENGINEER:** View tickets, assign ownership, post comments, and advance states.

### 4.2 Dashboard Module

- `[FR-DASH-01]`: Render real-time metric cards displaying total counts for OPEN, IN_PROGRESS, WAITING, and CLOSED tickets.
- `[FR-DASH-02]`: Display a dedicated quick-access table highlighting the latest CRITICAL tickets requiring immediate action.

### 4.3 Ticket Management Module

- `[FR-TCK-01]`: Present a structured tabular view of all tickets utilizing semantic color-coded badges for status and priority.
- `[FR-TCK-02]`: Support dynamic data filtering by Status, Priority, or Assigned Engineer.
- `[FR-TCK-03]`: Provide capability to assign tickets to specific support personnel.
- `[FR-TCK-04]`: Restrict ticket transitions to follow the defined lifecycle sequence:
  `OPEN -> ASSIGNED -> IN_PROGRESS -> RESOLVED -> CLOSED`
- `[FR-TCK-05]`: Prevent state transition to `CLOSED` unless a mandatory resolution comment is provided, automatically recording the `closed_at` timestamp.

### 4.4 Comments & Audit History Module

- `[FR-CMT-01]`: Enable engineers to append technical comments tied to the ticket alongside author tracking and timestamps.
- `[FR-HIS-01]`: Automatically dispatch a transaction log to `TICKET_HISTORY` via `TicketService` upon every state mutation, capturing prior status, new status, and the accountable user.

## 5. API Integration & Mock Interface (Webhook)

The central Java server automatically provisions a ticket when an event escalates to a `CRITICAL` severity level. Web integration is handled via a dedicated API endpoint.

### 5.1 API Endpoint Specifications

- **Endpoint URL:** `/api/webhook.php`
- **HTTP Method:** `POST`
- **Request Headers:** `Content-Type: application/json`

### 5.2 JSON Payload Schema

```JSON
{
  "incident_id": 1052,
  "title": "Core-R2 Link Failure - Interface Gi0/1",
  "description": "Interface GigabitEthernet0/1 changed state to down on Core Router 2. Operational uplink lost.",
  "priority": "CRITICAL"
}
```

### 5.3 PHP Webhook Processing Logic

1. **Input Validation:** Verify presence of core parameters (`title`, `description`, `priority`).
2. **Reference Generation:** Generate a sequential identifier using the format `TKT-{YEAR}-{AUTO_INCREMENT}` (e.g., `TKT-2026-0089`).
3. **Database Persistence:** Insert a new record into `TICKET` with state `OPEN` and `assigned_to = NULL`.
4. **Initial Audit Logging:** Record the system genesis state in `TICKET_HISTORY` (`SYSTEM_CREATED -> OPEN`).
5. **HTTP Response:** Return `HTTP 201 Created` with the payload confirmation:

```JSON
{
  "status": "success",
  "ticket_number": "TKT-2026-0089",
  "message": "Ticket generated successfully from central NOC engine."
}
```

## 6. Non-Functional Requirements & Design Standards

### 6.1 UI/UX -

- **Grid System:** Structured geometric layouts (e.g., ticket detail workspace split into a 70% primary inspection column and 30% control/timeline sidebar).

- **Semantic Color Coding:**
  - **CRITICAL / OPEN:** `#DC2626` (Crimson Red)
  - **IN_PROGRESS / WAITING:** `#D97706` (Amber Yellow)
  - **RESOLVED / CLOSED:** `#16A34A` (Emerald Green)
- **Contrast & Legibility:** Clean neutral backgrounds (`#F8FAFC`) paired with high-contrast dark typography to eliminate visual fatigue.

### 6.2 Security Architecture

- **SQL Injection Defense:** Strict reliance on PDO Prepared Statements.

- **XSS Mitigation:** Output sanitization implemented universally via `htmlspecialchars()` on all user-submitted text fields and payloads.

- **Credential Security:** Secure password storage enforced via `PASSWORD_BCRYPT`.

### 6.3 Performance & Reliability

- **Lightweight Footprint:** Zero heavy framework dependencies to optimize page load latency.
- **Database Indexing:** Optimized indexing on high-frequency filter columns (`status`, `priority`, `assigned_to`).

## 7. Project Directory Structure

Plaintext

```
netpulse-portal/
├──docs/
│   ├── system-specification.md
│   └── database-architecture.md
├── core/                  # Core system bootstrapping components
│   ├── Database.php       # PDO Singleton database connection provider
│   └── Router.php         # Request router dispatcher
├── src/                   # Isolated business domain logic
│   ├── Controllers/       # HTTP request handlers and response formatters
│   ├── Models/            # Domain entities (Ticket, User)
│   ├── Repositories/      # Data persistence layer (Raw SQL queries via PDO)
│   └── Services/          # Business logic orchestration and transaction managers
├── views/                 # Presentation templates (Decoupled from business logic)
│   ├── layouts/           # Global templates (Header, Footer, Navigation wrappers)
│   ├── tickets/           # Ticket operational views
│   └── dashboard/         # Executive metrics and status overview screens
├── public/                # Web-accessible public root directory
│   ├── index.php          # Unified Front Controller entry point
│   └── assets/            # CSS stylesheets, JS, and images
└── api/                   # Integration webhooks and automated endpoints
```
