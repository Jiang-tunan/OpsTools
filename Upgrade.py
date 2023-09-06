import argparse
import os
import logging
import subprocess

from verify_checksum import verify_checksum
from stop_service import stop_services
from backup_executable import backup_executable
from deploy_executable import deploy_executables
from start_service import start_services
from monitor_service import monitor_services
from log import log_init

def main(args):
    # 使用命令行参数
    INSTALLATION_PACKAGE_PATH = args.installation_package_path
    PROGRAM = args.program
    BACKUP_DIR = args.backup_dir

    # 1. 校验MD5
    SOURCE_DIR = verify_checksum(INSTALLATION_PACKAGE_PATH)
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
    if not backup_executable(SOURCE_DIR, BACKUP_DIR):
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

if __name__ == "__main__":

    log_init()
    logging.info("启动升级...", extra={"code": "200"})

    parser = argparse.ArgumentParser(description="zops升级脚本")
    parser.add_argument("installation_package_path", type=str, help="安装包路径")
    parser.add_argument("program", type=str, help="旧程序路径")
    parser.add_argument("backup_dir", type=str, help="备份目录")
    args = parser.parse_args()
    # 检查路径是否存在
    if not os.path.exists(args.installation_package_path):
        logging.error(f"提供的安装包路径不存在: {args.installation_package_path}", extra={"code": "500"})
        logging.error("升级失败!", extra={"code": "500"})
        exit(-1)

    if not os.path.exists(args.program):
        logging.error(f"提供的旧程序路径不存在: {args.program}", extra={"code": "500"})
        logging.error("升级失败!", extra={"code": "500"})
        exit(-1)

    if not os.path.exists(args.backup_dir):
        logging.error(f"提供的备份目录不存在: {args.backup_dir}", extra={"code": "500"})
        logging.error("升级失败!", extra={"code": "500"})
        exit(-1)

    logging.info("检查路径成功!", extra={"code": "200"})

    if not main(args):
        logging.error("升级失败!", extra={"code": "500"})
    else:
        logging.info("成功完成升级!", extra={"code": "201"})

    # # 获取当前脚本的目录 构建clean.sh的绝对路径
    # upgrade_dir = os.path.dirname(os.path.abspath(__file__))
    # clean_path = os.path.join(upgrade_dir, "clean.sh")
    #
    # # 清理缓存
    # clean_msg = subprocess.run([clean_path, f"{args.installation_package_path}/zops-upgrade", args.backup_dir], stdout=subprocess.PIPE, stderr=subprocess.PIPE, universal_newlines=True)
    # if clean_msg.stdout:
    #     logging.info(clean_msg.stdout.strip(), extra={"code": "200"})
    # if clean_msg.stderr:
    #     logging.error(clean_msg.stderr.strip(), extra={"code": "500"})
    exit(0)