package com.netpulse.repository;

import com.netpulse.model.TelemetryEvent;

import java.sql.ResultSet;
import java.sql.SQLException;

import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;


/**
 * JDBC repository for TELEMETRY_EVENT.
 */
@Repository
public class TelemetryEventRepository {
     private final JdbcTemplate jdbcTemplate;

    public TelemetryEventRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public long save(TelemetryEvent event) {
        Long eventId = jdbcTemplate.queryForObject(
                "SELECT telemetry_event_seq.NEXTVAL FROM dual",
                Long.class);

        String sql = """
                INSERT INTO telemetry_event
                    (event_id, device_id, event_type, severity, payload, received_at, processed_at)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?)
                """;

        jdbcTemplate.update(sql,
                eventId,
                event.getDeviceId(),
                event.getEventType(),
                event.getSeverity(),
                event.getPayload(),
                event.getReceivedAt(),
                event.getProcessedAt());

        return eventId;
    }

    public void markProcessed(long eventId, java.time.LocalDateTime processedAt) {
        jdbcTemplate.update(
                "UPDATE telemetry_event SET processed_at = ? WHERE event_id = ?",
                processedAt, eventId);
    }

    @SuppressWarnings("unused")
    private TelemetryEvent mapRow(ResultSet rs, int rowNum) throws SQLException {
        TelemetryEvent event = new TelemetryEvent();
        event.setEventId(rs.getLong("event_id"));
        event.setDeviceId(rs.getLong("device_id"));
        event.setEventType(rs.getString("event_type"));
        event.setSeverity(rs.getString("severity"));
        event.setPayload(rs.getString("payload"));
        if (rs.getTimestamp("received_at") != null) {
            event.setReceivedAt(rs.getTimestamp("received_at").toLocalDateTime());
        }
        if (rs.getTimestamp("processed_at") != null) {
            event.setProcessedAt(rs.getTimestamp("processed_at").toLocalDateTime());
        }
        return event;
    }
}
