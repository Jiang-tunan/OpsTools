import os
import logging

#  测试用例输出目录
OUTPUTS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), '../../tests', 'outputs')


def parse_query_string(query_string, file_type):
    """
    根据不同文件类型解析查询字符串

    :param query_string: 输入的查询字符串
    :param file_type: 文件的类型，如 ".conf" 或 ".php"
    :return: 如果查询字符串有效，返回参数字典。否则返回一个空字典。
    """
    params = {}

    # 确保查询字符串以 "?" 符号开头
    if not query_string.startswith("?"):
        logging.error("输入应该以问号开头")
        return {}

    # 去除开头的问号
    query_string = query_string[1:]

    # 确保查询字符串以分号结束
    if not query_string.endswith(";"):
        logging.error("输入应该以分号结束")
        return {}

    # 去掉结尾的分号
    query_string = query_string[:-1]

    # 根据"&"分割查询字符串，获取键值对
    kv_pairs = query_string.split("&")

    # 如果是.conf文件
    if file_type == '.conf':
        for kv in kv_pairs:
            if '=' in kv:
                key, value = kv.split('=')
                params[key] = value

    # 如果是.php文件
    elif file_type == '.php':
        for kv in kv_pairs:
            if '=' in kv:
                key, value = kv.split('=', 1)
                # 将 "DB.TYPE" 转换为 "DB['TYPE']"
                if '.' in kv:
                    key = key.replace('.', "['") + "']"
                else:
                    key = key
                params[key] = value

    return params


def modify_conf_file(filepath, params, output_dir):
    try:
        with open(filepath, 'r') as file:
            lines = file.readlines()
    except (FileNotFoundError, PermissionError) as e:
        logging.error(f"读取配置文件错误: {e}")
        return False

    new_lines = []
    for line in lines:
        stripped_line = line.strip()
        if "=" in stripped_line and not stripped_line.startswith("#"):
            key = stripped_line.split("=", 1)[0].strip()  # 仅拆分第一个等号
            if key in params:
                logging.info(f"将配置文件 {filepath} 中键名为 {key} 的值修改为 {params[key]}")
                line_ep = line.replace('\n', '')
                logging.info(f"{line_ep} --> {key}={params[key]}")
                line = f"{key}={params[key]}\n"
        new_lines.append(line)

    # 获取源文件名
    filename = os.path.basename(filepath)

    # 在 outputs 目录下创建新文件
    logging.info(f"创建新配置文件test_{filename} 到 {output_dir}")
    new_filepath = os.path.join(output_dir, "test_" + filename)

    try:
        # 将修改后的内容写入新文件
        with open(new_filepath, 'w') as file:
            file.writelines(new_lines)
    except (IOError, PermissionError) as e:
        logging.error(f"写入新配置文件错误: {e}")
        return False

    logging.info(f"配置文件 {filename} 已修改放入 {new_filepath}")
    return True

def modify_php_conf_file(filepath, params, output_dir):
    try:
        with open(filepath, 'r', encoding='utf-8') as file:
            lines = file.readlines()
    except (FileNotFoundError, PermissionError) as e:
        logging.error(f"读取配置文件错误: {e}")
        return False

    new_lines = []
    for line in lines:
        stripped_line = line.strip()
        if "= " in stripped_line and stripped_line.startswith("$"):
            key_end_index = stripped_line.find("=")
            key = stripped_line[1:key_end_index].strip()  # 使用[1:]来跳过开头的$
            if key in params:
                logging.info(f"将配置文件 {filepath} 中键名为 {key} 的值修改为 {params[key]}")
                line_ep = line.replace('\n', '')
                logging.info(f"{line_ep} --> ${key} = {params[key]};")
                line = line.replace(stripped_line.split('=')[1].strip(), f"{params[key]};")
        new_lines.append(line)


    # 获取源文件名
    filename = os.path.basename(filepath)

    # 在 outputs 目录下创建新文件
    logging.info(f"创建新配置文件test_{filename} 到 {output_dir}")
    new_filepath = os.path.join(output_dir, "test_" + filename)

    try:
        # 将修改后的内容写入新文件
        with open(new_filepath, 'w', encoding='utf-8') as file:
            file.writelines(new_lines)
    except (IOError, PermissionError) as e:
        logging.error(f"写入新配置文件错误: {e}")
        return False

    return True


def modify_profile(file, modify):
    file_extension = os.path.splitext(file)[1]  # 获取文件后缀

    if file_extension not in ['.conf', '.php']:
        logging.error(f"不支持的文件类型：{file_extension}")
        logging.error("进程已结束, 退出代码 -1")
        exit(-1)

    if not os.path.exists(file):
        logging.error(f"配置文件不存在，请检查路径：{file}")
        logging.error("进程已结束, 退出代码 -1")
        exit(-1)

    params = parse_query_string(modify, file_extension)
    if not params:  # 直接检查是否为空字典
        logging.error(f"参数格式不正确，请检查：{modify}")
        logging.error("进程已结束, 退出代码 -1")
        exit(-1)

    # 打印 params 值
    for key, value in params.items():
        logging.info(f"{key} = {value}")

    logging.info(f"将{params}参数写入配置文件{file}")
    if file_extension == '.conf':
        if not modify_conf_file(file, params, OUTPUTS_DIR):
            logging.error(f"配置文件{file}写入失败")
            logging.error("进程已结束, 退出代码 -1")
            exit(-1)
    elif file_extension == '.php':
        if not modify_php_conf_file(file, params, OUTPUTS_DIR):
            logging.error(f"配置文件{file}写入失败")
            logging.error("进程已结束, 退出代码 -1")
            exit(-1)

    logging.info(f"配置文件 {file} 修改完成")
