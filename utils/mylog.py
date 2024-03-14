import logging
import os
import time

# 设置日志配置


def log_init(script_name=""):
    """
   初始化日志配置。
   :param script_name: 脚本或模块的名称。
   """
    # 根据 script_name 设置日志文件名
    log_file = os.path.join('logs', f'{script_name}.log')

    if script_name == 'modify_profile':
        log_format = '%(asctime)s - %(levelname)s - %(message)s'
    elif script_name == 'upgrade':
        log_upgrade_init(log_file)
    else:
        log_format = '%(asctime)s - %(levelname)s - %(message)s'

    formatter = logging.Formatter(log_format)
    # 创建文件处理程序并设置级别为 debug
    file_handler = logging.FileHandler(log_file)
    file_handler.setLevel(logging.DEBUG)
    file_handler.setFormatter(formatter)
    # 创建控制台处理程序并设置级别为 error
    console_handler = logging.StreamHandler()
    console_handler.setLevel(logging.DEBUG)
    console_handler.setFormatter(formatter)

    # 添加处理程序到 logger
    logger = logging.getLogger()
    logger.addHandler(file_handler)
    # logger.addHandler(console_handler)
    logger.setLevel(logging.DEBUG)
    print("log_init")


def log_upgrade_init(log_file):
    class CustomFormatter(logging.Formatter):
        def formatTime(self, record, datefmt=None):
            return str(int(time.time()))

    # 清空日志文件内容
    if os.path.exists(log_file):
        with open(log_file, 'w'):

    # 设置日志文件权限为777
    os.chmod(log_file, 0o777)

    logging.basicConfig(filename=log_file, level=logging.INFO, format='%(asctime)s#%(code)s#%(message)s##',
                        datefmt='%s')
    logger = logging.getLogger()

    # 设置StreamHandler以禁用缓存
    handler = logging.StreamHandler(open(log_file, 'a', 1))  # 1 表示无缓冲
    handler.setFormatter(CustomFormatter('%(asctime)s#%(code)s#%(message)s##'))
    logger.addHandler(handler)

    # 删除默认的FileHandler
    logger.removeHandler(logger.handlers[0])


