import os
import shutil
import stat
import tarfile
import logging

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
