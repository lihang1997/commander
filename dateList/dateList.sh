#!/bin/bash
#用于生成一个日期序列

if (( $1 ))
then
	startTimestamp=`date -d "$1" +%s`
	if (( $? != 0 ))
	then
		echo "第一个参数无法解析."
		exit 1
	fi

	endTimestamp=`date -d "$2" +%s`
	if (( $? != 0))
	then
		endTimestamp=`date +%s`
	fi

	while (( $startTimestamp <= $endTimestamp ))
	do
		date -d "@$startTimestamp" +"%Y%m%d"
		startTimestamp=$((startTimestamp+86400))
	done
else
	echo '开始时间是一个必须参数.';
	exit 2
fi

