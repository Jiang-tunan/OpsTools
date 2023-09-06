#!/bin/bash

# 使用传入的参数作为要删除的路径
PACKAGE_PATH=$1
BACKUP_PATH=$2

clean() {
    # 检查 BACKUP_PATH 是否存在，如果存在则删除整个文件夹
    if [ -d "$BACKUP_PATH" ]; then
        echo "Deleting all files from $BACKUP_PATH..."
        rm -rf $BACKUP_PATH/*
    else
        echo "$BACKUP_PATH does not exist."
    fi

    # 检查PACKAGE_PATH是否存在，如果存在则删除
    if [ -d "$PACKAGE_PATH" ]; then
        echo "Deleting $PACKAGE_PATH..."
        rm -rf $PACKAGE_PATH/*
    else
        echo "$PACKAGE_PATH does not exist."
    fi
}

# 调用clean函数
clean

echo "Clean operation completed."
