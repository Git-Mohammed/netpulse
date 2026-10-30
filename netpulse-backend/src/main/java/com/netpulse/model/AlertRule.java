package com.netpulse.model;

/**
 * Defines a simple rule used to classify incoming events.
 */
public class AlertRule {

    private Long ruleId;
    private String ruleName;
    private String eventType;
    private String deviceRole;
    private String severity;
    private Boolean enabled;

    public Long getRuleId() { return ruleId; }
    public void setRuleId(Long ruleId) { this.ruleId = ruleId; }
    public String getRuleName() { return ruleName; }
    public void setRuleName(String ruleName) { this.ruleName = ruleName; }
    public String getEventType() { return eventType; }
    public void setEventType(String eventType) { this.eventType = eventType; }
    public String getDeviceRole() { return deviceRole; }
    public void setDeviceRole(String deviceRole) { this.deviceRole = deviceRole; }
    public String getSeverity() { return severity; }
    public void setSeverity(String severity) { this.severity = severity; }
    public Boolean getEnabled() { return enabled; }
    public void setEnabled(Boolean enabled) { this.enabled = enabled; }
}
