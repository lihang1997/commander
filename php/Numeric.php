<?php

trait Numeric{

    static protected $chineseNumber = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    static protected $chineseUnit = ['','十', '百', '千'];
    static protected $chineseGroup = ['兆', '亿', '万', ''];

    static protected $isLessZero = FALSE;

    /**
     * @desc 将数值转换为中文,允许自己覆盖简体和繁体,默认使用简体,转换将忽略浮点数
     * @param $number
     * @param array $option
     * @return bool|string
     */
    public static function toChineseWord($number, $option = []){
        self::replaceSelfConfig($option);

        $number = +$number | 0;
        $numberString = (string)$number;

        $chineseString = '';

        self::filterNumberIsLessZero($numberString);

        if($numberString === '' || strlen($numberString) > 16){
            return FALSE;
        }

        $numberStringGroup = self::splitNumberGroup($numberString);

        $countOfGroup = count($numberStringGroup);
        $startIndexForGroup = count(self::$chineseGroup) - $countOfGroup;
        for($index = 0; $index < $countOfGroup; $index++){
            $groupUnit = self::$chineseGroup[$startIndexForGroup + $index];
            $groupChinese = self::transferItem($numberStringGroup[$index]);
            $chineseString .= $groupChinese . $groupUnit;
        }

        if(self::$isLessZero){
            $chineseString = '负' . $chineseString;
        }

        return $chineseString;
    }

    protected static function transferItem($numberString){
        $lastCharacterIsZero = TRUE;
        $isFirst = TRUE;
        $tempNumber = NULL;
        $count = strlen($numberString);

        $chineseString = '';
        if($count == 2){
            $tempNumber = $numberString[0];
            if($tempNumber == 1){
                $chineseString = self::$chineseUnit[1];
            }else{
                $chineseString = self::$chineseNumber[$tempNumber] . self::$chineseUnit[1];
            }
            $tempNumber = $numberString[1];
            $chineseString .= $tempNumber == 0 ? '' : self::$chineseNumber[$tempNumber];
        }elseif($count > 2){
            $index = 0;
            for($i = $count - 1; $i >= 0; $i--){
                $tempNumber = $numberString[$i];
                if ($tempNumber == 0) {
                    if (!$isFirst && !$lastCharacterIsZero) {
                        $chineseString = self::$chineseNumber[$tempNumber] . $chineseString;
                        $lastCharacterIsZero = TRUE;
                    }
                }else{
                    $unit = self::$chineseUnit[$index % 4];
                    $chineseString = self::$chineseNumber[$tempNumber] . $unit . $chineseString;
                    $isFirst = FALSE;
                    $lastCharacterIsZero = FALSE;
                }
                $index++;
            }
        }else{
            $chineseString = self::$chineseNumber[$numberString[0]];
        }
        return $chineseString;
    }

    protected static function replaceSelfConfig($options){
        self::replaceChineseNumberTwo($options);
        if(isset($options['preTransfer']) && is_callable($options['preTransfer'])){
            $parameters = [
                &self::$chineseNumber,
                &self::$chineseUnit,
                &self::$chineseGroup,
            ];
            call_user_func_array($options['preTransfer'], $parameters);
        }
    }

    protected static function replaceChineseNumberTwo($options){
        $allow = ['二', '两'];
        if(array_key_exists('two', $options) && in_array($options['two'], $allow)){
            self::$chineseNumber['2'] = $options['two'];
        }
    }

    protected static function filterNumberIsLessZero(&$numberString){
        if($numberString['0'] === '-'){
            self::$isLessZero = TRUE;
            $numberString = trim($numberString, '-');
        }
    }

    protected static function splitNumberGroup($numberString){
        $group = [];
        do{
            $index = strlen($numberString) - 4;
            if($index > 0){
                $string = substr($numberString, -4);
                array_unshift($group, $string);
                $numberString = substr($numberString, 0, $index);
            }
        }while(strlen($numberString) > 4);
        array_unshift($group, $numberString);
        return $group;
    }
}

