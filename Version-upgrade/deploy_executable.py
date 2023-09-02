import os
import shutil

SOURCE_DIR = "test/zops_1.0.0/zops-server_1.0.0"
OUTPUT_DIR = "output"
dirs = ["h5", "h5php", "sbin"]
executables = ["zops_agentd", "zops_java", "zops_proxy", "zops_server"]

def delete_file_or_directory(path):
    """删除文件或目录"""
    if os.path.exists(path):
        if os.path.isfile(path):
            os.remove(path)
        else:
            shutil.rmtree(path)

def copy_directory(src, dst):
    """复制目录"""
    if os.path.exists(src):
        shutil.copytree(src, dst)

def copy_file_or_directory(src, dst):
    """复制文件或目录"""
    if os.path.exists(src):
        if os.path.isfile(src):
            shutil.copy2(src, dst)
        else:
            copy_directory(src, dst)

def deploy_executables():
    """从test/zops_1.0.0/zops-server_1.0.0中复制指定的文件夹和文件到output"""

    # 删除output目录下的指定文件和文件夹
    for d in dirs:
        delete_file_or_directory(os.path.join(OUTPUT_DIR, d))
    for e in executables:
        delete_file_or_directory(os.path.join(OUTPUT_DIR, "sbin", e))

    # # 从SOURCE_DIR复制到OUTPUT_DIR
    for d in dirs:
        src_dir = os.path.join(SOURCE_DIR, d)
        dst_dir = os.path.join(OUTPUT_DIR, d)
        copy_file_or_directory(src_dir, dst_dir)
    #
    # for e in executables:
    #     src_exe = os.path.join(SOURCE_DIR, "sbin", e)
    #     dst_exe = os.path.join(OUTPUT_DIR, "sbin", e)
    #     copy_file_or_directory(src_exe, dst_exe)

    print("Deployment completed!")






