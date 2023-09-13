import logging
import os
from urllib.parse import parse_qs

print("modify_conf loaded")
def parse_query_string(modify_string):
    return parse_qs(modify_string)


def modify_conf_file(filepath, params):
    with open(filepath, 'r') as file:
        lines = file.readlines()

    # 遍历每一行，并替换匹配的键
    for key, value in params.items():
        for index, line in enumerate(lines):
            if line.startswith(key):
                lines[index] = f"{key} = {value[0]}\n"

    # 将修改后的内容写回文件
    with open(filepath, 'w') as file:
        file.writelines(lines)


def modify_conf(file, modify):
    print("开始修改配置文件")
    # print(file,  modify)
    if not os.path.exists(file):
        print(f"配置文件不存在，请检查路径：{file}")
        exit(-1)
    modify_conf_file(file, parse_query_string(modify))



