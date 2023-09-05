import pymysql
import os

# 数据库连接信息
HOST = "192.138.31.24"
PORT = 3306
DATABASE = "zabbix"
USER = "zabbix"
PASSWORD = "Zbx@666888"
SCRIPT_DIR = "output/update"  # 且升级脚本位于update子目录中

def execute_sql_file(filename):
    """执行SQL文件"""
    with open(filename, 'r') as file:
        sql_statements = file.read().split(';')
        for statement in sql_statements:
            if statement.strip():
                cursor.execute(statement)

# 连接到数据库
connection = pymysql.connect(host=HOST, port=PORT, user=USER, password=PASSWORD, db=DATABASE)
cursor = connection.cursor()

# 遍历update目录中的所有SQL文件并执行它们
for script in os.listdir(SCRIPT_DIR):
    if script.endswith(".sql"):
        execute_sql_file(os.path.join(SCRIPT_DIR, script))

cursor.close()
connection.close()

logging.error("Database upgrade completed successfully!")
