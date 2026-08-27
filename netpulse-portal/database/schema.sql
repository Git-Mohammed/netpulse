-- =========================================================================
-- Project: NetPulse Ticketing Portal
-- Author: Mohammed Bin Fares
-- Engine: MySQL / InnoDB
-- Description: Core Database Architecture and Relational Schema
-- =========================================================================

-- 1. Create Database with Arabic encoding support
CREATE DATABASE IF NOT EXISTS netpulse_portal 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE netpulse_portal;

-- =========================================================================

-- 2. Create WEB_USER Table
CREATE TABLE WEB_USER (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'ENGINEER') NOT NULL DEFAULT 'ENGINEER',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (user_id),
    UNIQUE KEY uk_username (username),
    UNIQUE KEY uk_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================

-- 3. Create TICKET Table
CREATE TABLE TICKET (
    ticket_id INT(11) NOT NULL AUTO_INCREMENT,
    ticket_number VARCHAR(20) NOT NULL,
    incident_id INT(11) DEFAULT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('CRITICAL', 'HIGH', 'MEDIUM', 'LOW') NOT NULL DEFAULT 'MEDIUM',
    status ENUM('OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED') NOT NULL DEFAULT 'OPEN',
    assigned_to INT(11) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    closed_at DATETIME DEFAULT NULL,
    
    PRIMARY KEY (ticket_id),
    UNIQUE KEY uk_ticket_number (ticket_number),
    
    CONSTRAINT fk_ticket_assigned_user FOREIGN KEY (assigned_to) 
        REFERENCES WEB_USER (user_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================

-- 4. Create TICKET_HISTORY (Audit Trail) Table
CREATE TABLE TICKET_HISTORY (
    history_id INT(11) NOT NULL AUTO_INCREMENT,
    ticket_id INT(11) NOT NULL,
    changed_by INT(11) DEFAULT NULL,
    old_status VARCHAR(20) NOT NULL,
    new_status VARCHAR(20) NOT NULL,
    change_note TEXT DEFAULT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (history_id),
    
    CONSTRAINT fk_history_ticket FOREIGN KEY (ticket_id) 
        REFERENCES TICKET (ticket_id) ON DELETE CASCADE ON UPDATE CASCADE,
        
    CONSTRAINT fk_history_user FOREIGN KEY (changed_by) 
        REFERENCES WEB_USER (user_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================

-- =========================================================================
-- Project: NetPulse Ticketing Portal
-- Seed Data: Extended and comprehensive test records for stress testing & UI evaluation
-- =========================================================================

USE netpulse_portal;

-- Disable foreign key checks temporarily for smooth bulk insertion
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE TICKET_HISTORY;
TRUNCATE TABLE TICKET;
TRUNCATE TABLE WEB_USER;
SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================================
-- 1. Insert Diverse System Users (Admins & Engineers)
-- Note: Passwords are pre-hashed using BCRYPT for 'Password123!'
-- =========================================================================
INSERT INTO WEB_USER (user_id, username, email, password_hash, role, created_at) VALUES
(1, 'admin_fares', 'fares@netpulse.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', '2026-01-10 08:00:00'),
(2, 'eng_sara', 'sara@netpulse.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ENGINEER', '2026-01-12 09:30:00'),
(3, 'eng_khaled', 'khaled@netpulse.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ENGINEER', '2026-01-15 10:15:00'),
(4, 'eng_layla', 'layla@netpulse.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ENGINEER', '2026-02-01 11:00:00'),
(5, 'eng_omar', 'omar@netpulse.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ENGINEER', '2026-02-10 14:20:00'),
(6, 'admin_reem', 'reem@netpulse.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', '2026-03-01 08:45:00');

-- =========================================================================
-- 2. Insert Comprehensive Operational Tickets across all Priorities & Statuses
-- =========================================================================
INSERT INTO TICKET (ticket_id, ticket_number, incident_id, title, description, priority, status, assigned_to, created_at, updated_at, closed_at) VALUES
(1, 'TKT-2026-0001', 10045, 'Core Switch Packet Loss - Sector A', 'High packet loss detected on the primary core switch at Sector A data center affecting backbone routing.', 'CRITICAL', 'IN_PROGRESS', 2, '2026-08-10 09:15:00', '2026-08-10 11:00:00', NULL),
(2, 'TKT-2026-0002', 10046, 'VPN Gateway Latency Spike', 'Remote branch offices experiencing high latency connecting through the main enterprise VPN gateway.', 'HIGH', 'OPEN', 3, '2026-08-12 10:20:00', NULL, NULL),
(3, 'TKT-2026-0003', NULL, 'DNS Resolution Failure in VLAN 20', 'Internal workstations on VLAN 20 unable to resolve internal domain names and active directory services.', 'MEDIUM', 'OPEN', NULL, '2026-08-13 14:00:00', NULL, NULL),
(4, 'TKT-2026-0004', 10050, 'Fiber Optic Link Degradation (Line 4)', 'Signal attenuation reported on optical fiber line 4 connecting to North Tower distribution box.', 'CRITICAL', 'IN_PROGRESS', 4, '2026-08-14 06:30:00', '2026-08-14 08:15:00', NULL),
(5, 'TKT-2026-0005', NULL, 'Printer VLAN IP Conflict', 'Duplicate IP address assignment causing intermittent connectivity loss on floor 3 printer pool.', 'LOW', 'RESOLVED', 5, '2026-08-15 11:45:00', '2026-08-16 15:30:00', '2026-08-16 15:30:00'),
(6, 'TKT-2026-0006', 10058, 'BGP Route Flapping on Edge Router 2', 'Frequent route withdrawals observed on external BGP peering link with upstream ISP provider.', 'CRITICAL', 'OPEN', 2, '2026-08-17 01:10:00', NULL, NULL),
(7, 'TKT-2026-0007', NULL, 'Wi-Fi Authentication Timeout (Guest SSID)', 'Users unable to complete captive portal authentication due to RADIUS server timeout errors.', 'HIGH', 'IN_PROGRESS', 3, '2026-08-18 09:00:00', '2026-08-18 10:30:00', NULL),
(8, 'TKT-2026-0008', 10062, 'Firewall Rule Blockage on Port 8443', 'API microservices unable to communicate across DMZ due to strict outbound firewall filtering rules.', 'MEDIUM', 'CLOSED', 4, '2026-08-19 13:20:00', '2026-08-20 09:00:00', '2026-08-20 09:00:00'),
(9, 'TKT-2026-0009', NULL, 'Storage SAN Multipath Failure', 'Primary storage area network controller lost redundant path connection on controller B.', 'CRITICAL', 'IN_PROGRESS', 5, '2026-08-20 16:40:00', '2026-08-20 18:10:00', NULL),
(10, 'TKT-2026-0010', 10070, 'NTP Synchronization Drift on Rack 5', 'System clocks across servers in rack 5 drifting by more than 500ms causing authentication token expiry issues.', 'LOW', 'OPEN', NULL, '2026-08-21 08:15:00', NULL, NULL),
(11, 'TKT-2026-0011', NULL, 'Unresponsive Access Point in Cafeteria', 'Ceiling-mounted wireless access point crashed and stopped broadcasting SSID signals.', 'LOW', 'RESOLVED', 3, '2026-08-21 11:30:00', '2026-08-21 14:00:00', '2026-08-21 14:00:00'),
(12, 'TKT-2026-0012', 10075, 'SQL Server Deadlocks on Transaction Log', 'High concurrency workloads triggering frequent database deadlocks during peak billing hours.', 'HIGH', 'OPEN', 2, '2026-08-22 07:50:00', NULL, NULL);

-- =========================================================================
-- 3. Insert Detailed Ticket History & Audit Trail
-- =========================================================================
INSERT INTO TICKET_HISTORY (history_id, ticket_id, changed_by, old_status, new_status, change_note, changed_at) VALUES
(1, 1, 1, 'OPEN', 'IN_PROGRESS', 'Ticket reviewed and assigned to Eng. Sara for immediate hardware inspection.', '2026-08-10 11:00:00'),
(2, 4, 1, 'OPEN', 'IN_PROGRESS', 'Escalated to fiber optic specialized response team under Eng. Layla.', '2026-08-14 08:15:00'),
(3, 5, 3, 'OPEN', 'IN_PROGRESS', 'Investigating DHCP lease table and static assignments.', '2026-08-15 13:00:00'),
(4, 5, 3, 'IN_PROGRESS', 'RESOLVED', 'IP conflict resolved by reserving static leases for printer pool devices.', '2026-08-16 15:30:00'),
(5, 7, 1, 'OPEN', 'IN_PROGRESS', 'Assigned to Eng. Khaled to check RADIUS backend connectivity.', '2026-08-18 10:30:00'),
(6, 8, 4, 'OPEN', 'IN_PROGRESS', 'Rule reviewed with security officer and approved.', '2026-08-19 15:00:00'),
(7, 8, 4, 'IN_PROGRESS', 'CLOSED', 'Firewall rule applied successfully and verified via test curl requests.', '2026-08-20 09:00:00'),
(8, 9, 1, 'OPEN', 'IN_PROGRESS', 'High priority SAN alert dispatched to Eng. Omar for fiber channel cable check.', '2026-08-20 18:10:00'),
(9, 11, 3, 'OPEN', 'RESOLVED', 'Power cycle performed via PoE switch port reset. AP operational.', '2026-08-21 14:00:00');

-- =========================================================================