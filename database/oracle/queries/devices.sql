-- ============================================================
-- NetPulse NOC
-- Query File: devices.sql
-- Purpose  : Device monitoring and operational queries
-- ============================================================


-- ============================================================
-- 1. List all devices
-- ============================================================

SELECT
    DeviceId,
    Name,
    Type,
    IP,
    Status,
    Role
FROM Devices
ORDER BY DeviceId;


-- ============================================================
-- 2. List only online devices
-- ============================================================

SELECT
    DeviceId,
    Name,
    Type,
    IP,
    Role
FROM Devices
WHERE Status = 'Online'
ORDER BY Name;


-- ============================================================
-- 3. List devices requiring attention
-- ============================================================

SELECT
    DeviceId,
    Name,
    Type,
    IP,
    Status,
    Role
FROM Devices
WHERE Status IN ('Offline', 'Warning', 'Maintenance')
ORDER BY
    CASE Status
        WHEN 'Offline' THEN 1
        WHEN 'Warning' THEN 2
        WHEN 'Maintenance' THEN 3
        ELSE 4
    END,
    Name;


-- ============================================================
-- 4. Count devices by status
-- ============================================================

SELECT
    Status,
    COUNT(*) AS DeviceCount
FROM Devices
GROUP BY Status
ORDER BY DeviceCount DESC;


-- ============================================================
-- 5. Count devices by type
-- ============================================================

SELECT
    Type,
    COUNT(*) AS DeviceCount
FROM Devices
GROUP BY Type
ORDER BY DeviceCount DESC;


-- ============================================================
-- 6. Count devices by network role
-- ============================================================

SELECT
    Role,
    COUNT(*) AS DeviceCount
FROM Devices
GROUP BY Role
ORDER BY DeviceCount DESC;


-- ============================================================
-- 7. Find a device by IP address
-- ============================================================

SELECT
    DeviceId,
    Name,
    Type,
    IP,
    Status,
    Role
FROM Devices
WHERE IP = '192.168.1.1';


-- ============================================================
-- 8. Find devices by type
-- ============================================================

SELECT
    DeviceId,
    Name,
    IP,
    Status,
    Role
FROM Devices
WHERE Type = 'Switch'
ORDER BY Name;


-- ============================================================
-- 9. Find devices by network role
-- ============================================================

SELECT
    DeviceId,
    Name,
    Type,
    IP,
    Status
FROM Devices
WHERE Role = 'Core'
ORDER BY Name;


-- ============================================================
-- 10. Device telemetry summary
-- ============================================================

SELECT
    d.DeviceId,
    d.Name,
    d.Type,
    d.IP,
    d.Status,
    COUNT(t.EventId) AS TelemetryEventCount
FROM Devices d
LEFT JOIN TelemetryEvents t
    ON t.DeviceId = d.DeviceId
GROUP BY
    d.DeviceId,
    d.Name,
    d.Type,
    d.IP,
    d.Status
ORDER BY
    TelemetryEventCount DESC,
    d.Name;


-- ============================================================
-- 11. Devices with critical or emergency telemetry
-- ============================================================

SELECT DISTINCT
    d.DeviceId,
    d.Name,
    d.Type,
    d.IP,
    d.Status,
    t.Severity,
    t.EventType
FROM Devices d
JOIN TelemetryEvents t
    ON t.DeviceId = d.DeviceId
WHERE t.Severity IN ('Critical', 'Emergency')
ORDER BY
    CASE t.Severity
        WHEN 'Emergency' THEN 1
        WHEN 'Critical' THEN 2
        ELSE 3
    END,
    d.Name;


-- ============================================================
-- 12. Complete device operational view
-- ============================================================

SELECT
    d.DeviceId,
    d.Name,
    d.Type,
    d.IP,
    d.Status AS DeviceStatus,
    d.Role AS DeviceRole,
    COUNT(t.EventId) AS TotalEvents,
    SUM(
        CASE
            WHEN t.Severity = 'Emergency' THEN 1
            ELSE 0
        END
    ) AS EmergencyEvents,
    SUM(
        CASE
            WHEN t.Severity = 'Critical' THEN 1
            ELSE 0
        END
    ) AS CriticalEvents,
    SUM(
        CASE
            WHEN t.Severity = 'Warning' THEN 1
            ELSE 0
        END
    ) AS WarningEvents
FROM Devices d
LEFT JOIN TelemetryEvents t
    ON t.DeviceId = d.DeviceId
GROUP BY
    d.DeviceId,
    d.Name,
    d.Type,
    d.IP,
    d.Status,
    d.Role
ORDER BY
    EmergencyEvents DESC,
    CriticalEvents DESC,
    WarningEvents DESC,
    d.Name;