import asyncio
import websockets
from app.parsers.syslog_parser import parse_cisco_syslog

# ضع هنا الـ IP الفعلي لـ Server0 داخل Packet Tracer
SERVER_IP = "31.31.185.192"  
PORT = 4040
URI = f"ws://{SERVER_IP}:{PORT}/"

async def test_connection():
    print(f"Connecting to Packet Tracer Server0 at {URI}...")
    
    try:
        async with websockets.connect(URI) as websocket:
            print("Successfully connected to Server0 WebSocket Agent!")
            
            # إرسال رسالة Syslog تجريبية لسيسكو لاختبار النظام
            test_syslog = "<189>Feb 26 12:34:56 Core-R2 %LINEPROTO-5-UPDOWN: Line protocol on Interface GigabitEthernet0/0, changed state to down"
            print(f"\n[Sending Test Payload]: {test_syslog}")
            await websocket.send(test_syslog)
            
            # استقبال الرد (الصدى من السيرفر أو الأحداث القادمة)
            async for message in websocket:
                print(f"\n[Raw Event Received]: {message}")
                
                # تحليل الحدث عبر الـ Parser الذي بنيناه سابقاً
                network_event = parse_cisco_syslog(message)
                print(f"[Parsed Event Dict]: {network_event.to_dict()}")
                break # للخروج بعد اختبار أول رسالة
                
    except Exception as e:
        print(f"Connection error: {e}")

if __name__ == "__main__":
    asyncio.run(test_connection())