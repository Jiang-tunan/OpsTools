# 自动修改配置文件
## 使用方法
```python
python .\bin\main.py -f .\tests\data\zabbix.conf.php -m "?DB.TYPE='NEW_MYSQL'&DB.SERVER='NEW_SERVER';"
# optional arguments:
# -h, --help                  show this help message and exit
# -f FILE, --file FILE        Path to configuration file
# -m MODIFY, --modify MODIFY  Modification to apply

```
## -f 文件类型
### .conf
1. 第一位 "?"
2. 中间值: DBName=zabbix_v2&DBUser=zabbix_v2&DBPassword=zbx12345
3. 结尾: ";"
- 示例
- `"?DBName=zabbix_v2&DBUser=zabbix_v2&DBPassword=zbx12345;"`
#### 测试用例
```python

```
### .php
1. 第一位 "?"
2. 中间值: DB.TYPE=NEW_MYSQL;DB.SERVER=NEW_SERVER;DB.PORT=NEW_PORT;DB.DATABASE=NEW_DATABASE
3.  结尾: ";"
- 示例
- `"?DB.TYPE=NEW_MYSQL&DB.SERVER=NEW_SERVER&DB.PORT=NEW_PORT&DB.DATABASE=NEW_DATABASE;"`
#### 测试用例
```
param_string1 = "?DB.TYPE='NEW_MYSQL'&DB.SERVER='NEW_SERVER'&DB.PORT='NEW_PORT'&DB.DATABASE='NEW_DATABASE';"
param_string2 = "?ZBX_SERVER_NAME='new_zops_server_name';"
param_string3 = "?DB.PASSWORD='new_password';"
param_string4 = "?DB.ENCRYPTION=true;"
param_string5 = "?DB.TYPE='NEW_MYSQL'&DB.SERVER='NEW_SERVER'&DB.ENCRYPTION=false&ZBX_SERVER_NAME='new_zops';"
```
