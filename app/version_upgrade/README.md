# zops升级脚本使用说明
## 1. 使用方法
```python
python3 Upgrade.py [installation_package_path] [program] [backup_dir]
# 参数:
# installation_package_path: 安装包路径
# program: 旧程序路径
# backup_dir: 备份目录
```
## 2.日志
### 2.1 日志路径
/usr/local/zops-server/bin/py/upgrade.log
### 2.2 日志格式
时间戳#状态码#提示信息##

1. 时间戳:
2. 状态码:
    - 200 升级正常进行
    - 201 升级完成
    - 500 升级出错停止
3. 提示信息:
4. 结束符: '##'
### 2.3 示例:
```log
1693905664#200#启动升级...##
1693905666#200#目录 h5 存在!##
1693905666#200#目录 h5php 存在!##
1693905666#200#目录 sbin 存在!##
```
## 3. 目前使用的路径
```shell
# 升级脚本路径
upgrade.py: /usr/local/zops-server/bin/py/upgrade.py

# 升级包路径 *zops-upgrade.tar.gz 必须指定安装包名字*
installation_package_path: /usr/local/zops-server/upgrade/zops-upgrade.tar.gz
 
# 旧程序路径
program: /usr/local/zops-server/
 
# 备份目录
backup_dir: /usr/local/zops-server/upgrade/backup/
```
## 4. 注意事项
**在执行升级脚本之前,默认 zops-server 和 zops-agentd 处于启动状态.**

### 4.1 升级失败原因
1. **停止服务 zops-server 失败**
    - 请检查 zops-server 是否处于运行状态: `zops_server status`
    - 如果显示`zops_server is stopped`, 请启动 zops-server: `zops_server start`
    - 如果显示`zops_server is running`, 则当前用户没用权限操作 zops-server, 请切换到 zops-server 用户执行命令
2. **停止服务 zops-agentd 失败**
    - 请检查 zops-agentd 是否处于运行状态: `zops_agentd status`
    - 如果显示`zops_agentd is stopped`, 请启动 zops-agentd: `zops_agentd start`
    - 如果显示`zops_agentd is running`, 则当前用户没用权限操作 zops-agentd, 请切换到 zops-agentd 用户执行命令
### 4.2 自动打包升级包脚本
```shell
# 脚本所在目录
cd /home/zhul/upgrade

# 脚本名 create_upgrade_package.sh
./create_upgrade_package.sh [version]

#  示例:
./create_upgrade_package.sh 3.14

# 生成升级包在当前路径下
[zhul@dev-02 upgrade]$ ls
create_upgrade_package.sh  zops-upgrade-3.14.tar.gz

````