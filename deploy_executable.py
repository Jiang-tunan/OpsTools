import os
import shutil
import logging

dirs = ["h5", "h5php", "sbin"]
executables = ["zops_agentd", "zops_proxy", "zops_server"]
ignore_subpaths = ["h5php/conf"]

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
