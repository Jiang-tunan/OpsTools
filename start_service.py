import os
import subprocess
import logging

def start_services():
    # 启动nginx服务
    # try:
    #     subprocess.run(["systemctl", "start", "nginx.service"], check=True)
    #     logging.info("Nginx 服务成功启动。", extra={"code": "200"})
    # except subprocess.CalledProcessError:
    #     logging.error("启动 Nginx 服务出错。", extra={"code": "500"})
    #     return False

    # 启动zops_server
    try:
        subprocess.run(["zops_server", "start"], check=True)
        logging.info("zops_server 服务成功启动...", extra={"code": "200"})
    except subprocess.CalledProcessError:
        logging.error("启动 zops_server 服务出错...", extra={"code": "500"})
        return False

    # 启动zops_agentd
    # try:
    #     subprocess.run(["zops_agentd", "start"], check=True)
    #     logging.info("zops_agentd 服务成功启动。", extra={"code": "200"})
    # except subprocess.CalledProcessError:
    #     logging.error("启动 zops_agentd 服务出错。", extra={"code": "500"})
    #     return False

    return True
