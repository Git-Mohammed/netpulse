import os
from dotenv import load_dotenv

# تحميل المتغيرات من ملف .env
load_dotenv()

class Settings:
    APP_NAME: str = os.getenv("APP_NAME", "NetPulse")
    APP_ENV: str = os.getenv("APP_ENV", "development")
    APP_LOG_LEVEL: str = os.getenv("APP_LOG_LEVEL", "INFO")
    
    BRIDGE_HOST: str = os.getenv("BRIDGE_HOST", "127.0.0.1")
    BRIDGE_PORT: int = int(os.getenv("BRIDGE_PORT", "9000"))

# إنشاء الكائن الذي يتم استيراده في باقي الملفات
settings = Settings()