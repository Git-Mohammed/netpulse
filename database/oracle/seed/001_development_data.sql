-- ============================================================
-- NetPulse NOC Database
-- Seed: 001_development_data
-- Purpose : Insert representative development/test data
-- Target  : Oracle Database Free
-- ============================================================

-- IMPORTANT:
-- Development data only.
-- Do NOT use these passwords or fake credentials in production.

-- ============================================================
-- 1. AppUsers
-- ============================================================

INSERT INTO AppUsers (
    Username,
    Password,
    FirstName,
    LastName,
    FullName,
    Role
)
VALUES (
    'm_fares',
    'HashPass123',
    'محمد',
    'بن فارس',
    'محمد بن فارس',
    'Admin'
);

INSERT INTO AppUsers (
    Username,
    Password,
    FirstName,
    LastName,
    FullName,
    Role
)
VALUES (
    'ahmed_tech',
    'HashPass456',
    'أحمد',
    'عبدالله',
    'أحمد عبدالله',
    'Engineer'
);

INSERT INTO AppUsers (
    Username,
    Password,
    FirstName,
    LastName,
    FullName,
    Role
)
VALUES (
    'salah_soc',
    'HashPass789',
    'صلاح',
    'الحسني',
    'صلاح الحسني',
    'Analyst'
);

INSERT INTO AppUsers (
    Username,
    Password,
    FirstName,
    LastName,
    FullName,
    Role
)
VALUES (
    'fatima_ops',
    'HashPass321',
    'فاطمة',
    'العمري',
    'فاطمة العمري',
    'Analyst'
);

-- ============================================================
-- 2. Devices
-- ============================================================

INSERT INTO Devices (
    Name,
    Type,
    IP,
    Status,
    Role
)
VALUES (
    'CORE-SW-TAIZ-01',
    'Switch',
    '192.168.1.1',
    'Online',
    'Core'
);

INSERT INTO Devices (
    Name,
    Type,
    IP,
    Status,
    Role
)
VALUES (
    'DIST-ROUTER-SANA-02',
    'Router',
    '192.168.2.1',
    'Warning',
    'Distribution'
);

INSERT INTO Devices (
    Name,
    Type,
    IP,
    Status,
    Role
)
VALUES (
    'FW-ADEN-EDGE-01',
    'Firewall',
    '10.0.0.1',
    'Online',
    'Gateway'
);

INSERT INTO Devices (
    Name,
    Type,
    IP,
    Status,
    Role
)
VALUES (
    'ACC-SW-HOD-03',
    'Switch',
    '192.168.3.15',
    'Offline',
    'Access'
);

INSERT INTO Devices (
    Name,
    Type,
    IP,
    Status,
    Role
)
VALUES (
    'SRV-DB-MAIN-01',
    'Server',
    '172.16.10.5',
    'Maintenance',
    'Core'
);

-- ============================================================
-- 3. AlertRules
-- ============================================================

INSERT INTO AlertRules (
    Name,
    Role,
    EntityType,
    Status
)
VALUES (
    'قاعدة ارتفاع استهلاك المعالج',
    'Core',
    'Device',
    'Active'
);

INSERT INTO AlertRules (
    Name,
    Role,
    EntityType,
    Status
)
VALUES (
    'قاعدة سقوط منافذ الشبكة',
    'Access',
    'Port',
    'Active'
);

INSERT INTO AlertRules (
    Name,
    Role,
    EntityType,
    Status
)
VALUES (
    'قاعدة هجمات الجدار الناري',
    'Gateway',
    'Firewall',
    'Active'
);

INSERT INTO AlertRules (
    Name,
    Role,
    EntityType,
    Status
)
VALUES (
    'قاعدة انقطاع الاتصال العام',
    'Distribution',
    'Device',
    'Inactive'
);

-- ============================================================
-- 4. TelemetryEvents
-- ============================================================

INSERT INTO TelemetryEvents (
    DeviceId,
    EventType,
    Payload,
    Severity
)
VALUES (
    1,
    'CPU_High',
    'CPU usage reached 92% on Core Switch',
    'Warning'
);

INSERT INTO TelemetryEvents (
    DeviceId,
    EventType,
    Payload,
    Severity
)
VALUES (
    4,
    'Port_Down',
    'GigabitEthernet0/1 went down unexpectedly',
    'Critical'
);

INSERT INTO TelemetryEvents (
    DeviceId,
    EventType,
    Payload,
    Severity
)
VALUES (
    3,
    'DDoS_Attempt',
    'High volume SYN flood detected from external IP',
    'Emergency'
);

INSERT INTO TelemetryEvents (
    DeviceId,
    EventType,
    Payload,
    Severity
)
VALUES (
    2,
    'Memory_Spike',
    'RAM utilization surpassed 85%',
    'Warning'
);

INSERT INTO TelemetryEvents (
    DeviceId,
    EventType,
    Payload,
    Severity
)
VALUES (
    5,
    'Service_Restart',
    'Database service restarted successfully for maintenance',
    'Info'
);

-- ============================================================
-- 5. Alerts
-- ============================================================

INSERT INTO Alerts (
    RuleId,
    EventId,
    Message,
    Status
)
VALUES (
    1,
    1,
    'تحذير: استهلاك المعالج تجاوز الحد الطبيعي في سويتش النواة',
    'Active'
);

INSERT INTO Alerts (
    RuleId,
    EventId,
    Message,
    Status
)
VALUES (
    2,
    2,
    'حرج: انقطاع تام في منفذ جهاز الوصول',
    'Acknowledged'
);

INSERT INTO Alerts (
    RuleId,
    EventId,
    Message,
    Status
)
VALUES (
    3,
    3,
    'طوارئ: رصد محاولة هجوم حجب خدمة على الجدار الناري',
    'Resolved'
);

INSERT INTO Alerts (
    RuleId,
    EventId,
    Message,
    Status
)
VALUES (
    1,
    4,
    'تحذير: ضغط عالي على ذاكرة الراوتر الموزع',
    'Active'
);

-- ============================================================
-- 6. Incidents
-- ============================================================

INSERT INTO Incidents (
    AlertId,
    AssignedTo,
    Status
)
VALUES (
    1,
    1,
    'Open'
);

INSERT INTO Incidents (
    AlertId,
    AssignedTo,
    Status
)
VALUES (
    2,
    2,
    'In_Progress'
);

INSERT INTO Incidents (
    AlertId,
    AssignedTo,
    Status
)
VALUES (
    3,
    3,
    'Closed'
);

INSERT INTO Incidents (
    AlertId,
    AssignedTo,
    Status
)
VALUES (
    4,
    4,
    'Escalated'
);

-- ============================================================
-- 7. Audits
-- ============================================================

INSERT INTO Audits (
    UserId,
    Action,
    EntityType
)
VALUES (
    1,
    'CREATE',
    'Device'
);

INSERT INTO Audits (
    UserId,
    Action,
    EntityType
)
VALUES (
    2,
    'UPDATE',
    'Alert'
);

INSERT INTO Audits (
    UserId,
    Action,
    EntityType
)
VALUES (
    3,
    'ACKNOWLEDGE',
    'Incident'
);

INSERT INTO Audits (
    UserId,
    Action,
    EntityType
)
VALUES (
    4,
    'DELETE',
    'AlertRule'
);

-- ============================================================
-- Commit seed data
-- ============================================================

COMMIT;

-- ============================================================
-- Verification
-- ============================================================

SELECT 'AppUsers' AS table_name, COUNT(*) AS row_count
FROM AppUsers
UNION ALL
SELECT 'Devices', COUNT(*)
FROM Devices
UNION ALL
SELECT 'AlertRules', COUNT(*)
FROM AlertRules
UNION ALL
SELECT 'TelemetryEvents', COUNT(*)
FROM TelemetryEvents
UNION ALL
SELECT 'Alerts', COUNT(*)
FROM Alerts
UNION ALL
SELECT 'Incidents', COUNT(*)
FROM Incidents
UNION ALL
SELECT 'Audits', COUNT(*)
FROM Audits;