from dataclasses import dataclass, asdict
from datetime import datetime

@dataclass
class NetworkEvent:
    event_type: str        # مثل: LINK_DOWN, LINK_UP
    device_name: str       # مثل: Core-R2
    severity: str          # مثل: CRITICAL, INFO
    message: str           # نص الرسالة التفصيلي
    timestamp: str         # وقت الحدث
    raw_message: str       # الرسالة الخام الأصلية

    def to_dict(self):
        return asdict(self)