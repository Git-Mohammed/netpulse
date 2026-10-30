package com.netpulse.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;

/**
 * Payload accepted from the Python Bridge.
 * The bridge is responsible for normalization before sending this request.
 */
public class TelemetryEventRequest {

    @NotNull
    private Long deviceId;

    @NotBlank
    private String eventType;

    private String severity;

    private String payload;

    public Long getDeviceId() { return deviceId; }
    public void setDeviceId(Long deviceId) { this.deviceId = deviceId; }
    public String getEventType() { return eventType; }
    public void setEventType(String eventType) { this.eventType = eventType; }
    public String getSeverity() { return severity; }
    public void setSeverity(String severity) { this.severity = severity; }
    public String getPayload() { return payload; }
    public void setPayload(String payload) { this.payload = payload; }
}
