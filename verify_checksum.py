import os
import hashlib
import logging

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

def verify_checksum(INSTALLATION_PACKAGE_PATH):
    """验证MD5值并返回SERVER_DIR的值"""
    INSTALLATION_PACKAGE = os.path.join(INSTALLATION_PACKAGE_PATH, "zops-upgrade")
    UPGRADE_TAR_PATH = os.path.join(INSTALLATION_PACKAGE_PATH, 'zops-upgrade.tar.gz')

    # 检查 zops-upgrade.tar.gz 是否存在
    if not os.path.exists(UPGRADE_TAR_PATH):
        logging.error(f"升级包 {UPGRADE_TAR_PATH} 不存在!", extra={"code": "500"})
        return None

    # 解压 zops-upgrade.tar.gz 到 INSTALLATION_PACKAGE_PATH 目录
    os.system(f"tar -xzvf {UPGRADE_TAR_PATH} -C {INSTALLATION_PACKAGE_PATH}")

    # 读取release.txt文件内容
    with open(os.path.join(INSTALLATION_PACKAGE, "release.txt"), "r") as f:
        lines = f.readlines()
        for line in lines:
            if "name:zops-server" in line:
                parts = line.strip().split("\t")
                name_version = parts[0].split(":")[1] + "_" + parts[1].split(":")[1]
                name_version_tar = name_version + ".tar.gz"
                expected_md5 = parts[2].split(":")[1]

                # 计算文件的MD5值
                actual_md5 = calculate_md5(os.path.join(INSTALLATION_PACKAGE, name_version_tar))

                # 比较MD5值
                if actual_md5 != expected_md5:
                    logging.error(f"{name_version_tar} MD5值校验失败!", extra={"code": "500"})
                    return None

    # 解压 zops-server_version.tar.gz 到 INSTALLATION_PACKAGE 目录
    os.system(f"tar -xzvf {os.path.join(INSTALLATION_PACKAGE, name_version_tar)} -C {INSTALLATION_PACKAGE}")

    # 验证必要的文件和目录是否存在
    SERVER_DIR = os.path.join(INSTALLATION_PACKAGE, name_version)
    if not verify_files_and_dirs(SERVER_DIR):
        return None
    return SERVER_DIR

