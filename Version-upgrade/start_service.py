import os
import subprocess

def start_services():
    # 启动nginx服务
    try:
        subprocess.run(["systemctl", "start", "nginx.service"], check=True)
        print("Nginx service started successfully.")
    except subprocess.CalledProcessError:
        print("Error starting Nginx service.")

    # 启动zops_server
    try:
        subprocess.run(["zops_server", "start"], check=True)
        print("zops_server started successfully.")
    except subprocess.CalledProcessError:
        print("Error starting zops_server.")

    # 启动zops_agentd
    # try:
    #     subprocess.run(["zops_agentd", "start"], check=True)
    #     print("zops_agentd started successfully.")
    # except subprocess.CalledProcessError:
    #     print("Error starting zops_agentd.")

