# NetPulse Backend — MVP Feature

This module implements the core MVP workflow:

```text
Python Bridge
     ↓ POST /api/events
Java Spring Boot
     ↓
Oracle
     ↓
Alert Rule
     ↓
Alert / Incident
     ↓
WebSocket
     ↓
Desktop Client
```

## Requirements

- Java 17
- Maven 3.9+
- Oracle Database
- NetBeans or any Maven-compatible IDE

## Run

1. Create the Oracle schema using `src/main/resources/db/oracle-mvp.sql`.
2. Set `ORACLE_URL`, `ORACLE_USERNAME`, and `ORACLE_PASSWORD` as environment variables, or update `application.properties` for local development.
3. Run:

```bash
mvn spring-boot:run
```

## Test the main feature

```bash
curl -X POST http://localhost:8080/api/events \\
  -H "Content-Type: application/json" \\
  -d '{
    "deviceId": 1,
    "eventType": "INTERFACE_DOWN",
    "severity": "CRITICAL",
    "payload": "Gi0/1 is down"
  }'
```

Expected result:

```json
{
  "eventId": 1,
  "alertId": 1,
  "incidentId": 1,
  "result": "INCIDENT_CREATED"
}
```

WebSocket endpoint:

```text
ws://localhost:8080/ws
```

Subscribe to:

```text
/topic/alerts
/topic/incidents
```

## Design Notes

- JDBC is used directly; JPA/Hibernate is intentionally not required for this MVP.
- Oracle is the operational source of truth.
- Alert matching is intentionally simple: `event_type + device_role + enabled`.
- The first matching rule is used.
- Critical/High alerts create incidents.
- The ESP32 is not an event source; it is only a display consumer in the wider system.
