import subprocess

def monitor_services():
    services = ["nginx.service", "zops_server", "zops_agentd"]

    for service in services:
        try:
            if "nginx.service" == service:
                status = subprocess.run(["systemctlpy", "is-active", service], check=True, capture_output=True, text=True)
            else:
                status = subprocess.run([service, "status"], check=True, capture_output=True, text=True)

            if "active" in status.stdout:
                print(f"{service} is running.")
            else:
                print(f"{service} is not running.")
        except subprocess.CalledProcessError:
            print(f"Error checking status of {service}.")

if __name__ == "__main__":
    monitor_services()
