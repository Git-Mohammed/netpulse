package com.netpulse.repository;

import com.netpulse.model.AlertRule;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;

/**
 * JDBC repository for simple alert-rule matching.
 */
@Repository
public class AlertRuleRepository {

    private final JdbcTemplate jdbcTemplate;

    public AlertRuleRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public List<AlertRule> findEnabledRules(String eventType, String deviceRole) {
        String sql = """
                SELECT rule_id, rule_name, event_type, device_role, severity, enabled
                FROM alert_rule
                WHERE enabled = 1
                  AND event_type = ?
                  AND device_role = ?
                ORDER BY rule_id
                """;

        return jdbcTemplate.query(sql, this::mapRow, eventType, deviceRole);
    }

    private AlertRule mapRow(ResultSet rs, int rowNum) throws SQLException {
        AlertRule rule = new AlertRule();
        rule.setRuleId(rs.getLong("rule_id"));
        rule.setRuleName(rs.getString("rule_name"));
        rule.setEventType(rs.getString("event_type"));
        rule.setDeviceRole(rs.getString("device_role"));
        rule.setSeverity(rs.getString("severity"));
        rule.setEnabled(rs.getInt("enabled") == 1);
        return rule;
    }
}
