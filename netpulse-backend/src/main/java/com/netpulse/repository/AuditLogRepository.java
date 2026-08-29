package com.netpulse.repository;

import com.netpulse.model.AuditLog;

import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;

/**
 * JDBC repository for AUDIT_LOG.
 */
@Repository
public class AuditLogRepository {

    private final JdbcTemplate jdbcTemplate;

    public AuditLogRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public void save(AuditLog auditLog) {
        String sql = """
                INSERT INTO audit_log
                    (audit_id, user_id, action, entity_type, entity_id, description, created_at)
                VALUES
                    (audit_log_seq.NEXTVAL, ?, ?, ?, ?, ?, ?)
                """;

        jdbcTemplate.update(sql,
                auditLog.getUserId(),
                auditLog.getAction(),
                auditLog.getEntityType(),
                auditLog.getEntityId(),
                auditLog.getDescription(),
                auditLog.getCreatedAt());
    }
}
