-- NetPulse MVP Oracle schema.
-- Run this script using a schema with privileges to create tables and sequences.

CREATE TABLE device (
    device_id NUMBER PRIMARY KEY,
    device_name VARCHAR2(100) NOT NULL,
    device_type VARCHAR2(50),
    ip_address VARCHAR2(45),
    device_role VARCHAR2(50) NOT NULL,
    status VARCHAR2(20) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

CREATE TABLE alert_rule (
    rule_id NUMBER PRIMARY KEY,
    rule_name VARCHAR2(100) NOT NULL,
    event_type VARCHAR2(50) NOT NULL,
    device_role VARCHAR2(50) NOT NULL,
    severity VARCHAR2(20) NOT NULL,
    enabled NUMBER(1) DEFAULT 1 NOT NULL,
    CONSTRAINT ck_alert_rule_enabled CHECK (enabled IN (0, 1))
);

CREATE TABLE user_account (
    user_id NUMBER PRIMARY KEY,
    username VARCHAR2(100) UNIQUE NOT NULL,
    password_hash VARCHAR2(255) NOT NULL,
    full_name VARCHAR2(150),
    role VARCHAR2(30) NOT NULL,
    status VARCHAR2(20) NOT NULL,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE telemetry_event (
    event_id NUMBER PRIMARY KEY,
    device_id NUMBER NOT NULL,
    event_type VARCHAR2(50) NOT NULL,
    severity VARCHAR2(20),
    payload CLOB,
    received_at TIMESTAMP NOT NULL,
    processed_at TIMESTAMP,
    CONSTRAINT fk_event_device FOREIGN KEY (device_id) REFERENCES device(device_id)
);

CREATE TABLE alert (
    alert_id NUMBER PRIMARY KEY,
    event_id NUMBER NOT NULL,
    rule_id NUMBER NOT NULL,
    message VARCHAR2(500) NOT NULL,
    severity VARCHAR2(20) NOT NULL,
    status VARCHAR2(30) NOT NULL,
    detected_at TIMESTAMP NOT NULL,
    ack_at TIMESTAMP,
    resolved_at TIMESTAMP,
    CONSTRAINT fk_alert_event FOREIGN KEY (event_id) REFERENCES telemetry_event(event_id),
    CONSTRAINT fk_alert_rule FOREIGN KEY (rule_id) REFERENCES alert_rule(rule_id)
);

CREATE TABLE incident (
    incident_id NUMBER PRIMARY KEY,
    alert_id NUMBER NOT NULL,
    title VARCHAR2(200) NOT NULL,
    description CLOB,
    priority VARCHAR2(20) NOT NULL,
    status VARCHAR2(30) NOT NULL,
    assigned_to NUMBER,
    created_at TIMESTAMP NOT NULL,
    resolved_at TIMESTAMP,
    closed_at TIMESTAMP,
    CONSTRAINT fk_incident_alert FOREIGN KEY (alert_id) REFERENCES alert(alert_id),
    CONSTRAINT fk_incident_user FOREIGN KEY (assigned_to) REFERENCES user_account(user_id)
);

CREATE TABLE audit_log (
    audit_id NUMBER PRIMARY KEY,
    user_id NUMBER,
    action VARCHAR2(100) NOT NULL,
    entity_type VARCHAR2(50) NOT NULL,
    entity_id NUMBER,
    description VARCHAR2(500),
    created_at TIMESTAMP NOT NULL,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES user_account(user_id)
);

CREATE SEQUENCE telemetry_event_seq START WITH 1 INCREMENT BY 1;
CREATE SEQUENCE alert_seq START WITH 1 INCREMENT BY 1;
CREATE SEQUENCE incident_seq START WITH 1 INCREMENT BY 1;
CREATE SEQUENCE audit_log_seq START WITH 1 INCREMENT BY 1;

INSERT INTO device (
    device_id, device_name, device_type, ip_address, device_role, status, created_at, updated_at
) VALUES (
    1, 'Core-R2', 'Switch', '192.168.1.20', 'CORE', 'UP', SYSTIMESTAMP, SYSTIMESTAMP
);

INSERT INTO alert_rule (
    rule_id, rule_name, event_type, device_role, severity, enabled
) VALUES (
    1, 'CORE_LINK_DOWN', 'INTERFACE_DOWN', 'CORE', 'CRITICAL', 1
);

COMMIT;
