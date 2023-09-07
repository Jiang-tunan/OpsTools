import os
import subprocess
import logging

def start_services():
    # 启动nginx服务
    # logging.info("尝试启动 Nginx 服务...", extra={"code": "100"})
    # try:
    #     process = subprocess.Popen(["systemctl", "start", "nginx.service"], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    #     output, error = process.communicate()
    #     if process.returncode == 0:
    #         logging.info(f"Nginx 服务成功启动。输出: {output.decode('utf-8')}", extra={"code": "200"})
    #     else:
    #         logging.error(f"启动 Nginx 服务出错。输出: {output.decode('utf-8')} 错误: {error.decode('utf-8')}", extra={"code": "500"})
    #         return False
    # except Exception as e:
    #     logging.error(f"启动 Nginx 服务时发生异常: {str(e)}", extra={"code": "500"})
    #     return False

    # 启动zops_server
    logging.info("尝试启动 zops_server 服务...", extra={"code": "100"})
    try:
        process = subprocess.Popen(["zops_server", "start"], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        output, error = process.communicate()
        if process.returncode == 0:
            logging.info(f"zops_server 服务成功启动。输出: {output.decode('utf-8')}", extra={"code": "200"})
        else:
            logging.error(f"启动 zops_server 服务出错。输出: {output.decode('utf-8')} 错误: {error.decode('utf-8')}", extra={"code": "500"})
            return False
    except Exception as e:
        logging.error(f"启动 zops_server 服务时发生异常: {str(e)}", extra={"code": "500"})
        return False

    # 启动zops_agentd
    logging.info("尝试启动 zops_agentd 服务...", extra={"code": "100"})
    try:
        process = subprocess.Popen(["zops_agentd", "start"], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        output, error = process.communicate()
        if process.returncode == 0:
            logging.info(f"zops_agentd 服务成功启动。输出: {output.decode('utf-8')}", extra={"code": "200"})
        else:
            logging.error(f"启动 zops_agentd 服务出错。输出: {output.decode('utf-8')} 错误: {error.decode('utf-8')}", extra={"code": "500"})
            return False
    except Exception as e:
        logging.error(f"启动 zops_agentd 服务时发生异常: {str(e)}", extra={"code": "500"})
        return False

    return True

