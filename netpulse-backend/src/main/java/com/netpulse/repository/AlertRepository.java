package com.netpulse.repository;

import com.netpulse.model.Alert;


import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;

/**
 * JDBC repository for ALERT.
 */
@Repository
public class AlertRepository {

    private final JdbcTemplate jdbcTemplate;

    public AlertRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public long save(Alert alert) {
        Long alertId = jdbcTemplate.queryForObject(
                "SELECT alert_seq.NEXTVAL FROM dual",
                Long.class);

        String sql = """
                INSERT INTO alert
                    (alert_id, event_id, rule_id, message, severity, status, detected_at, ack_at, resolved_at)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?)
                """;

        jdbcTemplate.update(sql,
                alertId,
                alert.getEventId(),
                alert.getRuleId(),
                alert.getMessage(),
                alert.getSeverity(),
                alert.getStatus(),
                alert.getDetectedAt(),
                alert.getAckAt(),
                alert.getResolvedAt());

        return alertId;
    }
}
