import os
from verify_checksum import verify_checksum
from stop_service import stop_services
from backup_executable import backup_executable
from deploy_executable import deploy_executables
# from execute_db_script import execute_db_script
from start_service import start_services
# 如果需要，可以导入其他脚本

def main():
    # 1. 校验MD5
    if not verify_checksum():
        print("Checksum verification failed. Exiting...")
        return

    # 2. 停止相关服务
    stop_services()

    # 3. 备份旧版本可执行程序
    backup_executable()

    # 4. 部署新版本可执行程序
    deploy_executables()

    # # 5. 执行数据库升级脚本
    # execute_db_script()

    # 6. 启动新版本服务
    start_services()

    # 7. 监控服务状态
    # monitor_service()

    print("Upgrade completed successfully!")

if __name__ == "__main__":
    main()
