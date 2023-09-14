import logging
import os
import time

# 设置日志配置


def log_init():
    # 获取Upgrade.py的绝对路径
    # script_dir = os.path.dirname(os.path.abspath(__file__))
    #
    # log_file_path = os.path.join(script_dir, 'upgrade.log')
    #
    # # 删除日志文件并重新创建
    # if os.path.exists(log_file_path):
    #     os.remove(log_file_path)
    #
    # logging.basicConfig(filename=log_file_path, level=logging.INFO, format='%(asctime)s#%(code)s#%(message)s##', datefmt='%s')
    # logger = logging.getLogger()
    #
    # # 设置StreamHandler以禁用缓存
    # handler = logging.StreamHandler(open(log_file_path, 'a', 1))  # 1 表示无缓冲
    # handler.setFormatter(CustomFormatter('%(asctime)s#%(code)s#%(message)s##'))
    # logger.addHandler(handler)
    #
    # # 删除默认的FileHandler
    # logger.removeHandler(logger.handlers[0])
    print("log_init")