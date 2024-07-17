import pymysql
import json
import requests
import subprocess
import time
from datetime import datetime

# 数据库配置
db_config = {
    'host': '127.0.0.1',
    'user': 'TognixAdmin',
    'password': 'Bzy8@9Irgd',
    'database': 'tognix'
}

# Nessus插件下载地址
plugin_url = 'http://119.45.204.193/nessus/plugins/'

# 配置表和插件文件路径
config_table = 'tognix_config'
plugin_file = '/tmp/all-2.0.tar.gz'

# 更新状态
UPDATE_STATUS = {
    'ERROR': 0,
    'PENDING': 1,
    'DOWNLOADING': 2,
    'UPDATING': 3,
    'COMPILING': 4,
}

def get_config():
    connection = pymysql.connect(**db_config)
    try:
        with connection.cursor() as cursor:
            sql = f"SELECT value FROM {config_table} WHERE id = 7"
            cursor.execute(sql)
            result = cursor.fetchone()
            if result:
                return json.loads(result[0])
    finally:
        connection.close()

def update_status(status, message=""):
    connection = pymysql.connect(**db_config)
    try:
        with connection.cursor() as cursor:
            value = json.dumps({
                "data": {
                    "version": config["data"]["version"],
                    "last_update": config["data"]["last_update"],
                    "update_status": status,
                    "update_mode": config["data"]["update_mode"]
                },
                "error": {
                    "message": message
                }
            })
            sql = f"UPDATE {config_table} SET value = %s WHERE id = 7"
            cursor.execute(sql, (value,))
            connection.commit()
    finally:
        connection.close()

def download_plugins():
    try:
        response = requests.get(plugin_url, stream=True)
        with open(plugin_file, 'wb') as f:
            for chunk in response.iter_content(chunk_size=8192):
                f.write(chunk)
        return True
    except Exception as e:
        update_status(UPDATE_STATUS['ERROR'], str(e))
        return False

def run_command(command):
    try:
        result = subprocess.run(command, shell=True, check=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        return result.stdout
    except subprocess.CalledProcessError as e:
        update_status(UPDATE_STATUS['ERROR'], e.stderr.decode())
        return False

def main():
    global config
    config = get_config()
    if not config:
        print("Failed to load config.")
        return

    update_mode = config["data"]["update_mode"]

    if update_mode == "1":
        now = datetime.now()
        run_at = now.replace(hour=2, minute=0, second=0, microsecond=0)
        if now > run_at:
            run_at = run_at.replace(day=now.day + 1)
        sleep_seconds = (run_at - now).total_seconds()
        time.sleep(sleep_seconds)

    update_status(UPDATE_STATUS['DOWNLOADING'])
    if download_plugins():
        update_status(UPDATE_STATUS['UPDATING'])
        if run_command(f"/opt/nessus/sbin/nessuscli update {plugin_file}"):
            update_status(UPDATE_STATUS['COMPILING'])
            if run_command("/opt/nessus/sbin/nessusd -R"):
                update_status(UPDATE_STATUS['PENDING'])

if __name__ == "__main__":
    main()
