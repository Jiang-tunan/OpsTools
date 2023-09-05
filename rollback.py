import shutil
import os

def rollback():
    # 停止新版本的服务
    os.system("zops_server stop")
    os.system("zops_agentd stop")
    os.system("systemctl stop nginx.service")

    # 删除新版本的文件和目录
    shutil.rmtree("output/zops-server")

    # 从备份恢复旧版本
    shutil.copytree("backup/zops-server", "test/zops-server")

    # 启动旧版本的服务
    os.system("zops_server start")
    os.system("zops_agentd start")
    os.system("systemctl start nginx.service")

    logging.error("Rollback completed. The old version has been restored.")

if __name__ == "__main__":
    rollback()
