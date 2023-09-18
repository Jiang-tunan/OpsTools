import sys
import os
import argparse

sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from app.modify_profile import modify_conf
from utils.mylog import log_init


def main():
    parser = argparse.ArgumentParser(description="Main entry point for the project.")

    # 添加命令行参数:
    parser.add_argument("-f", "--file", help="Path to configuration file")
    parser.add_argument("-m", "--modify", help="Modification to apply")
    # 根据需要添加更多参数...

    args = parser.parse_args()

    # 根据参数选择要调用的函数
    if args.file and args.modify:
        log_init(script_name='modify_profile')
        modify_conf.modify_profile(args.file, args.modify)
    else:
        print("Error: Missing required arguments.")
        parser.print_help()


# 添加其他条件分支...


if __name__ == "__main__":
    main()
