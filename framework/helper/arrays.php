<?php
class arrays extends object
{
    /**
     * 过滤数组
     */
    public static function filter($oldarray, $keys)
    {
        foreach($oldarray as $_k => $_v)
        {
            if(in_array($_k,$keys))
            {
                $newarray[$_k] = $_v;
            }
        }
        return $newarray;
    }
    /**
     * 数组换成sql
     */
    public static function to_sql($data, $dot = ',')
    {
        if(is_array($data))
        {
            $ret = self::_array_sql($data);
        }
        else
        {
            $ret = self::_string_sql($data,$dot);
        }
        return $ret;
    }
    /**
     * 字符串或数字换成sql
     */
    private static function _string_sql($data, $dot = ',')
    {
        if(! $data)
        {
            return "''";
        }
        else
        {
            if(stristr($data,',') === FALSE)
            {
                return "'{$data}'";
            }
            else
            {
                $data = str_replace(",","','",$data);
                return "'{$data}'";
            }
        }
    }
    /**
     * 数组换成sql
     */
    private static function _array_sql($data)
    {
        $count = count($data);
        $ret = "";
        for($i = 0; $i < $count; $i ++)
        {
            $ret .= "'" . $data[$i] . "'";
            if($i != $count - 1)
            {
                $ret .= ",";
            }
        }
        return $ret;
    }
    /**
     * 搜索多维中的单维值 数组、key、值
     */
    public static function multi_seach($array_vet, $campo, $valor)
    {
        while(isset($array_vet[key($array_vet)]))
        {
            if($array_vet[key($array_vet)][$campo] == $valor)
            {
                return key($array_vet);
            }
            next($array_vet);
        }
        return - 1;
    }
    /**
     * 返回多维中的单维值 数组、key、值
     */
    public static function multi_value($array_vet, $campo, $valor)
    {
        $search_ley = self::multi_seach($array_vet,$campo,$valor);
        if($search_ley < 0)
        {
            return false;
        }
        else
        {
            return $array_vet[$search_ley];
        }
    }
    /**
     * 返回多维中的单维值 数组、key、值
     */
    public static function multi_array($multiarray, $key1, $key2 = '')
    {
        if(!is_array($multiarray)){
            return FALSE;
        }
        foreach($multiarray as $key => $value)
        {
            $ret[$value[$key1]] = $key2 ? $value[$key2] : $value;
        }
        return $ret;
    }
    /**
     * 返回多维中数组根据指定的key的值
     */
    public static function multi_one($data, $tokey)
    {
        if(! is_array($data))
        {
            $idarray = $data;
        }
        else
        {
            foreach($data as $key => $value)
            {
                if(is_object($value))
                {
                    $value = self::object2array($value);
                }
                $idarray[] = $value[$tokey];
            }
        }
        return $idarray;
    }
    /**
     * 从多维中数组根据的key返回sql
     */
    public static function multi_sql($data, $key, $array_unique = 0)
    { //$array_unique=0时不去掉重复,否则去掉
        $idarray = self::multi_one($data,$key);
        $idarray = $array_unique ? array_unique($idarray) : $idarray;
        return self::_array_sql($idarray);
    }
    /**
     * 从多维中数组根据的key返回有逻辑关系的sql
     */
    public static function multi_sqls($data, $keya, $keyb, $rekeya = false, $rekeyb = false)
    { //data 为数组 keya为key1 keyb为key2，对应rekeya替代sql里面的keya
        if(! is_array($data))
            return null;
        $count = count($data);
        $keyadot = $rekeya ? $rekeya : $keya;
        $keybdot = $rekeyb ? $rekeyb : $keyb;
        $sql = "";
        for($i = 0; $i < $count; $i ++)
        {
            $sql .= " (`" . $keyadot . "`='" . $data[$i][$keya] . "' ";
            $sql .= "AND";
            $sql .= " `" . $keybdot . "`='" . $data[$i][$keyb] . "') ";
            if($i < $count - 1)
            {
                $sql .= "OR";
            }
        }
        return $sql;
    }
    public static function array2object($array)
    {
        if(is_array($array))
        {
            $obj = new StdClass();
            foreach($array as $key => $val)
            {
                $obj->$key = $val;
            }
        }
        else
        {
            $obj = $array;
        }
        return $obj;
    }
    public static function object2array($object)
    {
        if(is_object($object))
        {
            foreach($object as $key => $value)
            {
                $array[$key] = is_object($value) ? self::object2array($value) : $value;
            }
        }
        else
        {
            $array = $object;
        }
        return $array;
    }
    /**
     * 数组转成单纯一维数组
     * $array 为源数组
     * $leveal 为数组维数
     * $dot 为合并时候的连接符
     */
    public static function array2string($array, $leveal = 2, $dot = '')
    {
        if(! is_array($array))
        {
            return $array;
        }
        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                $ret[$key] = $leveal > 1 ? self::array2string($value,$leveal - 1,$dot) : implode($dot,$value);
            }
            else
            {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }
    /**
     * 数组合并，不全为数组返回false 
     */
    public static function merge($arraya, $arrayb)
    {
        if(! is_array($arraya) || ! is_array($arrayb))
        {
            return false;
        }
        return array_merge($arraya,$arrayb);
    }
    /**
     * JSON格式转为array格式
     */
    public static function json2array($json)
    {
        $json_org = $json;
        if(get_magic_quotes_gpc())
        {
            $json = stripslashes($json);
        }
        $json = substr($json,1,- 1);
        $json = str_replace(array(
            ":", 
            "{", 
            "[", 
            "}", 
            "]"
        ),array(
            "=>", 
            "array(", 
            "array(", 
            ")", 
            ")"
        ),$json);
        $json_array = array();
        @eval("\$json_array = array({$json});");
        if(! $json_array)
        { //转化失败
            $json_string = json_decode($json_org);
            if(is_object($json_string))
            {
                $json_array = self::object2array($json_string);
            }
            else
            {
                $json_array = $json_string;
            }
        }
        return $json_array;
    }
    /**
     * 将数组写入文件
     */
    public static function arrayeval($array, $level = 0)
    {
        if(is_object($array))
        {
            $array = self::object2array($array);
        }
        if(! is_array($array))
        {
            return "'" . $array . "'";
        }
        if(is_array($array) && function_exists('var_export'))
        {
            return var_export($array,true);
        }
        $space = '';
        for($i = 0; $i <= $level; $i ++)
        {
            $space .= "\t";
        }
        $evaluate = "Array\n$space(\n";
        $comma = $space;
        if(is_object($array))
        {
            $array = self::object2array($array);
        }
        if(is_array($array))
        {
            foreach($array as $key => $val)
            {
                if(is_object($val))
                {
                    $val = self::object2array($val);
                }
                $key = is_string($key) ? '\'' . addcslashes($key,'\'\\') . '\'' : $key;
                $val = ! is_array($val) && (! preg_match("/^\-?[1-9]\d*$/",$val) || strlen($val) > 12) ? '\'' . addcslashes($val,'\'\\') . '\'' : $val;
                if(is_array($val))
                {
                    $evaluate .= "$comma$key => " . self::arrayeval($val,$level + 1);
                }
                else
                {
                    $evaluate .= "$comma$key => $val";
                }
                $comma = ",\n$space";
            }
        }
        $evaluate .= "\n$space)";
        return $evaluate;
    }
    /**
     * 两个数组通过相同的key来排序，第一个数组为最后排序输出数组
     */
    public static function multi_order($array_out, $array_old, $tokey)
    {
        $order_data_keys = self::multi_array($array_out,$tokey); //通过key获得key对应的数组 
        foreach($array_old as $key => $value)
        {
            if(is_object($value))
            {
                $value = self::object2array($value);
            }
            if(isset($order_data_keys[$value[$tokey]]))
            {
                $ret[] = $order_data_keys[$value[$tokey]];
            }
        }
        return $ret;
    }

}
?>