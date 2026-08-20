import logging

from app.config import APP_NAME, APP_ENV
from app.logging_config import configure_logging


def main():

    configure_logging()

    logger = logging.getLogger("netpulse.bridge")

    logger.info("%s Python Bridge started.", APP_NAME)
    logger.info("Environment: %s", APP_ENV)


if __name__ == "__main__":
    main()