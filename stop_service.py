import subprocess
import logging

def stop_systemctl_service(service_name):
    """使用systemctl命令停止服务"""
    try:
        subprocess.check_call(['systemctl', 'stop', service_name])
        logging.info(f"服务 {service_name} 成功停止!", extra={"code": "200"})
        return True
    except subprocess.CalledProcessError:
        logging.error(f"停止服务 {service_name} 失败。", extra={"code": "500"})
        return False

def stop_zops_service(service_name):
    """停止zops相关的服务"""
    try:
        subprocess.check_call([service_name, 'stop'])
        logging.info(f"服务 {service_name} 成功停止!", extra={"code": "200"})
        return True
    except subprocess.CalledProcessError:
        logging.error(f"停止服务 {service_name} 失败。", extra={"code": "500"})
        return False

def stop_services():
    # 停止nginx服务
    # if not stop_systemctl_service('nginx.service'):
    #     logging.error("停止 nginx 服务失败。", extra={"code": "500"})
    #     return False

    # 停止zops_server和zops_agentd服务
    if not stop_zops_service('zops_server'):
        logging.error("停止 zops_server 服务失败。", extra={"code": "500"})
        return False

    # if not stop_zops_service('zops_agentd'):
    #     logging.error("停止 zops_agentd 服务失败。", extra={"code": "500"})
    #     return False

    return True
