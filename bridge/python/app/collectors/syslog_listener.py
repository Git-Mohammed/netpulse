import asyncio
import logging
from websockets.server import serve
from app.config import settings
from app.parsers.syslog_parser import parse_cisco_syslog

logger = logging.getLogger("NetPulse.Bridge")

class SyslogServerProtocol(asyncio.DatagramProtocol):
    """مستقبل حزم الـ UDP Syslog التقليدية"""
    def connection_made(self, transport):
        self.transport = transport
        logger.info(f"Syslog UDP Listener active on {settings.BRIDGE_HOST}:9000")

    def datagram_received(self, data, addr):
        raw_message = data.decode(errors="ignore").strip()
        logger.info(f"Received raw UDP syslog from {addr}: {raw_message}")
        
        # تحليل وتطبيع الحدث فوراً
        network_event = parse_cisco_syslog(raw_message)
        logger.info(f"[UDP Normalized] Type: {network_event.event_type} | Device: {network_event.device_name} | Severity: {network_event.severity}")


async def handle_packet_tracer_websocket(websocket):
    """خادم WebSocket لاستقبال اتصالات RealWSClient القادمة من Packet Tracer"""
    logger.info("Packet Tracer agent connected via WebSocket!")
    
    try:
        async for message in websocket:
            raw_message = message.strip()
            logger.info(f"Received WS message from Packet Tracer: {raw_message}")
            
            # تحليل وتطبيع الحدث فوراً
            network_event = parse_cisco_syslog(raw_message)
            logger.info(f"[WS Normalized] Type: {network_event.event_type} | Device: {network_event.device_name} | Severity: {network_event.severity}")
            
            # إرسال تأكيد استلام عودة لـ Packet Tracer
            await websocket.send("ACK: Event processed successfully by Bridge")
            
    except websockets.exceptions.ConnectionClosed:
        logger.info("Packet Tracer agent disconnected.")
    except Exception as e:
        logger.error(f"WebSocket error: {e}")


async def start_syslog_server():
    loop = asyncio.get_running_loop()
    
    # 1. تشغيل مستقبل الـ UDP على البورت 9000
    udp_transport, udp_protocol = await loop.create_datagram_endpoint(
        lambda: SyslogServerProtocol(),
        local_addr=(settings.BRIDGE_HOST, 9000)
    )
    
    # 2. تشغيل خادم الـ WebSocket على البورت 1234 لاستقبال Packet Tracer
    ws_port = 1234
    ws_server = await serve(handle_packet_tracer_websocket, settings.BRIDGE_HOST, ws_port)
    logger.info(f"WebSocket Bridge Server started on ws://{settings.BRIDGE_HOST}:{ws_port}")

    try:
        # إبقاء الخدمات تعمل باستمرار
        await asyncio.Future()
    finally:
        udp_transport.close()
        ws_server.close()
        await ws_server.wait_closed()