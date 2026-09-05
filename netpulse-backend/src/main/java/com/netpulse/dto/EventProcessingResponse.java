package com.netpulse.dto;

/**
 * Small response returned after an event has been accepted and processed.
 */
public class EventProcessingResponse {

    private Long eventId;
    private Long alertId;
    private Long incidentId;
    private String result;

    public EventProcessingResponse() {
    }

    public EventProcessingResponse(Long eventId, Long alertId, Long incidentId, String result) {
        this.eventId = eventId;
        this.alertId = alertId;
        this.incidentId = incidentId;
        this.result = result;
    }

    public Long getEventId() { return eventId; }
    public Long getAlertId() { return alertId; }
    public Long getIncidentId() { return incidentId; }
    public String getResult() { return result; }
}
