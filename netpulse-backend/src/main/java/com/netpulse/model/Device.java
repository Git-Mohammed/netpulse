package com.netpulse.model;

import java.time.LocalDateTime;

/**
 * Represents a monitored network device.
 */
public class Device {

    private Long deviceId;
    private String deviceName;
    private String deviceType;
    private String ipAddress;
    private String deviceRole;
    private String status;
    private LocalDateTime createdAt;
    private LocalDateTime updatedAt;

    public Long getDeviceId() { return deviceId; }
    public void setDeviceId(Long deviceId) { this.deviceId = deviceId; }
    public String getDeviceName() { return deviceName; }
    public void setDeviceName(String deviceName) { this.deviceName = deviceName; }
    public String getDeviceType() { return deviceType; }
    public void setDeviceType(String deviceType) { this.deviceType = deviceType; }
    public String getIpAddress() { return ipAddress; }
    public void setIpAddress(String ipAddress) { this.ipAddress = ipAddress; }
    public String getDeviceRole() { return deviceRole; }
    public void setDeviceRole(String deviceRole) { this.deviceRole = deviceRole; }
    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
    public LocalDateTime getCreatedAt() { return createdAt; }
    public void setCreatedAt(LocalDateTime createdAt) { this.createdAt = createdAt; }
    public LocalDateTime getUpdatedAt() { return updatedAt; }
    public void setUpdatedAt(LocalDateTime updatedAt) { this.updatedAt = updatedAt; }
}
