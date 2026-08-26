# NetPulse

### Enterprise Network Operations & Environmental Monitoring System

> **NetPulse** is an event-driven, modular Network Operations Center (NOC) platform designed to monitor simulated network infrastructure, process real-time telemetry, generate intelligent alerts, manage operational incidents, and trigger physical IoT responses.

---

## 🏗️ Architectural Philosophy & Technology Stack

NetPulse is built on the principle of **Separation of Concerns (SoC)**. Rather than building a monolithic application, the system leverages a polyglot architecture where each language and tool is chosen specifically for its domain strength. 

### Why This Stack?

#### 1. Python: The Ingestion & Normalization Bridge
* **The Role:** Acts as the entry point for all raw network events and telemetry.
* **The Rationale:** Python excels at rapid network-level scripting, data parsing, and handling diverse network protocols. Instead of burdening the core backend with raw data extraction, Python acts as a lightweight daemon (buffer). It captures simulated network spikes or failures, normalizes the messy raw data into structured JSON, and passes it cleanly to the backend. This protects the core Java application from being overwhelmed by network noise or malformed packets.

#### 2. Java: The Core Business Backend
* **The Role:** The central brain of the system, handling business logic, alert thresholds, and concurrency.
* **The Rationale:** Processing thousands of network events requires strict memory management, robust multithreading, and absolute stability—areas where the JVM (Java Virtual Machine) shines. Java is used here to enforce strict data models, handle concurrent event streams from the Python bridge, and manage high-volume, secure transactions with the Oracle database.

#### 3. PHP: The Web Portal & Ticketing Interface
* **The Role:** A modern, accessible web interface for engineers to manage tickets, view dashboards, and update statuses.
* **The Rationale:** PHP remains one of the most efficient languages for rendering dynamic HTML and handling web-based CRUD (Create, Read, Update, Delete) operations. It allows for a stateless, fast-loading web portal that communicates flawlessly with MySQL, enabling NOC engineers to access the system via any browser without needing heavy client-side rendering.

#### 4. Dual Database Strategy (Oracle + MySQL)
* **The Role:** Data persistence distributed by workload.
* **The Rationale:** 
  * **Oracle DB:** Used by the Java Backend. It is highly optimized for complex relational telemetry, massive audit trails (`TICKET_HISTORY`), and strict ACID compliance required for enterprise logging.
  * **MySQL:** Used by the PHP Web Portal. It provides a lightweight, highly responsive database dedicated purely to web session management, UI state, and ticketing metadata, keeping web traffic isolated from heavy backend analytics.

#### 5. ESP32 (IoT): The Physical Edge
* **The Role:** Environmental monitoring and physical alert mechanisms.
* **The Rationale:** Network health isn't just about software; server room temperature and physical security matter. The ESP32 is a cost-effective microcontroller that bridges the digital NOC with the physical world via WebSockets, allowing NetPulse to trigger physical alarms or monitor server rack environments in real-time.

---

## ⚙️ System Architecture

```text
                  ┌───────────────────────────┐
                  │ Network Simulation Script │
                  │     (Python Generator)    │
                  └─────────────┬─────────────┘
                                │ Raw Events
                                ▼
                  ┌───────────────────────────┐
                  │       Python Bridge       │
                  │   Collect & Normalize     │
                  └─────────────┬─────────────┘
                                │ JSON Payload
                                ▼
                  ┌───────────────────────────┐
                  │        Java Backend       │
                  │    Core Business Logic    │
                  └──────┬─────────────┬──────┘
             JDBC        │             │        WebSocket
            ┌────────────┘             └────────────┐
            ▼                                       ▼
      ┌───────────┐                           ┌───────────┐
      │  Oracle   │                           │ WebSocket │
      └───────────┘                           └─────┬─────┘
                                                    │
                             ┌──────────────────────┼──────────────────────┐
                             ▼                      ▼                      ▼
                     Desktop Client            Web Portal              ESP32 IoT
                      (Java UI)                  (PHP)                 (Physical)

```

---

## 🗂️ Core Components

| Component | Technology | Responsibility |
| --- | --- | --- |
| **Network** | Python Script | Simulates network topology & events |
| **Bridge** | Python | Event buffering & JSON normalization |
| **Backend** | Java | Concurrency, alerting & core logic |
| **Desktop** | Java | Heavy-duty real-time NOC dashboard |
| **Web Portal** | PHP 8.x | Agile ticket & incident management |
| **IoT Edge** | ESP32 (C++) | Physical alarms & telemetry |
| **Databases** | Oracle & MySQL | Distributed data persistence |

---

## 🔄 The Event Lifecycle

NetPulse ensures every anomaly is tracked from detection to resolution with an immutable audit trail:

1. **Detection:** Python script detects a simulated failure (e.g., *Switch Port Down*).
2. **Ingestion:** Python Bridge normalizes the event and pushes it to the backend.
3. **Processing:** Java evaluates the event against defined thresholds (e.g., *Is this a CRITICAL link?*).
4. **Alerting:** An Alert is fired via WebSocket to the Desktop/Web clients.
5. **Ticketing:** If sustained, an automated incident ticket (`TKT-2026-XXXX`) is generated.
6. **Action:** An engineer assigns and updates the ticket via the PHP Portal.
7. **Resolution:** The network heals, the ticket is closed, and Oracle logs the immutable history.

---

## 📂 Repository Structure

```text
NetPulse/
│
├── backend/          # Java Enterprise Backend & Socket Servers
├── bridge/           # Python Data Ingestion Scripts
├── desktop/          # Java Desktop UI (NOC Dashboard)
├── web/              # PHP Ticket Management Portal
├── firmware/         # ESP32 C++ Microcontroller Code
├── database/         # DDL/DML Scripts (Oracle & MySQL)
├── network/          # Python Network Simulation Assets
├── docs/             # Technical Architecture Documentation

```

---

## 🚀 Development Roadmap

The project embraces an iterative, modular development approach:

* [x] **Phase 0:** Core Architecture & Schema Design
* [ ] **Phase 1:** Python Network Simulation & Event Generation
* [ ] **Phase 2:** Event Ingestion & Normalization Bridge
* [ ] **Phase 3:** Java Backend Event Processing Engine
* [ ] **Phase 4:** PHP Web Portal & Ticketing System (`TicketController` & Services)
* [ ] **Phase 5:** Real-Time WebSocket Dashboards
* [ ] **Phase 6:** IoT Integration (ESP32)
* [ ] **Phase 7:** End-to-End Testing & Refinement

---

## 📖 Documentation

Comprehensive technical documentation focusing on Clean Architecture, database schemas, and API contracts is available in the `/docs` directory.

Recommended reading sequence:
**Architecture → Requirements → Database → Integration → Implementation**

---

## 🛡️ License & Status

> 🚧 **Status:** Active Development
> This project is currently developed as an advanced academic and engineering initiative to demonstrate enterprise software architecture, asynchronous communication, and modern NOC principles.

---

### 👨‍💻 Architected & Developed by **Eng. Mohammed Fares**

*Software Engineer | Clean Architecture Enthusiast*

⭐ *If you appreciate clean code and decoupled architecture, consider giving this repository a star!*
