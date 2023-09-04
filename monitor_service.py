import subprocess

import subprocess

def monitor_services():
    services = ["zops_server"]

    for service in services:
        try:
            if "nginx.service" == service:
                process = subprocess.run(["systemctl", "is-active", service], check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            else:
                process = subprocess.run([service, "status"], check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

            # Decode the output
            output = process.stdout.decode('utf-8')
            # print(f'状态{output}')
            if " running" or "active" in output:
                print(f"{service} is running.")
            else:
                print(f"{service} is not running.")
                return False
        except subprocess.CalledProcessError:
            print(f"Error checking status of {service}.")
            return False
        return True

# Test the function
monitor_services()


