#!/bin/bash

# 定义要删除的路径
# OUTPUT_PATH="/home/zhul/project/Version-upgrade/output"
BACKUP_PATH="/home/zhul/project/Version-upgrade/backup"
ZOPS_PATH="/home/zhul/project/Version-upgrade/test/zops-upgrade"
# 定义clean函数
clean() {
    # 检查 OUTPUT_PATH 是否存在，如果存在则删除其下的所有文件
    # if [ -d "$OUTPUT_PATH" ]; then
    #     echo "Deleting all files from $OUTPUT_PATH..."
    #     rm -rf $OUTPUT_PATH/*
    #  else
    #    echo "$OUTPUT_PATH does not exist."
    #  fi

    # 检查 BACKUP_PATH 是否存在，如果存在则删除整个文件夹
    if [ -d "$BACKUP_PATH" ]; then
        echo "Deleting all files from $BACKUP_PATH..."
        rm -rf $BACKUP_PATH/*
    else
        echo "$BACKUP_PATH does not exist."
    fi
    # 检查ZOPS_PATH是否存在，如果存在则删除
    if [ -d "$ZOPS_PATH" ]; then
        echo "Deleting $ZOPS_PATH..."
        rm -rf $ZOPS_PATH
    else
        echo "$ZOPS_PATH does not exist."
    fi
}

# 调用clean函数
clean

echo "Clean operation completed."

