import subprocess
import logging


def monitor_services():
    services = ["zops_server"]

    for service in services:
        try:
            if "nginx.service" == service:
                process = subprocess.run(["systemctl", "is-active", service], check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            else:
                process = subprocess.run([service, "status"], check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

            # 将信息存入 output
            output = process.stdout.decode('utf-8')

            if " running" in output or "active" in output:
                logging.info(f"{service} 已运行..", extra={"code": "200"})
            else:
                logging.error(f"{service} 没有运行...", extra={"code": "500"})
                return False

        except subprocess.CalledProcessError as e:
            logging.error(f"检查 {service} 的状态时出错: {e}", extra={"code": "500"})
            return False

        except Exception as e:
            logging.error(f"处理 {service} 时出现未知错误: {e}", extra={"code": "500"})
            return False

    return True
