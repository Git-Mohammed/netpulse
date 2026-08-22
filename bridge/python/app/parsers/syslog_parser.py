import re
from datetime import datetime
from app.models.event_model import NetworkEvent

def parse_cisco_syslog(raw_message: str) -> NetworkEvent:
    """تحليل وتحويل رسالة Syslog القادمة من أجهزة سيسكو إلى حدث قياسي نظيف"""
    
    pattern = r"<(?P<pri>\d+)>(?P<timestamp>[A-Za-z]+\s+\d+\s+\d+:\d+:\d+)\s+(?P<hostname>[\w-]+)\s+(?P<facility>%[\w-]+)-(?P<severity>\d)-(?P<mnemonic>[\w_]+):\s+(?P<text>.+)"
    
    match = re.match(pattern, raw_message.strip())
    if not match:
        return NetworkEvent(
            event_type="RAW_SYSLOG",
            device_name="UNKNOWN",
            severity="INFO",
            message=raw_message,
            timestamp=datetime.utcnow().isoformat(),
            raw_message=raw_message
        )
        
    data = match.groupdict()
    text = data["text"]
    event_type = "SYSTEM_LOG"
    severity = "INFO"
    
    if "UPDOWN" in data["mnemonic"] or "changed state" in text:
        if "down" in text.lower():
            event_type = "LINK_DOWN"
            severity = "CRITICAL"
        elif "up" in text.lower():
            event_type = "LINK_UP"
            severity = "INFO"

    return NetworkEvent(
        event_type=event_type,
        device_name=data["hostname"],
        severity=severity,
        message=text,
        timestamp=datetime.utcnow().isoformat(),
        raw_message=raw_message
    )