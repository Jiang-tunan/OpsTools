import logging
import os
import time


# 设置日志配置
class CustomFormatter(logging.Formatter):
    def formatTime(self, record, datefmt=None):
        return str(int(time.time()))

def log_init():

    log_file = 'upgrade.log'
    # 清空日志文件内容
    # with open(log_file, 'w'):
    #     pass

    if os.path.exists(log_file):
        os.remove(log_file)


    logging.basicConfig(filename='upgrade.log', level=logging.INFO, format='%(asctime)s#%(code)s#%(message)s##', datefmt='%s')
    logger = logging.getLogger()
    logger.handlers[0].setFormatter(CustomFormatter('%(asctime)s#%(code)s#%(message)s##'))
