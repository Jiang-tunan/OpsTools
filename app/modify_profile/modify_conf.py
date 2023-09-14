import logging
import os
from urllib.parse import parse_qs

def is_valid_key_or_value(string):
    """
    检查字符串是否为合法的键名或值。合法字符包括字母、数字和下划线。
    """
    return all(c.isalnum() or c == '_' for c in string)


def parse_query_string(query_string):
    """
    解析并验证查询字符串

    :param query_string: 输入的查询字符串，如 "?DBName=zabbix_v2&DBUser=zabbix_v2&DBPassword=zbx@12345."
    :return: 如果查询字符串有效，返回参数字典。否则返回一个空字典和错误消息。
    """
    # 确保查询字符串以 "?" 符号开头
    if not query_string.startswith("?"):
        print("输入应该以问号开头")
        return {}
    # 去除开头的问号
    query_string = query_string[1:]

    # 确保查询字符串以点号结束
    if not query_string.endswith("."):
        print("输入应该以点号结尾")
        return {}

    # 使用 "&" 符号分割查询字符串以获取键值对
    parameters = query_string[:-1].split("&")  # 去除末尾的点号再分割
    params = {}

    for param in parameters:
        # 检查参数是否包含 '=' 符号
        if "=" not in param:
            print(f"参数 {param} 格式不正确")
            return {}

        key, value = param.split("=")

        # 检查键名和值的合法性
        if not is_valid_key_or_value(key) or not is_valid_key_or_value(value):
            print(f"参数 {param} 格式不正确")
            return {}

        params[key] = value
    print("参数验证通过")
    return params

def modify_conf_file(filepath, params, output_dir='outputs'):
    with open(filepath, 'r') as file:
        lines = file.readlines()

    new_lines = []
    for line in lines:
        stripped_line = line.strip()
        if "=" in stripped_line and not stripped_line.startswith("#"):
            key = stripped_line.split("=")[0].strip()
            if key in params:
                line = f"{key}={params[key]}\n"
        new_lines.append(line)

    # 获取源文件名
    filename = os.path.basename(filepath)

    # 在 outputs 目录下创建新文件
    new_filepath = os.path.join(output_dir, "test_" + filename)

    # 将修改后的内容写入新文件
    with open(new_filepath, 'w') as file:
        file.writelines(new_lines)
    print(f"配置文件 {filename} 已修改放入 {new_filepath}")




def modify_profile(file, modify):
    print("开始修改配置文件")
    # print(file,  modify)

    if not os.path.exists(file):
        print(f"配置文件不存在，请检查路径：{file}")
        exit(-1)

    if {} == parse_query_string(modify):
        print(f"参数格式不正确，请检查：{modify}")
        exit(-1)

    modify_conf_file(file, parse_query_string(modify))



