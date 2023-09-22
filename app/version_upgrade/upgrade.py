import os
import logging
import time

from app.version_upgrade.verify_checksum import verify_checksum
from app.version_upgrade.stop_service import stop_services
from app.version_upgrade.start_service import start_services
from app.version_upgrade.backup_executable import backup_executable
from app.version_upgrade.deploy_executable import deploy_executables
from app.version_upgrade.monitor_service import monitor_services


# 解压路径

BASE_UNPACK_DIR = "/usr/local/zops-server/upgrade/unpack"  # BASE_


def main(installation_package_path, program, backup_dir):
    INSTALLATION_PACKAGE_PATH = installation_package_path
    PROGRAM = program
    BACKUP_DIR = backup_dir

    # 1. 校验MD5
    logging.info("开始校验MD5...", extra={"code": "200"})
    SOURCE_DIR = verify_checksum(INSTALLATION_PACKAGE_PATH, BASE_UNPACK_DIR)
    if SOURCE_DIR:
        logging.info(f"验证成功! SERVER_DIR: {SOURCE_DIR}", extra={"code": "200"})
    else:
        logging.error("MD5校验失败，退出...", extra={"code": "500"})
        return False

    # 2. 停止相关服务
    if not stop_services():
        logging.error("停止服务失败，退出...", extra={"code": "500"})
        return False
    else:
        logging.info("成功停止服务!", extra={"code": "200"})

    # 3. 备份旧版本可执行程序
    if not backup_executable(PROGRAM, BACKUP_DIR):
        logging.error(" 备份可执行程序失败，退出...", extra={"code": "500"})
        return False
    else:
        logging.info("成功备份可执行程序!", extra={"code": "200"})

    # 4. 部署新版本可执行程序
    if not deploy_executables(SOURCE_DIR, PROGRAM):
        logging.error("部署可执行程序失败，退出...", extra={"code": "500"})
        return False
    else:
        logging.info("成功部署可执行程序!", extra={"code": "200"})

    # 5. 执行数据库升级脚本
    # execute_db_script()

    # 6. 启动新版本服务
    if not start_services():
        logging.error("启动服务失败，退出...", extra={"code": "500"})
        return False
    else:
        logging.info("成功启动服务!", extra={"code": "200"})

    # 7. 监控服务状态
    if not monitor_services():
        logging.error("服务监控失败，退出...", extra={"code": "500"})
        return False
    else:
        logging.info("服务监控成功!", extra={"code": "200"})
    return True


def upgrade(installation_package_path, program, backup_dir):
    logging.info("启动升级...", extra={"code": "200"})

    if not os.path.exists(installation_package_path):
        logging.error(f"提供的安装包路径不存在: {installation_package_path}", extra={"code": "500"})
        logging.error("升级失败!", extra={"code": "500"})
        exit(-1)

    if not os.path.exists(program):
        logging.error(f"提供的旧程序路径不存在: {program}", extra={"code": "500"})
        logging.error("升级失败!", extra={"code": "500"})
        exit(-1)

    if not os.path.exists(backup_dir):
        logging.error(f"提供的备份目录不存在: {backup_dir}", extra={"code": "500"})
        logging.error("升级失败!", extra={"code": "500"})
        exit(-1)

    logging.info("检查路径成功!", extra={"code": "200"})

    # 对路径规范化
    installation_package_path = os.path.abspath(installation_package_path)
    program = os.path.abspath(program)
    backup_dir = os.path.abspath(backup_dir)
    installation_package_path = os.path.normpath(installation_package_path)
    program = os.path.normpath(program)
    backup_dir = os.path.normpath(backup_dir)

    # # 启动升级
    if not main(installation_package_path, program, backup_dir):
        logging.error("升级失败!", extra={"code": "500"})
    else:
        logging.info("成功完成升级!", extra={"code": "201"})

    exit(0)
