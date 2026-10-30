package com.netpulse.service;

import com.netpulse.dto.EventProcessingResponse;
import com.netpulse.dto.TelemetryEventRequest;
import com.netpulse.exception.ResourceNotFoundException;
import com.netpulse.model.Alert;
import com.netpulse.model.AlertRule;
import com.netpulse.model.AuditLog;
import com.netpulse.model.Device;
import com.netpulse.model.Incident;
import com.netpulse.model.TelemetryEvent;
import com.netpulse.repository.AlertRepository;
import com.netpulse.repository.AlertRuleRepository;
import com.netpulse.repository.AuditLogRepository;
import com.netpulse.repository.DeviceRepository;
import com.netpulse.repository.IncidentRepository;
import com.netpulse.repository.TelemetryEventRepository;

import java.time.LocalDateTime;
import java.util.List;

import org.springframework.messaging.simp.SimpMessagingTemplate;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

/**
 * Core business service responsible for coordinating the NetPulse event-processing workflow.
 * 
 * <p>This service orchestrates the end-to-end lifecycle of incoming network telemetry data:
 * <ol>
 *   <li>Validates the target device existence.</li>
 *   <li>Persists the raw telemetry event.</li>
 *   <li>Evaluates the event against active alert rules based on device role and event type.</li>
 *   <li>Triggers and persists alerts if a matching rule is found.</li>
 *   <li>Escalates high or critical severity events into formal operational incidents.</li>
 *   <li>Records system audit logs for traceability.</li>
 *   <li>Dispatches real-time alerts and incident updates to connected clients via WebSockets.</li>
 * </ol>
 * 
 * <p>All state-changing operations within the main workflow are executed transactionally 
 * to ensure database consistency and integrity across multiple persistence layers.
 * 
 * @see com.netpulse.dto.TelemetryEventRequest
 * @see com.netpulse.dto.EventProcessingResponse
 * @see org.springframework.messaging.simp.SimpMessagingTemplate
 * @since 1.0
 */
@Service
public class EventProcessingService {

    private final DeviceRepository deviceRepository;
    private final TelemetryEventRepository telemetryEventRepository;
    private final AlertRuleRepository alertRuleRepository;
    private final AlertRepository alertRepository;
    private final IncidentRepository incidentRepository;
    private final AuditLogRepository auditLogRepository;
    private final SimpMessagingTemplate messagingTemplate;

    public EventProcessingService(
            DeviceRepository deviceRepository,
            TelemetryEventRepository telemetryEventRepository,
            AlertRuleRepository alertRuleRepository,
            AlertRepository alertRepository,
            IncidentRepository incidentRepository,
            AuditLogRepository auditLogRepository,
            SimpMessagingTemplate messagingTemplate) {
        this.deviceRepository = deviceRepository;
        this.telemetryEventRepository = telemetryEventRepository;
        this.alertRuleRepository = alertRuleRepository;
        this.alertRepository = alertRepository;
        this.incidentRepository = incidentRepository;
        this.auditLogRepository = auditLogRepository;
        this.messagingTemplate = messagingTemplate;
    }

    @Transactional
    public EventProcessingResponse process(TelemetryEventRequest request) {
        Device device = deviceRepository.findById(request.getDeviceId())
                .orElseThrow(() -> new ResourceNotFoundException(
                        "Device not found: " + request.getDeviceId()));

        LocalDateTime now = LocalDateTime.now();

        TelemetryEvent event = new TelemetryEvent();
        event.setDeviceId(device.getDeviceId());
        event.setEventType(request.getEventType().trim().toUpperCase());
        event.setSeverity(request.getSeverity());
        event.setPayload(request.getPayload());
        event.setReceivedAt(now);

        long eventId = telemetryEventRepository.save(event);
        event.setEventId(eventId);

        List<AlertRule> rules = alertRuleRepository.findEnabledRules(
                event.getEventType(),
                device.getDeviceRole());

        if (rules.isEmpty()) {
            LocalDateTime processedAt = LocalDateTime.now();
            event.setProcessedAt(processedAt);
            telemetryEventRepository.markProcessed(eventId, processedAt);
            return new EventProcessingResponse(eventId, null, null, "EVENT_STORED");
        }

        // MVP rule handling: use the first matching enabled rule.
        AlertRule rule = rules.get(0);

        Alert alert = new Alert();
        alert.setEventId(eventId);
        alert.setRuleId(rule.getRuleId());
        alert.setMessage(buildAlertMessage(device, event, rule));
        alert.setSeverity(rule.getSeverity());
        alert.setStatus("TRIGGERED");
        alert.setDetectedAt(now);

        long alertId = alertRepository.save(alert);
        alert.setAlertId(alertId);

        publishAlert(alert, device);

        Long incidentId = null;
        if (isIncidentSeverity(rule.getSeverity())) {
            Incident incident = new Incident();
            incident.setAlertId(alertId);
            incident.setTitle(device.getDeviceName() + " - " + event.getEventType());
            incident.setDescription(alert.getMessage());
            incident.setPriority(rule.getSeverity());
            incident.setStatus("OPEN");
            incident.setCreatedAt(now);

            incidentId = incidentRepository.save(incident);
            incident.setIncidentId(incidentId);

            publishIncident(incident, device);
        }

        AuditLog auditLog = new AuditLog();
        auditLog.setAction("PROCESS_EVENT");
        auditLog.setEntityType("TELEMETRY_EVENT");
        auditLog.setEntityId(eventId);
        auditLog.setDescription("Event processed and rule evaluated.");
        auditLog.setCreatedAt(now);
        auditLogRepository.save(auditLog);

        telemetryEventRepository.markProcessed(eventId, LocalDateTime.now());

        return new EventProcessingResponse(
                eventId,
                alertId,
                incidentId,
                incidentId == null ? "ALERT_CREATED" : "INCIDENT_CREATED");
    }

    private boolean isIncidentSeverity(String severity) {
        return "HIGH".equalsIgnoreCase(severity)
                || "CRITICAL".equalsIgnoreCase(severity);
    }

    private String buildAlertMessage(Device device, TelemetryEvent event, AlertRule rule) {
        return "%s: %s reported %s (%s)"
                .formatted(rule.getSeverity(), device.getDeviceName(), event.getEventType(), device.getDeviceRole());
    }

    private void publishAlert(Alert alert, Device device) {
        messagingTemplate.convertAndSend("/topic/alerts", new AlertNotification(
                "ALERT",
                alert.getAlertId(),
                device.getDeviceId(),
                device.getDeviceName(),
                alert.getSeverity(),
                alert.getMessage(),
                alert.getDetectedAt()));
    }

    private void publishIncident(Incident incident, Device device) {
        messagingTemplate.convertAndSend("/topic/incidents", new IncidentNotification(
                "INCIDENT",
                incident.getIncidentId(),
                device.getDeviceId(),
                device.getDeviceName(),
                incident.getPriority(),
                incident.getTitle(),
                incident.getCreatedAt()));
    }

    /** WebSocket payload intentionally kept small for the MVP desktop client. */
    public record AlertNotification(
            String type,
            Long alertId,
            Long deviceId,
            String deviceName,
            String severity,
            String message,
            LocalDateTime timestamp) {
    }

    /** WebSocket payload intentionally kept small for the MVP desktop client. */
    public record IncidentNotification(
            String type,
            Long incidentId,
            Long deviceId,
            String deviceName,
            String priority,
            String title,
            LocalDateTime timestamp) {
    }
}
