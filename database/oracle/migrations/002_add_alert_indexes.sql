-- ============================================================
-- NetPulse NOC Database
-- Migration: 002_add_alert_indexes
-- Purpose : Add indexes for frequently joined/filtered columns
-- Target  : Oracle Database Free
-- ============================================================

-- ============================================================
-- Alerts
-- ============================================================

CREATE INDEX idx_alerts_rule_id
    ON Alerts (RuleId);

CREATE INDEX idx_alerts_event_id
    ON Alerts (EventId);

CREATE INDEX idx_alerts_status
    ON Alerts (Status);

-- ============================================================
-- TelemetryEvents
-- ============================================================

CREATE INDEX idx_telemetryevents_device_id
    ON TelemetryEvents (DeviceId);

CREATE INDEX idx_telemetryevents_severity
    ON TelemetryEvents (Severity);

CREATE INDEX idx_telemetryevents_event_type
    ON TelemetryEvents (EventType);

-- ============================================================
-- Incidents
-- ============================================================

CREATE INDEX idx_incidents_alert_id
    ON Incidents (AlertId);

CREATE INDEX idx_incidents_assigned_to
    ON Incidents (AssignedTo);

CREATE INDEX idx_incidents_status
    ON Incidents (Status);

-- ============================================================
-- Audits
-- ============================================================

CREATE INDEX idx_audits_user_id
    ON Audits (UserId);

CREATE INDEX idx_audits_entity_type
    ON Audits (EntityType);

-- ============================================================
-- AlertRules
-- ============================================================

CREATE INDEX idx_alertrules_status
    ON AlertRules (Status);

CREATE INDEX idx_alertrules_entity_type
    ON AlertRules (EntityType);

COMMIT;

-- ============================================================
-- Migration completed
-- ============================================================