import shutil
import os

# 定义全局变量
import os
import shutil
import tarfile

# 定义全局变量
SOURCE_DIR = "output" # 源目录
BACKUP_DIR = "backup" #  备份目录

def backup_directory(source_path, backup_path):
    """
    备份指定目录到备份路径
    """
    # 检查备份目录是否存在，如果不存在则创建
    if not os.path.exists(backup_path):
        os.makedirs(backup_path)

    # 在备份目录下创建一个与源目录同名的子目录
    backup_subdir = os.path.join(backup_path, os.path.basename(source_path))

    # 如果备份目录下已经存在一个与源目录同名的子目录或文件，先删除它
    if os.path.exists(backup_subdir):
        shutil.rmtree(backup_subdir)

    # 然后复制源目录的内容到这个子目录中
    shutil.copytree(source_path, backup_subdir)

    # 创建一个压缩文件，该文件包含源目录的所有内容
    with tarfile.open(os.path.join(backup_path, os.path.basename(source_path) + ".tar.gz"), "w:gz") as tar:
        tar.add(source_path, arcname=os.path.basename(source_path))

# 调用函数进行备份
backup_directory(SOURCE_DIR, BACKUP_DIR)


def backup_executable():
    backup_directory(SOURCE_DIR, BACKUP_DIR)
    print(f"Backup of {SOURCE_DIR} completed to {BACKUP_DIR}")

