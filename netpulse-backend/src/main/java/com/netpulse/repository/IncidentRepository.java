package com.netpulse.repository;

import com.netpulse.model.Incident;


import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;

/**
 * JDBC repository for INCIDENT.
 */
@Repository
public class IncidentRepository {

    private final JdbcTemplate jdbcTemplate;

    public IncidentRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public long save(Incident incident) {
        Long incidentId = jdbcTemplate.queryForObject(
                "SELECT incident_seq.NEXTVAL FROM dual",
                Long.class);

        String sql = """
                INSERT INTO incident
                    (incident_id, alert_id, title, description, priority, status,
                     assigned_to, created_at, resolved_at, closed_at)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                """;

        jdbcTemplate.update(sql,
                incidentId,
                incident.getAlertId(),
                incident.getTitle(),
                incident.getDescription(),
                incident.getPriority(),
                incident.getStatus(),
                incident.getAssignedTo(),
                incident.getCreatedAt(),
                incident.getResolvedAt(),
                incident.getClosedAt());

        return incidentId;
    }
}
