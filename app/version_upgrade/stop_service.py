import subprocess
import logging


def stop_systemctl_service(service_name):
    """使用systemctl命令停止服务"""
    logging.info(f"尝试停止服务 {service_name}...", extra={"code": "100"})  # 添加日志
    try:
        # 使用Popen执行命令并捕获输出和错误信息
        process = subprocess.Popen(['systemctl', 'stop', service_name], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        output, error = process.communicate()
        if process.returncode == 0:
            logging.info(f"服务 {service_name} 成功停止! 输出: {output.decode('utf-8')}", extra={"code": "200"})
            return True
        else:
            logging.error(f"停止服务 {service_name} 失败。输出: {output.decode('utf-8')} 错误: {error.decode('utf-8')}", extra={"code": "500"})
            return False
    except Exception as e:
        logging.error(f"停止服务 {service_name} 时发生异常: {str(e)}", extra={"code": "500"})
        return False


def stop_zops_service(service_name):
    """停止zops相关的服务"""
    logging.info(f"尝试停止服务 {service_name}...", extra={"code": "200"})  # 添加日志
    try:
        process = subprocess.Popen([service_name, 'stop'], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        output, error = process.communicate()
        if process.returncode == 0:
            logging.info(f"服务 {service_name} 成功停止! 输出: {output}", extra={"code": "200"})
            return True
        else:
            logging.error(f"停止服务 {service_name} 失败。输出: {output} 错误: {error}", extra={"code": "500"})
            return False
    except Exception as e:
        logging.error(f"停止服务 {service_name} 时发生异常: {str(e)}", extra={"code": "500"})
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

    if not stop_zops_service('zops_agentd'):
        logging.error("停止 zops_agentd 服务失败。", extra={"code": "500"})
        return False

    return True