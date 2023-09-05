import logging
import time


# 设置日志配置
class CustomFormatter(logging.Formatter):
    def formatTime(self, record, datefmt=None):
        return str(int(time.time()))

def log_init():
    logging.basicConfig(filename='upgrade.log', level=logging.INFO, format='%(asctime)s#%(code)s#%(message)s##', datefmt='%s')
    logger = logging.getLogger()
    logger.handlers[0].setFormatter(CustomFormatter('%(asctime)s#%(code)s#%(message)s##'))
