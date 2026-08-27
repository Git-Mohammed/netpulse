import asyncio
import logging
from app.config import settings
from app.logging_config import setup_logging
from app.collectors.syslog_listener import start_syslog_server

setup_logging()
logger = logging.getLogger("NetPulse.Bridge.Main")

async def main():
    logger.info(f"Starting {settings.APP_NAME} Python Bridge in {settings.APP_ENV} mode...")
    
    # تشغيل مستقبل الـ Syslog كـ Background Task
    await start_syslog_server()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        logger.info("Python Bridge stopped by user.")