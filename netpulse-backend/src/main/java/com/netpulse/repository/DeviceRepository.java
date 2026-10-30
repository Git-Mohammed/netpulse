package com.netpulse.repository;

import com.netpulse.model.Device;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Optional;

import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;

/**
 * JDBC repository for DEVICE.
 */
@Repository
public class DeviceRepository {

    private final JdbcTemplate jdbcTemplate;

    public DeviceRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public Optional<Device> findById(Long deviceId) {
        String sql = """
                SELECT device_id, device_name, device_type, ip_address,
                       device_role, status, created_at, updated_at
                FROM device
                WHERE device_id = ?
                """;

        return jdbcTemplate.query(sql, this::mapRow, deviceId)
                .stream()
                .findFirst();
    }

    private Device mapRow(ResultSet rs, int rowNum) throws SQLException {
        Device device = new Device();
        device.setDeviceId(rs.getLong("device_id"));
        device.setDeviceName(rs.getString("device_name"));
        device.setDeviceType(rs.getString("device_type"));
        device.setIpAddress(rs.getString("ip_address"));
        device.setDeviceRole(rs.getString("device_role"));
        device.setStatus(rs.getString("status"));
        if (rs.getTimestamp("created_at") != null) {
            device.setCreatedAt(rs.getTimestamp("created_at").toLocalDateTime());
        }
        if (rs.getTimestamp("updated_at") != null) {
            device.setUpdatedAt(rs.getTimestamp("updated_at").toLocalDateTime());
        }
        return device;
    }
}
