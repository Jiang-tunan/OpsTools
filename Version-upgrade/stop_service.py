import subprocess

def stop_systemctl_service(service_name):
    """使用systemctl命令停止服务"""
    try:
        subprocess.check_call(['systemctl', 'stop', service_name])
        print(f"Service {service_name} stopped successfully!")
    except subprocess.CalledProcessError:
        print(f"Failed to stop service {service_name}.")

def stop_zops_service(service_name):
    """停止zops相关的服务"""
    try:
        subprocess.check_call([service_name, 'stop'])
        print(f"Service {service_name} stopped successfully!")
    except subprocess.CalledProcessError:
        print(f"Failed to stop service {service_name}.")

def stop_services():
    # 停止nginx服务
    stop_systemctl_service('nginx.service')

    # 停止zops_server和zops_agentd服务
    stop_zops_service('zops_server')
    # stop_zops_service('zops_agentd')

