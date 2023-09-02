import os
import hashlib

# 全局路径变量
BASE_DIR = "test"
ZOPS_DIR = os.path.join(BASE_DIR, "zops_1.0.0")
SERVER_DIR = os.path.join(ZOPS_DIR, "zops-server_1.0.0")

# 需检测文件及文件夹
dirs = ["h5", "h5php", "sbin", "data"]
executables = ["zops_agentd", "zops_java", "zops_proxy", "zops_server"]

def calculate_md5(file_path):
    """计算文件的MD5值"""
    with open(file_path, 'rb') as f:
        return hashlib.md5(f.read()).hexdigest()

def verify_files_and_dirs(base_dir):
    """验证必要的文件和目录是否存在"""
    for d in dirs:
        if not os.path.exists(os.path.join(base_dir, d)):
            print(f"目录 {d} 不存在!")
            return False
        else:
            print(f"目录 {d} 存在!")

    for exe in executables:
        if not os.path.exists(os.path.join(base_dir, "sbin", exe)):
            print(f"可执行文件 {exe} 不存在!")
            return False
        else:
            print(f"可执行文件 {exe} 存在!")

    return True

def verify_checksum():
    """验证MD5值"""
    # 解压zops_1.0.0.tar.gz到BASE_DIR目录
    os.system(f"tar -xzvf {os.path.join(BASE_DIR, 'zops_1.0.0.tar.gz')} -C {BASE_DIR}")

    # 读取release.txt文件内容
    with open(os.path.join(ZOPS_DIR, "release.txt"), "r") as f:
        lines = f.readlines()
        for line in lines:
            if "name:zops-server" in line:
                parts = line.strip().split("\t")
                name_version = parts[0].split(":")[1] + "_" + parts[1].split(":")[1] + ".tar.gz"
                expected_md5 = parts[2].split(":")[1]

                # 计算文件的MD5值
                actual_md5 = calculate_md5(os.path.join(ZOPS_DIR, name_version))

                # 比较MD5值
                if actual_md5 != expected_md5:
                    print(f"{name_version} MD5值校验失败!")
                    return False

    # 解压zops-server_1.0.0.tar.gz到ZOPS_DIR目录
    os.system(f"tar -xzvf {os.path.join(ZOPS_DIR, name_version)} -C {ZOPS_DIR}")

    # 验证必要的文件和目录是否存在
    if not verify_files_and_dirs(SERVER_DIR):
        return False

    return True

