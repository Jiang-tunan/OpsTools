import argparse
import shutil
import stat
import subprocess

import os
import hashlib
import logging
import tarfile
import time

# 解压路径
BASE_UNPACK_DIR="/usr/local/zops-server/upgrade/unpack" #BASE_

"""mylog.py"""
class CustomFormatter(logging.Formatter):
    def formatTime(self, record, datefmt=None):
        return str(int(time.time()))


def log_init():
    # 获取Upgrade.py的绝对路径
    script_dir = os.path.dirname(os.path.abspath(__file__))

    log_file_path = os.path.join(script_dir, 'upgrade.log')

    # 清空日志文件内容
    with open(log_file_path, 'w'):
        pass

    # 设置日志文件权限为777
    os.chmod(log_file_path, 0o777)

    logging.basicConfig(filename=log_file_path, level=logging.INFO, format='%(asctime)s#%(code)s#%(message)s##', datefmt='%s')
    logger = logging.getLogger()

    # 设置StreamHandler以禁用缓存
    handler = logging.StreamHandler(open(log_file_path, 'a', 1))  # 1 表示无缓冲
    handler.setFormatter(CustomFormatter('%(asctime)s#%(code)s#%(message)s##'))
    logger.addHandler(handler)

    # 删除默认的FileHandler
    logger.removeHandler(logger.handlers[0])
"""mylog.py ********** end * """

""""verify_checksum.py"""
# 需检测文件及文件夹
dirs = ["h5", "h5php", "sbin"]
executables = ["zops_agentd", "zops_java", "zops_proxy", "zops_server"]

def calculate_md5(file_path):
    """计算文件的MD5值"""
    with open(file_path, 'rb') as f:
        return hashlib.md5(f.read()).hexdigest()

def verify_files_and_dirs(base_dir):
    """验证必要的文件和目录是否存在"""
    for d in dirs:
        if not os.path.exists(os.path.join(base_dir, d)):
            logging.error(f"目录 {d} 不存在!", extra={"code": "500"})
            return False
        else:
            logging.info(f"目录 {d} 存在!", extra={"code": "200"})

    for exe in executables:
        if not os.path.exists(os.path.join(base_dir, "sbin", exe)):
            logging.error(f"可执行文件 {exe} 不存在!", extra={"code": "500"})
            return False
        else:
            logging.info(f"可执行文件 {exe} 存在!", extra={"code": "200"})

    return True

def verify_checksum(INSTALLATION_PACKAGE_PATH, BASE_UNPACK_DIR):
    """验证MD5值并返回SERVER_DIR的值"""
    # 获取文件名并去除.tar.gz后缀
    PACKAGE_NAME = os.path.basename(INSTALLATION_PACKAGE_PATH).replace('.tar.gz', '')
    UNPACK_DIR = os.path.join(BASE_UNPACK_DIR, PACKAGE_NAME)

    # 确保解压目标目录存在
    if not os.path.exists(UNPACK_DIR):
        os.makedirs(UNPACK_DIR)

    # 解压 安装包 到 UNPACK_DIR 目录
    os.system(f"tar -xzvf {INSTALLATION_PACKAGE_PATH} -C {UNPACK_DIR}")

    # 检查是否存在嵌套的目录结构
    nested_dir = os.path.join(UNPACK_DIR, PACKAGE_NAME)
    if os.path.exists(nested_dir):
        for item in os.listdir(nested_dir):
            os.rename(os.path.join(nested_dir, item), os.path.join(UNPACK_DIR, item))
        os.rmdir(nested_dir)

    # 读取release.txt文件内容
    with open(os.path.join(UNPACK_DIR, "release.txt"), "r") as f:
        lines = f.readlines()
        for line in lines:
            if "name:zops-server" in line:
                parts = line.strip().split("\t")
                name_version = parts[0].split(":")[1] + "_" + parts[1].split(":")[1]
                name_version_tar = name_version + ".tar.gz"
                expected_md5 = parts[2].split(":")[1]

                # 计算文件的MD5值
                actual_md5 = calculate_md5(os.path.join(UNPACK_DIR, name_version_tar))

                # 比较MD5值
                if actual_md5 != expected_md5:
                    logging.error(f"{name_version_tar} MD5值校验失败!", extra={"code": "500"})
                    return None

    # 解压 zops-server_version.tar.gz 到 UNPACK_DIR 目录
    os.system(f"tar -xzvf {os.path.join(UNPACK_DIR, name_version_tar)} -C {UNPACK_DIR}")

    # 验证必要的文件和目录是否存在
    SERVER_DIR = os.path.join(UNPACK_DIR, name_version)
    if not verify_files_and_dirs(SERVER_DIR):
        return None
    return SERVER_DIR
"""verify_checksum.py*****end """


""" stop_service.py"""
def stop_systemctl_service(service_name):
    """使用systemctl命令停止服务"""
    logging.info(f"尝试停止服务 {service_name}...", extra={"code": "100"})  # 添加日志
    try:
        # 使用Popen执行命令并捕获输出和错误信息
        process = subprocess.Popen(['systemctl', 'stop', service_name], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        output, error = process.communicate()
        if process.returncode == 0:
            logging.info(f"服务 {service_name} 成功停止! 输出: {output.decode('utf-8')}", extra={"code": "200"})
            return True
        else:
            logging.error(f"停止服务 {service_name} 失败。输出: {output.decode('utf-8')} 错误: {error.decode('utf-8')}", extra={"code": "500"})
            return False
    except Exception as e:
        logging.error(f"停止服务 {service_name} 时发生异常: {str(e)}", extra={"code": "500"})
        return False
def stop_zops_service(service_name):
    """停止zops相关的服务"""
    logging.info(f"尝试停止服务 {service_name}...", extra={"code": "200"})  # 添加日志
    try:
        process = subprocess.Popen([service_name, 'stop'], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        output, error = process.communicate()
        if process.returncode == 0:
            logging.info(f"服务 {service_name} 成功停止! 输出: {output}", extra={"code": "200"})
            return True
        else:
            logging.error(f"停止服务 {service_name} 失败。输出: {output} 错误: {error}", extra={"code": "500"})
            return False
    except Exception as e:
        logging.error(f"停止服务 {service_name} 时发生异常: {str(e)}", extra={"code": "500"})
        return False


def stop_services():
    # 停止nginx服务
    # if not stop_systemctl_service('nginx.service'):
    #     logging.error("停止 nginx 服务失败。", extra={"code": "500"})
    #     return False

    # 停止zops_server和zops_agentd服务
    if not stop_zops_service('zops_server'):
        logging.error("停止 zops_server 服务失败。", extra={"code": "500"})
        return False

    if not stop_zops_service('zops_agentd'):
        logging.error("停止 zops_agentd 服务失败。", extra={"code": "500"})
        return False

    return True
"""stop_service.py******* end * """

"""backup_executable.py"""
def ignore_files_and_folders(folder, filenames):
    ignored = [name for name in filenames if os.path.exists(os.path.join(folder, name)) and stat.S_ISSOCK(os.stat(os.path.join(folder, name)).st_mode)]
    if 'upgrade' in filenames:
        ignored.append('upgrade')
    return ignored

def backup_directory(source_path, backup_path):
    """备份指定目录到备份路径"""

    source_path = os.path.normpath(source_path)
    # 检查备份目录是否存在，如果不存在则创建
    if not os.path.exists(backup_path):
        try:
            os.makedirs(backup_path)
            logging.info(f"成功创建备份目录: {backup_path}", extra={"code": "200"})
        except OSError as e:
            logging.error(f"创建备份目录失败: {e}", extra={"code": "500"})
            return False

    # 在备份目录下创建一个与源目录同名的子目录
    backup_subdir = os.path.join(backup_path, os.path.basename(source_path))

    # 如果备份目录下已经存在一个与源目录同名的子目录或文件，先删除它
    if os.path.exists(backup_subdir):
        try:
            shutil.rmtree(backup_subdir)
            logging.info(f"成功删除已存在的备份子目录: {backup_subdir}", extra={"code": "200"})
        except OSError as e:
            logging.error(f"删除已存在的备份子目录失败: {e}", extra={"code": "500"})
            return False

    # 然后复制源目录的内容到这个子目录中，忽略UNIX套接字文件和upgrade文件夹
    try:
        shutil.copytree(source_path, backup_subdir, ignore=ignore_files_and_folders)
        logging.info(f"成功复制源目录到备份子目录: {backup_subdir}", extra={"code": "200"})
    except OSError as e:
        logging.error(f"复制源目录到备份子目录失败: {e}", extra={"code": "500"})
        return False

    # 创建一个压缩文件，该文件包含源目录的所有内容
    try:
        with tarfile.open(os.path.join(backup_path, os.path.basename(source_path) + ".tar.gz"), "w:gz") as tar:
            tar.add(source_path, arcname=os.path.basename(source_path))
        logging.info(f"成功创建压缩文件: {os.path.join(backup_path, os.path.basename(source_path) + '.tar.gz')}", extra={"code": "200"})
    except Exception as e:
        logging.error(f"创建压缩文件失败: {e}", extra={"code": "500"})
        return False

    return True

def backup_executable(PROGRAM, BACKUP_DIR):
    if not backup_directory(PROGRAM, BACKUP_DIR):
        logging.error(f"{PROGRAM} 备份失败", extra={"code": "500"})
        return False
    logging.info(f"{PROGRAM} 备份完成，备份到 {BACKUP_DIR}", extra={"code": "200"})
    return True
"""backup_executable.py****** end * """

"""deploy_executable.py"""

dirs = ["h5", "h5php", "sbin"]
executables = ["zops_agentd", "zops_proxy", "zops_server"]

# 忽略项
ignore_subpaths = ["h5php/conf", "h5/net.config.js"]

def should_ignore(path):
    """检查路径是否应该被忽略"""
    for subpath in ignore_subpaths:
        if subpath in path:
            logging.info(f"忽略 {path}", extra={"code": "200"})
            return True
    return False

def delete_file_or_directory(path):
    """删除文件或目录"""
    if os.path.exists(path) and not should_ignore(path):
        try:
            if os.path.isfile(path):
                os.remove(path)
                logging.info(f"成功删除 {path}", extra={"code": "200"})
            else:
                for item in os.listdir(path):
                    delete_file_or_directory(os.path.join(path, item))
                if not os.listdir(path):  # 如果目录为空，则删除它
                    os.rmdir(path)
                    logging.info(f"成功删除 {path}", extra={"code": "200"})
        except OSError as e:
            logging.error(f"删除 {path} 失败: {e}", extra={"code": "500"})
            return False
    return True

def copy(src, dst):
    """复制文件或目录"""
    if os.path.exists(src) and not should_ignore(src):
        try:
            if os.path.isfile(src):
                shutil.copy2(src, dst)
                logging.info(f"成功复制 {src} 到 {dst}", extra={"code": "200"})
            else:
                if not os.path.exists(dst):
                    os.makedirs(dst)
                for item in os.listdir(src):
                    copy(os.path.join(src, item), os.path.join(dst, item))
                logging.info(f"成功复制 {src} 到 {dst}", extra={"code": "200"})
        except OSError as e:
            logging.error(f"复制 {src} 到 {dst} 失败: {e}", extra={"code": "500"})
            return False
    return True

def deploy_executables(SOURCE_DIR, PROGRAM):
    """从 /package/zops-upgrade/zops-server_version 中复制指定的文件夹和文件到output"""
    # 删除output目录下的指定文件和文件夹
    for d in dirs:
        if not delete_file_or_directory(os.path.join(PROGRAM, d)):
            return False

    for e in executables:
        if not delete_file_or_directory(os.path.join(PROGRAM, "sbin", e)):
            return False

    # 从SOURCE_DIR复制到OUTPUT_DIR
    for d in dirs:
        src_dir = os.path.join(SOURCE_DIR, d)
        dst_dir = os.path.join(PROGRAM, d)
        if not copy(src_dir, dst_dir):
            return False

    for e in executables:
        src_exe = os.path.join(SOURCE_DIR, "sbin", e)
        dst_exe = os.path.join(PROGRAM, "sbin", e)
        if not copy(src_exe, dst_exe):
            return False

    logging.info("部署完成!", extra={"code": "200"})
    return True
"""deploy_executable.py********* end * """

"""start_service.py"""
def start_services():
    # 启动nginx服务
    # try:
    #     subprocess.run(["systemctl", "start", "nginx.service"], check=True)
    #     logging.info("Nginx 服务成功启动。", extra={"code": "200"})
    # except subprocess.CalledProcessError:
    #     logging.error("启动 Nginx 服务出错。", extra={"code": "500"})
    #     return False

    # 启动zops_server
    try:
        subprocess.run(["zops_server", "start"], check=True)
        logging.info("zops_server 服务成功启动...", extra={"code": "200"})
    except subprocess.CalledProcessError:
        logging.error("启动 zops_server 服务出错...", extra={"code": "500"})
        return False

    # 启动zops_agentd
    try:
        subprocess.run(["zops_agentd", "start"], check=True)
        logging.info("zops_agentd 服务成功启动。", extra={"code": "200"})
    except subprocess.CalledProcessError:
        logging.error("启动 zops_agentd 服务出错。", extra={"code": "500"})
        return False

    return True
"""start_services.py********* end * """

"""monitor_service.py"""
def monitor_services():
    services = ["zops_server"]

    for service in services:
        try:
            if "nginx.service" == service:
                process = subprocess.run(["systemctl", "is-active", service], check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            else:
                process = subprocess.run([service, "status"], check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

            # 将信息存入 output
            output = process.stdout.decode('utf-8')

            if " running" in output or "active" in output:
                logging.info(f"{service} 已运行..", extra={"code": "200"})
            else:
                logging.error(f"{service} 没有运行...", extra={"code": "500"})
                return False

        except subprocess.CalledProcessError as e:
            logging.error(f"检查 {service} 的状态时出错: {e}", extra={"code": "500"})
            return False

        except Exception as e:
            logging.error(f"处理 {service} 时出现未知错误: {e}", extra={"code": "500"})
            return False

    return True
"""monitor_service.py********* end * """
def main(args):
    # 使用命令行参数
    INSTALLATION_PACKAGE_PATH = args.installation_package_path
    PROGRAM = args.program
    BACKUP_DIR = args.backup_dir

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

    # 对路径规范化
    args.installation_package_path = os.path.abspath(args.installation_package_path)
    args.program = os.path.abspath(args.program)
    args.backup_dir = os.path.abspath(args.backup_dir)
    args.installation_package_path = os.path.normpath(args.installation_package_path)
    args.program = os.path.normpath(args.program)
    args.backup_dir = os.path.normpath(args.backup_dir)

    # # 启动升级
    if not main(args):
        logging.error("升级失败!", extra={"code": "500"})
    else:
        logging.info("成功完成升级!", extra={"code": "201"})

    # 获取当前脚本的目录 构建clean.sh的绝对路径
    upgrade_dir = os.path.dirname(os.path.abspath(__file__))
    clean_path = os.path.join(upgrade_dir, "clean.sh")

    # # 清理缓存
    # subprocess.run([clean_path, f"{BASE_UNPACK_DIR}", args.backup_dir], stdout=subprocess.PIPE, stderr=subprocess.PIPE, universal_newlines=True)
    # if clean_msg.stdout:
    #     logging.info(clean_msg.stdout.strip(), extra={"code": "200"})
    # if clean_msg.stderr:
    #     logging.error(clean_msg.stderr.strip(), extra={"code": "500"})
    exit(0)