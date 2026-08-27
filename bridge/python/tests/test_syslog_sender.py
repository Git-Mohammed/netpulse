import socket

def send_test_syslog():
    # عنوان وسوكت الـ Bridge المحلي
    host = "127.0.0.1"
    port = 514 # أو 9000 حسب ما ضبطته في الـ .env
    
    # محاكاة رسالة Syslog حقيقية تصدرها أجهزة سيسكو عند سقوط منفذ
    # Format: <Facility*8 + Severity>timestamp hostname %PROTOCOL-CODE: message
    sample_syslog = b"<189>Oct 24 10:00:00 Core-R2 %LINEPROTO-5-UPDOWN: Line protocol on Interface GigabitEthernet0/0, changed state to down"
    
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    try:
        sock.sendto(sample_syslog, (host, port))
        print(f"[Test Sender] Test syslog packet sent successfully to {host}:{port}")
    finally:
        sock.close()

if __name__ == "__main__":
    send_test_syslog()