import logging
import sys
from app.config import settings

def setup_logging():
    """إعداد نظام التسجيل (Logging) المركزي للـ Python Bridge"""
    log_level = getattr(logging, settings.APP_LOG_LEVEL.upper(), logging.INFO)
    
    logging.basicConfig(
        level=log_level,
        format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
        handlers=[
            logging.StreamHandler(sys.stdout)
        ]
    )
    
    logger = logging.getLogger("NetPulse.Bridge")
    logger.info(f"Logging initialized with level: {settings.APP_LOG_LEVEL}")