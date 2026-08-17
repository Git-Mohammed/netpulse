# NetPulse NOC

**Hybrid Network + IoT + Enterprise Software Platform**

NetPulse NOC is an end-to-end, real-time monitoring platform designed for small or medium enterprise network sites. It solves a critical operational challenge by combining physical environment faults (IoT) and network reachability (Cisco/OSPF) into a single, unified operational view.

Unlike a simple dashboard or a polling-only tool, NetPulse is a complete operational system featuring an edge layer, network transport, event-processing pipeline, durable data store, desktop operations UI, and web administration surface.

## 🏗️ Architecture & Technology Stack

The system is built as a modular monolith, growing vertically through stable boundaries with clear responsibilities.

* **Core Backend:** Java 21 (Receives, validates, normalizes, processes, and exposes APIs).


* **Operational Database:** Oracle (Durable system of record for network, telemetry, and alerts).


* **Live Desktop UI:** JavaFX (Real-time NOC operational console).


* **Web Portal:** PHP (Administration, reporting, and web workflows).


* **Web Database:** MySQL 8.4+ (Users, roles, portal settings, and web audit data).


* **Network Foundation:** Cisco Routers/Switches utilizing OSPF for the routed underlay.


* **Physical Edge:** Arduino sensor nodes (Generating physical telemetry via HTTP JSON).



## ✨ Key Features

* **Unified Telemetry Ingestion:** Processes network telemetry via WebSocket/TLS and IoT sensor data via HTTP JSON.


* **Streaming Telemetry:** Reduces fault detection latency by utilizing streaming updates over traditional polling methods.


* **Intelligent Alert Engine:** Evaluates operational rules, state transitions, and manages the complete alert lifecycle (Normal -> Candidate -> Active -> Acknowledged -> Closed).


* **Strict Data Segregation:** Oracle handles high-volume operational truth, while MySQL independently manages web portal state and user governance.


* **Dual-Interface Operations:** Features a JavaFX NOC console for low-latency, real-time monitoring, and a PHP/MySQL portal for administrative reporting.



## 📂 Repository Structure

The codebase is organized to support the end-to-end architecture:

```text
netpulse-noc/
├── docs/                 # Architecture ADRs, network designs, and runbooks
├── backend/              # Java 21 Middleware (Domain, App, Infra, API bootstrap)
├── desktop/              # JavaFX NOC Console
├── web/                  # PHP Portal
├── database/
│   ├── oracle/           # Telemetry schemas (Devices, Interfaces, Metrics, Alerts)
│   └── mysql/            # Web application schemas (Users, Roles, Portal)
├── edge/                 # Arduino sensor and actuator sketches
├── network/              # Cisco lab topologies and OSPF configurations
└── tests/                # Integration and load testing suites

```

## 🚀 Implementation Roadmap

Development of NetPulse NOC follows a strict "Vertical Slice" phase-gated approach. Do not attempt to build every component simultaneously.

* **Phase 0-1:** Foundations, environment setup, and basic Java backend health checks.


* **Phase 2-3:** Arduino sensor networking over OSPF and core device registry persistence in Oracle.


* **Phase 4-5:** Cisco WebSocket ingestion and the foundational JavaFX live streaming dashboard.


* **Phase 6-7:** Metrics, alert engine, and the PHP/MySQL administrative portal.


* **Phase 8-10:** Topology views, IoT actuator control, High Availability (HA), and production load testing.



**Phase Gate Rule:** Do not advance to the next phase simply because the code compiles. Advance only when the current phase has a repeatable demo, automated tests, documented failure behavior, and a corresponding runbook.

## 🛡️ Security & Integrity

* Network boundaries enforce strict isolation: Arduino and JavaFX clients never connect directly to Oracle or MySQL.


* TLS is utilized for operator-to-middleware and device-to-middleware traffic.


* Cisco telemetry networks remain isolated from operational databases.


* The Java middleware acts as the absolute trust and business-logic boundary.



---

*Prepared by Eng. Mohammed Fares* | *Version 1.0 (Baseline Reference)*
