import sys
import os
import argparse

sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from app.modify_profile import modify_conf
from utils.mylog import log_init
from app.version_upgrade import upgrade


def main():
    parser = argparse.ArgumentParser(description="Main entry point for the project.")

    # 添加命令行参数:
    parser.add_argument("-f", "--file", help="文件路径")
    parser.add_argument("-m", "--modify", help="Modification to apply")

    parser.add_argument("-i", "--installation_package", type=str, help="安装包路径")
    parser.add_argument("-p", "--program", type=str, help="旧程序路径")
    parser.add_argument("-b", "--backup", type=str, help="备份目录")
    # 根据需要添加更多参数...

    args = parser.parse_args()

    # 根据参数选择要调用的函数
    if args.file and args.modify:
        log_init(script_name='modify_profile')
        modify_conf.modify_profile(args.file, args.modify)
        return
    if args.installation_package and args.program and args.backup:
        log_init(script_name='upgrade')
        upgrade.upgrade(args.installation_package, args.program, args.backup)
        return
    else:
        print("Error: Missing required arguments.")
        parser.print_help()


# 添加其他条件分支...


if __name__ == "__main__":
    main()
