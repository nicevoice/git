<?php

//增加万能的的额外参数解析,以@开头,通过下划线划分，第一个为case，其他为参数
function sptag($str) {
    if (!strpos($str, '@') === 0 || !strpos($str, '_')) {
        return $str;
    }
    $strs = explode("_", $str);
    $case = ltrim($strs[0], '@');
    static $_categoryurl;
    static $_categorys;
    static $_view;
    static $_page;
    static $_free;
    static $_categoryen;
    static $_categorycss;
    static $_product;
    static $_video;
    switch ($case) {
        case "caturl":
            $id = intval($strs[1]);
            if (!isset($_categoryurl[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT `url` FROM `#table_category` WHERE `catid`=?", array($id));
                $_categoryurl[$id] = $r ? $r['url'] : false;
            }
            return $_categoryurl[$id];
            break;
        case "caten":
            $id = intval($strs[1]);
            if (!isset($_categoryen[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT `keywords` FROM `#table_category` WHERE `catid`=?", array($id));
                $keywords = $r['keywords'];
                if (strpos($keywords, '@') === flase) {
                    $_categoryen[$id] = false;
                } else {
                    $keywords = explode("@", $keywords);
                    $_categoryen[$id] = $keywords[1];
                }
            }
            return $_categoryen[$id];
            break;
        case "catcss":
            $id = intval($strs[1]);
            if (!isset($_categorycss[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT `keywords` FROM `#table_category` WHERE `catid`=?", array($id));
                $keywords = $r['keywords'];
                if (strpos($keywords, '@') === flase) {
                    $_categorycss[$id] = false;
                } else {
                    $keywords = explode("@", $keywords);
                    $_categorycss[$id] = $keywords[2];
                }
            }
            return $_categorycss[$id];
            break;
        case "cat":
            $id = intval($strs[1]);
            if (!isset($_categorys[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT * FROM `#table_category` WHERE `catid`=?", array($id));
                $keywords = $r['keywords'];
                if (strpos($keywords, '@') === flase) {
                    $r['caten'] = '';
                } else {
                    $keywords = explode("@", $keywords);
                    $r['caten'] = $keywords[1];
                }
                $_categorys[$id] = $r ? $r : false;
            }
            return $_categorys[$id];
            break;
        case "view":
            $id = intval($strs[1]);
            if (!isset($_view[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT `url` FROM `#table_content` WHERE `contentid`=?", array($id));
                $_view[$id] = $r ? $r['url'] : $str;
            }
            return $_view[$id];
            break;
        case "video":
            $id = intval($strs[1]);
            if (!isset($_video[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT * FROM `#table_video` WHERE `contentid`=?", array($id));
                if (!$r) {
                    return array();
                }
                $aid = str_replace(array('[ctvideo]', '[/ctvideo]'), array(), $r['video']);
                if (!is_numeric($aid)) {
                    return array();
                }
                $vmodel = loader::model('attachment', 'video');
                $_video[$id] = $vmodel->getone($aid);
            }
            return $_video[$id];
            break;
        case "product":
            $id = intval($strs[1]);
            if (!isset($_product[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT * FROM `#table_product` p,`#table_content` c WHERE p.contentid=c.contentid AND p.contentid=?", array($id));
                $_product[$id] = $r;
            }
            return $_product[$id];
            break;
        case "page":
            $id = intval($strs[1]);
            if (!isset($_page[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT `url` FROM `#table_page` WHERE `pageid`=?", array($id));
                $_page[$id] = $r ? $r['url'] : $str;
            }
            return $_page[$id];
            break;
        case "pageinfo":
            $id = intval($strs[1]);
            if (!isset($_page[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT * FROM `#table_page` WHERE `pageid`=?", array($id));
                $_page[$id] = $r ? $r : $str;
            }
            return $_page[$id];
            break;
        case "free":
            $id = intval($strs[1]);
            if (!isset($_free[$id])) {
                $db = & factory::db();
                $r = $db->get("SELECT `path` FROM `#table_freelist` WHERE `flid`=?", array($id));
                $_free[$id] = $r ? str_replace("{PSN:1}/", WWW_URL, $r['path']) : $str;
            }
            return $_free[$id];
            break;
        case "UI":
            $pre = IMG_URL . 'templates/' . config('template', 'name') . '/ui';
            unset($strs[0]);
            return $pre . implode("_", $strs);
            break;
        default:
        case "orig": //找原图
            $strp = implode("_", $strs);
            $strp_url = substr($strp, 6);
            $pathinfo = pathinfo($strp_url);
            if (strpos($pathinfo['filename'], '_') > 0) {
                $filenames = explode("_", $pathinfo['filename']);
                $newurl = $pathinfo['dirname'] . "/orig_" . $filenames[count($filenames) - 1] . "." . $pathinfo['extension'];
            } else {
                $newurl = $pathinfo['dirname'] . "/orig_" . $pathinfo['filename'] . "." . $pathinfo['extension'];
                if (!strpos($newurl, 'http') === 0) {
                    if (!file_exists(UPLOAD_PATH . $newurl)) {
                        $newurl = str_replace("orig_", "", $newurl);
                    }
                }
            }
            return $newurl;
            break;
        default:
            return $str;
            break;
    }
}

function same_subcatids($catid, $level = 1, $isid = false) {
    static $_channel;
    static $_subcatids;
    if (!$_channel) {
        $_channel = channel();
    }
    if (!isset($_subcatids[$catid])) {
        $_channel_ids = array_keys($_channel);
        $catinfo = category($catid);
        if (in_array($catid, $_channel) || is_null($catinfo['parentids']) || $catinfo['parentid'] == $catid) {
            $parentid = $catinfo['parentid'];
        } else {
            $pcatids = explode(",", $catinfo['parentids']);
            $count = max(count($pcatids) - $level, 0);
            $parentid = $pcatids[$count];
        }
        $_catinfo = category($parentid);
        $_subcatids[$catid] = $_catinfo;
    }
    if ($isid) {
        return $_subcatids[$catid]['catid'];
    } else {
        $db = & factory::db();
        $r = $db->select("SELECT `catid` FROM `#table_category` WHERE `parentid`=? ORDER BY sort ASC", array($_subcatids[$catid][catid]));
        $ret = array();
        if (is_array($r)) {
            foreach ($r as $_r) {
                $ret[] = $_r['catid'];
            }
        }
//        return explode(',',$_subcatids[$catid]['childids']);
        return $ret;
    }
}

/* * 通过catid获取类别信息
 * @param $catid
 * @return array
 */

function category($catid) {
    $cat = table('category', $catid);
    return $cat;
}

function category_content_count($catid) {
    $db = & factory::db();
    $cate = table('category', $catid);
    if ($cate['childids']) {
        $r = $db->get("SELECT count(*) as count FROM `#table_content` WHERE `catid` IN ({$cate['childids']})");
    } else {
        $r = $db->get("SELECT count(*) as count FROM `#table_content` WHERE `catid`=?", array($catid));
    }
    return $r['count'];
}

/**
 * 通过分发点获取路径信息
 * @param $psn 可以是数字，也可以是{PSN:1}格式
 * @return bool|string
 */
function psn2path($psn) {
    if (is_numeric($psn)) {
        $psnid = $psn;
    } else {
        if (!preg_match('|^{psn:(\d+)}(.*)$|i', $psn, $m)) {
            return false;
        }
        $psnid = $m[1];
    }
    if (!($pos = table('psn', $psnid))) {
        return false;
    }

    $path = array(
        rtrim(WWW_PATH, '/'),
        rtrim(str_replace('\\', '/', $pos['path']), '/')
    );
    return implode('/', $path);
}

/**
 * 分页函数
 *
 * @param int $total 总条目
 * @param int $page 当前页码
 * @param int $pagesize 每页条数
 * @param int $offset 页码显示数量控制（n*2+1）
 * @param string $url 基础URL
 * @param bool $mode 是否转义
 * @return string
 */
function pages_simple($total, $page = 1, $pagesize = 20, $offset = 2, $url = null, $mode = false) {
    if ($total <= $pagesize)
        return '';
    $page = max(intval($page), 1);
    $pages = ceil($total / $pagesize);
    $page = min($pages, $page);
    $prepage = max($page - 1, 1);
    $nextpage = min($page + 1, $pages);
    $from = max($page - $offset, 2);
    if ($pages - $page - $offset < 1)
        $from = max($pages - $offset * 2 - 1, 2);
    $to = min($page + $offset, $pages - 1);
    if ($page - $offset < 2)
        $to = min($offset * 2 + 2, $pages - 1);
    $more = 1;
    if ($pages <= ($offset * 2 + 5)) {
        $from = 2;
        $to = $pages - 1;
        $more = 0;
    }
    $str = '';
    $_ENV['page_url_pre'] = pages_url($url, $prepage, $mode);
    if ($offset >= 2)
        $str .= '<a href="' . pages_url($url, $prepage, $mode) . '">上一页</a>';
    $str .= $page == 1 ? '<a href="' . pages_url($url, 1, $mode) . '" class="hover">1</a>' : '<a href="' . pages_url($url, 1, $mode) . '">1' . ($from > 2 && $more ? '...' : '') . '</a>';
    if ($to >= $from) {
        for ($i = $from; $i <= $to; $i++) {
            $str .= $i == $page ? '<a href="' . pages_url($url, $i, $mode) . '" class="hover">' . $i . '</a>' : '<a href="' . pages_url($url, $i, $mode) . '">' . $i . '</a>';
        }
    }
    $str .= $page == $pages ? '<a href="' . pages_url($url, $pages, $mode) . '" class="hover">' . $pages . '</a>' : '<a href="' . pages_url($url, $pages, $mode) . '">' . ($to < $pages - 1 && $more ? '...' : '') . $pages . '</a>';
    $_ENV['page_url_next'] = pages_url($url, $nextpage, $mode);
    if ($offset >= 2)
        $str .= '<a href="' . pages_url($url, $nextpage, $mode) . '">下一页</a>';
    return $str;
}

/**
 * 分页函数最简化
 *
 * @param int $total 总条目
 * @param int $page 当前页码
 * @param int $pagesize 每页条数
 * @param int $offset 页码显示数量控制（n*2+1）
 * @param string $url 基础URL
 * @param bool $mode 是否转义
 * @return string
 */
function pages_mini($total, $page = 1, $pagesize = 20, $offset = 2, $url = null, $mode = false) {
    if ($total <= $pagesize)
        return '';
    $page = max(intval($page), 1);
    $pages = ceil($total / $pagesize);
    $page = min($pages, $page);
    $prepage = max($page - 1, 1);
    $nextpage = min($page + 1, $pages);
    $from = max($page - $offset, 2);
    if ($pages - $page - $offset < 1)
        $from = max($pages - $offset * 2 - 1, 2);
    $to = min($page + $offset, $pages - 1);
    if ($page - $offset < 2)
        $to = min($offset * 2 + 2, $pages - 1);
    $more = 1;
    if ($pages <= ($offset * 2 + 5)) {
        $from = 2;
        $to = $pages - 1;
        $more = 0;
    }
    $str = '';
    $_ENV['page_url_pre'] = pages_url($url, $prepage, $mode);
    $_ENV['page_url_next'] = pages_url($url, $nextpage, $mode);
    if ($offset < 2) {
        $prev_nomore = $page <= 1;
        if ($prev_nomore) {
            $str .= '<a class="prev prev_nomore">上一页</a>';
        } else {
            $str .= '<a class="prev" href="' . pages_url($url, $prepage, $mode) . '">上一页</a>';
        }
        $next_nomore = $page >= $pages;
        if ($next_nomore) {
            $str .= '<a  class="next next_nomore">下一页</a>';
        } else {
            $str .= '<a  class="next" href="' . pages_url($url, $nextpage, $mode) . '">下一页</a>';
        }
    } else {
        $str = '';
        $prev_nomore = $page <= 1;
        if ($prev_nomore) {
            $str .= '<a class="prev prev_nomore">上一页</a>';
        } else {
            $str .= '<a class="prev" href="' . pages_url($url, $prepage, $mode) . '">上一页</a>';
        }
        $str .= $page == 1 ? '<a href="' . pages_url($url, 1, $mode) . '" class="now">1</a>' : '<a class="normal" href="' . pages_url($url, 1, $mode) . '">1' . ($from > 2 && $more ? '...' : '') . '</a></li>';
        if ($to >= $from) {
            for ($i = $from; $i <= $to; $i++) {
                $str .= $i == $page ? '<a href="' . pages_url($url, $i, $mode) . '" class="now">' . $i . '</a>' : '<a class="normal" href="' . pages_url($url, $i, $mode) . '">' . $i . '</a>';
            }
        }
        $str .= $page == $pages ? '<a href="' . pages_url($url, $pages, $mode) . '" class="now">' . $pages . '</a>' : '<a class="normal" href="' . pages_url($url, $pages, $mode) . '">' . ($to < $pages - 1 && $more ? '...' : '') . $pages . '</a>';
        $next_nomore = $page >= $pages;
        if ($next_nomore) {
            $str .= '<a  class="next next_nomore">下一页</a>';
        } else {
            $str .= '<a  class="next" href="' . pages_url($url, $nextpage, $mode) . '">下一页</a>';
        }
    }

    return $str;
}

function fix_atturl($url, $isorig = false) {
    if (strpos($url, 'http://') === 0) {
        if (!$isorig)
            return $url;
        return sptag('@orig_' . $url);
    }else {
        if (strpos($url, '@UI') === 0) {
            return sptag($url);
        }
        if (!$isorig) {
            return rtrim(UPLOAD_URL, "/") . "/" . ltrim($url, "/");
        } else {
            return sptag('@orig_' . rtrim(UPLOAD_URL, "/") . "/" . ltrim($url, "/"));
        }
    }
}

function fix_content($content) {
    return str_replace(array("<div ", "</div>"), array("<p ", "</p>"), $content);
}

function murl($type, $id = '', $page = '') {
    switch ($type) {
        case "c":
            if (!$page)
                return M_URL . 'c' . $id;
            return M_URL . 'c' . $id . '_' . $page;
            break;
        case "p":
            if (!$page)
                return M_URL . 'p' . $id;
            return M_URL . 'p' . $id . '_' . $page;
            break;
        case "page":
            if (!$page)
                return M_URL . 'page' . $id;
            return M_URL . 'page' . $id . '_' . $page;
            break;
        case "v":
            if (!$page)
                return M_URL . 'v' . $id;
            return M_URL . 'v' . $id . '_' . $page;
            break;
        case "u":
            if (!$page)
                return M_URL . 'v' . $id;
            return M_URL . 'v' . $id . '_' . $page;
            break;
        case "so":
            return M_URL . 'so.html';
            break;
    }
}

function object2array($object) {
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = is_object($value) ? object2array($value) : $value;
        }
    } else {
        $array = $object;
    }
    return $array;
}

function get_setion($sectionid, $dataonly = true) {
    $section = loader::model('admin/section', 'page');
    $ret = $section->get($sectionid);
    $ret['data'] = json_decode($ret['data']) ? json_decode($ret['data']) : $ret['data'];
    if (is_object($ret['data'])) {
        $ret['data'] = object2array($ret['data']);
    }

    if ($dataonly) {
        return $ret['data'] ? $ret['data'] : array();
    }
    return $ret;
}

//dump(msptag('@view_123'));
//针对手机版本的万能标签
function msptag($str) {
    if (!strpos($str, '@') === 0 || !strpos($str, '_')) {
        return $str;
    }
    $strs = explode("_", $str);
    $case = ltrim($strs[0], '@');
    static $_categoryurl;
    static $_categorys;
    static $_view;
    static $_page;
    static $_free;
    static $_categoryen;
    static $_categorycss;
    static $_product;
    static $_video;
    switch ($case) {
        case "caturl":
            $id = intval($strs[1]);
            return murl('c', $id);
            break;
        case "view":
            $id = intval($strs[1]);
            $id = intval($strs[1]);
            return murl('v', $id);
            break;
        case "page":
            $id = intval($strs[1]);
            $id = intval($strs[1]);
            return murl('page', $id);
            break;
        default:
            return sptag($str);
            break;
    }
}

function nowimtab($catid = '') {
    if ($_ENV[nowim]) {
        return $_ENV[nowim];
    }
    if (!is_numeric($catid)) {
        $_ENV[nowim] = $catid;
        return $_ENV[nowim];
    }
    $cate = category($catid);
    if (!$cate || !$cate['parentids']) {//首页
        $_ENV[nowim] = null;
        return $_ENV[nowim];
    }
    $pid = $cate['parentids'];
    if (!is_numeric($pid)) {
        $pids = explode(',', $pid);
        $pid = $pids[1];
    }
    switch ($pid) {
        case 1:
            $_ENV[nowim] = 'zjwxj'; //走进无限极
            break;
        case 12:
            $_ENV[nowim] = 'rdjj'; //热点聚焦
            break;
        case 18:
            $_ENV[nowim] = 'jkcp'; //健康产品
            break;
        case 25:
            $_ENV[nowim] = 'jkrs'; //健康人生
            break;
        case 52:
            $_ENV[nowim] = 'shzr'; //社会责任
            break;
        case 431:
            $_ENV[nowim] = 'fwdt'; //社会责任
            break;
        default:
            $_ENV[nowim] = null; //走进无限极
            break;
    }
    return $_ENV[nowim];
}

function get_video($contentid, $key = '') {
    return get_video_vid(contentid2vid($contentid), $key);
}

function contentid2vid($contentid) {
    $db = & factory::db();
    $r = $db->get("SELECT * FROM `#table_video` WHERE `contentid`=?", array($contentid));
//    dump($r);
    if (preg_match('/^(\[ctvideo\])([^\[]+)(\[\/ctvideo\])$/i', $r['video'], $matches)) {
        $r['video'] = $matches[2];
    }
    return $r['video'];
}

function get_video_vid($vid, $key = '') {
    $attachment = loader::model('attachment', 'video');
    $data = $attachment->getone($vid);
    if (!$data) {
        return false;
    }
    if (!$key) {
        return $data;
    }
    return $data[$key];
}

/**
 * 以指定key的value作为多维数组的key, $is_kv:true 返回仅包含key的value数组
 */
function array_as_key($array, $key, $is_kv = false) {
    $data = array();
    foreach ($array as $v) {
        if ($is_kv)
            $data[] = $v[$key];
        else
            $data[$v[$key]] = $v;
    }
    return $data;
}

/**
 * 获取IP对应IP库信息
 */
function convert_ip($ip, $ipdatafile) {
    if (!$fd = @fopen($ipdatafile, 'rb'))
        return false;

    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

    if (!($DataBegin = fread($fd, 4)) || !($DataEnd = fread($fd, 4)))
        return;
    @$ipbegin = implode('', unpack('L', $DataBegin));
    if ($ipbegin < 0)
        $ipbegin += pow(2, 32);
    @$ipend = implode('', unpack('L', $DataEnd));
    if ($ipend < 0)
        $ipend += pow(2, 32);
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

    $BeginNum = $ip2num = $ip1num = 0;
    $ipAddr1 = $ipAddr2 = '';
    $EndNum = $ipAllNum;

    while ($ip1num > $ipNum || $ip2num < $ipNum) {
        $Middle = intval(($EndNum + $BeginNum) / 2);

        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if (strlen($ipData1) < 4) {
            fclose($fd);
            return false;
        }
        $ip1num = implode('', unpack('L', $ipData1));
        if ($ip1num < 0)
            $ip1num += pow(2, 32);

        if ($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }

        $DataSeek = fread($fd, 3);
        if (strlen($DataSeek) < 3) {
            fclose($fd);
            return false;
        }
        $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if (strlen($ipData2) < 4) {
            fclose($fd);
            return false;
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if ($ip2num < 0)
            $ip2num += pow(2, 32);

        if ($ip2num < $ipNum) {
            if ($Middle == $BeginNum) {
                fclose($fd);
                return false;
            }
            $BeginNum = $Middle;
        }
    }

    $ipFlag = fread($fd, 1);
    if ($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if (strlen($ipSeek) < 3) {
            fclose($fd);
            return false;
        }
        $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }

    if ($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if (strlen($AddrSeek) < 3) {
            fclose($fd);
            return false;
        }
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return false;
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }

        while (($char = fread($fd, 1)) != chr(0))
            $ipAddr2 .= $char;

        $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
        fseek($fd, $AddrSeek);

        while (($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;
    } else {
        fseek($fd, -1, SEEK_CUR);
        while (($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;

        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return false;
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while (($char = fread($fd, 1)) != chr(0))
            $ipAddr2 .= $char;
    }
    fclose($fd);

    if (preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }
    $ipaddr = "$ipAddr1 $ipAddr2";
    $ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
    $ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
    if (preg_match('/http/i', $ipaddr) || $ipaddr == '')
        $ipaddr = false;

    return iconv('gb2312', 'utf-8', $ipaddr);
}

/**
 * IP地址对应的城市信息
 * @param type $ip
 */
function ip_city($ip = null) {
    $ip = is_null($ip) ? request::get_clientip() : $ip;
    $address = convert_ip($ip, KUAIJI_RESOURCES . 'ipdata/qqwry.dat');
    $city = common_data('area_city', 'system');
    $data = $city[440100];
    if (!$address)
        return $data;
    foreach ($city as $v) {
        if (strpos($address, $v['title']) !== false) {
            $data = $v;
            break;
        }
    }
    return $data;
}

/**
 * 获取/设置客户端城市信息
 * @param type $area_id
 */
function client_city($area_id = 0) {
    static $Cookie;
    if (!$Cookie) {
        import('core.cookie');
        $Cookie = new cookie('KJ_', '/', '.kuaiji.com');
    }
    if ($area_id) {
        $city = common_data('area_city', 'system');
        $data = isset($city[$area_id]) ? $city[$area_id] : array();
    } else {
        $data = $Cookie->get('city');
        $data = $data ? decodeData(str_decode($data, 'city')) : '';
        if (is_array($data) && $data) {
            if (!$Cookie->get('city_str')) {
                $Cookie->set('city_str', implode('|', array($data['id'], $data['alias'], $data['pinyin'])), 2592000);
                $Cookie->set('city_id', $data['id'], 2592000);
                $Cookie->set('city_py', $data['pinyin'], 2592000);
            }
            return $data;
        } else {
            $data = ip_city();
        }
    }
    $Cookie->set('city', str_encode(encodeData($data), 'city'), 2592000);
    $Cookie->set('city_str', implode('|', array($data['id'], $data['alias'], $data['pinyin'])), 2592000);
    $Cookie->set('city_id', $data['id'], 2592000);
    $Cookie->set('city_py', $data['pinyin'], 2592000);
    return $data;
}

/**
 *  获取客户端ip 及 地区id , 不依赖cookie
 * @return array 若不能识别地区 , 则只有ip项
Array
(
[ip] => 211.140.160.0
[id] => 330200
[title] => 宁波市
[alias] => 宁波
[pinyin] => ningbo
[province_id] => 330000
[province_name] => 浙江
)
 */
function client_area() {
    $ip = request::get_clientip();
    $address = convert_ip($ip, KUAIJI_RESOURCES . 'ipdata/qqwry.dat');
    $city = common_data('area_city', 'system');
    $data = array('ip' => $ip);
    if ($address) {
        foreach ($city as $v) {
            if (strpos($address, $v['title']) !== false) {
                $data = array_merge($data, $v);
                break;
            }
        }
    }
    return $data;
}

/**
 * 验证码
 * @param $check 是否判断验证码
 */
function seccode($check = false) {
    import('helper.seccode');
    $seccode = new seccode();
    if ($check)
        return $seccode->valid(false);
    $seccode->image();
}

/**
 * 会计网链接构造
 * @param $ca
 * @param $params
 * @param $mp
 */
function kuaiji_url($ca, $params = null, $ma = '') {
    static $objects;
    $router = 'kuaiji';
    $router_key = 'router_' . $router;
    if (!isset($objects[$router_key])) {
        !defined('KUAIJI_MODULE') ? define('KUAIJI_MODULE', 'brand') : null;
        $objects[$router_key] = loader::lib('router_kuaiji', 'system');
    }

    return $objects[$router_key]->url($ca, $params, $ma);
}

/**
 * 加载 UC Client 接口函数库
 */
function load_uc() {

    load_open();
}

/**
 * 加载 UC Client 接口函数库
 */
function load_open() {
    config('ucenter');
    loader::import('ucenter.open', ROOT_PATH . 'extension/');
}
/**
 * 获取接口实例
 */
function load_rpc($controller, $action = 'all') {
    include_once(CMSTOP_PATH . 'api/rpc/ApiApp.php');
    $Client = ApiApp::Client($controller, $action);
    return $Client;
}

/**
 * 写入公用配置缓存
 * @param $filename   文件名
 * @param $array      数据数组
 * @param $dir        文件目录
 */
function common_data_set($filename, $array, $dir = 'system') {
    if (!$dir)
        $dir = 'system';
    $file = $dir . '/cache_' . $filename . '.php';
    return cache_write($file, $array, CACHE_COMMON_PATH);

    if (!$dir) {
        $dir = 'system';
    }
    if( BASE_DOMAIN == '.kuaiji.com' || BASE_DOMAIN == '.p.kuaiji.com' ){
        $key = "cache_{$filename}_{$dir}" ;
        $cache_instance = redis('data');//解决cache被其他动作清除的问题

        ///////////
        if(!is_array($array)) return false;
        $cache = $array;
        ///////////
        $cache_instance->set($key, $cache);
    }
    $file = $dir . '/cache_' . $filename . '.php';
    return cache_write($file, $array, CACHE_COMMON_PATH);
}

/**
 * 读取公用配置缓存
 * @param  $filename 文件名
 * @param  $dir      文件目录
 */
function common_data($filename, $dir = 'system') {
    if (!$dir)
        $dir = 'system';
    $file = $dir . '/cache_' . $filename . '.php';
    return cache_read($file, CACHE_COMMON_PATH, true);

    if (!$dir) {
        $dir = 'system';
    }
    if( BASE_DOMAIN == '.kuaiji.com' || BASE_DOMAIN == '.p.kuaiji.com' )
    {
        $key = "cache_{$filename}_{$dir}" ;
        $cache_instance = redis('data');//解决cache被其他动作清除的问题
        $cache = $cache_instance->get($key);
        if( empty($cache) ){
            $file = $dir . '/cache_' . $filename . '.php';
            $cache = cache_read($file, CACHE_COMMON_PATH, true);
            if($cache){
                $cache_instance->set($key, $cache);
            }
        }
        return $cache;
    }
    else
    {
        $file = $dir . '/cache_' . $filename . '.php';
        return cache_read($file, CACHE_COMMON_PATH, true);
    }
}

/**
 * 获取通知模板信息
 */
function message_template($id, $argv = array()) {
    $data = common_data('message/' . $id, 'notify');
    if ($argv) {
        foreach ($argv as $k => $v) {
            $search[] = '{' . $k . '}';
            $replace[] = $v;
        }
        foreach ($data as &$v) {
            if (!empty($v['title']))
                $v['title'] = str_replace($search, $replace, $v['title']);
            if (!empty($v['content']))
                $v['content'] = str_replace($search, $replace, $v['content']);
        }
    }
    return $data;
}

function login_member() {
    $x = online();
    if (!$x)return array();
    return array('user_id'=>$x['userid'], 'name'=>$x['username'], 'auth'=>$x['auth']);
    load_uc();
    $user_id = value($_COOKIE, 'KJ_uid', 0);
    $name = value($_COOKIE, 'KJ_username', '');
    $auth = value($_COOKIE, 'KJ_auth', '');
    $decode = explode("\t", uc_authcode($auth, 'DECODE', LOGIN_KEY));
    if (empty($decode) || count($decode) != 2)
        return array();
    list(, $member_id) = $decode;
    if ($member_id != $user_id)
        return array();
    return compact('user_id', 'name', 'auth');
}

/**
 *  搜索
 * @param string $functon
 * @param $so_param
 * @param int $cache_time
 * @return mixed
 */
function so($functon = 'query', $so_param, $cache_time = 0) {

//    $client = load_rpc('so');    return call_user_func_array(array($client, $functon), $so_param);
    if( in_array($functon, array('delete', 'query', 'analysis', 'multi_func', 'analysis_and_query')) ){//直接访问solr
        $solr_config = config('solr');
        loader::import('solr.soController', ROOT_PATH . 'extension/');
        $soController = new soController($solr_config);
        return call_user_func_array( array($soController, $functon), $so_param );
    }else{//通过api访问solr
        $client = load_rpc('so');
        return call_user_func_array(array($client, $functon), $so_param);
    }
}


/**
 *
 *  利用搜索来取关联属性数据
 * @param $catid
 * @param $typeid
 * @param string $order
 * @param int $offset
 * @param int $size
 * @return array
 */
function getnewsbyso($catid, $typeid, $order = "pv desc, add_time desc", $offset = 0, $size = 10, $thumb = false)
{
    include_once app_dir('brand/_common').'baseHelper.php';

    /**
     * 法规   乱七八糟的设定， 主逻辑还是@李英超  qq:18925145189
     *
     */
    if( $catid == 17500 ){
        $property = array(
            '810'=>array(
                'proid'=>810,
                'name'=>'中央税收法规',
                'child_proid'=>range(811, 842),
            ),
            '850'=>array(
                'proid'=>850,
                'name'=>'中央财政法规',
                'child_proid'=>range(851, 856),
            ),
            '860'=>array(
                'proid'=>860,
                'name'=>'财务法规',
                'child_proid'=>range(861, 863),
            ),
            '870'=>array(
                'proid'=>870,
                'name'=>'金融法规',
                'child_proid'=>range(871, 875),
            ),
            '880'=>array(
                'proid'=>880,
                'name'=>'审计法规',
                'child_proid'=>range(881, 883),
            ),
            '890'=>array(
                'proid'=>890,
                'name'=>'会计法规',
                'child_proid'=>range(891, 897),
            ),
            '900'=>array(
                'proid'=>900,
                'name'=>'更多法规',
                'child_proid'=>range(901, 914),
            ),
        );
    }

    //过滤条件
    $additionalParameters['fq'] = '';
    $additionalParameters['fq'] .= " catid:{$catid} &&"; //栏目id
    if( $typeid ){ //栏目扩展id
        if( $catid == 17500 && in_array($typeid, array_keys($property))){

            foreach($property[$typeid]['child_proid'] as $cid){
                $fq_typeid_arr[] = 'typeid:'.($cid);
            }

            $additionalParameters['fq'] .= ' ('.implode(' || ', $fq_typeid_arr) . ') &&';
        }elseif( ($catid ==16000 || $catid == 17000) && $typeid%10 == 0 ){
            $toptypeid = substr($typeid, 0, -1)*10;
            for($i=0 ; $i<=9 ; $i++){
                $fq_typeid_arr[] = 'typeid:'.($toptypeid+$i);
            }
            $additionalParameters['fq'] .= ' ('.implode(' || ', $fq_typeid_arr) . ') &&';
        }else{
            $additionalParameters['fq'] .= " typeid:{$typeid} &&";
        }
    }
    $additionalParameters['fq'] = trim($additionalParameters['fq'], '&&');
    $thumb && $additionalParameters['fq'] .= " && thumb:['' TO *]" ;
    //排序
    $additionalParameters['sort'] = $order;
    $core = $catid == 17500 ? 'cms_fagui' : 'cms_news' ;
    $so_param = array($core, '', $offset, $size, $additionalParameters);
    $so_result = so('query', $so_param);
    $list = array();
    if ($so_result['responseHeader']['status'] == 0) {
        foreach ($so_result['response']['docs'] as $v) {
            $doc = array(
                'contentid' => $v['id'],
                'title' => $v['title'],
                'url'=> $v['url'],
                'thumb' => $v['thumb'],
                'author' => $v['editor'],
                'description' => $v['description'],
                'pv' => $v['pv'],
                'add_time'=>$v['add_time'],
            );

            if( $catid == 15000 ){
                //简介
                $doc['content'] = trim(str_replace('&nbsp;',' ',$v['description']?$v['description']:$v['content'])) ;
                $doc['content'] = mb_substr($doc['content'], 0, 120, 'utf-8') . ( mb_strlen($doc['content'], 'utf-8')>120 ? '...' : '' );
            }else{
                $proid = end($v['typeid']);
                if( $proid ){
                    $property_cache = baseHelper::property_get_cache(0);
                    $pro_data = $property_cache[$proid];
                    $doc['cat_name'] = $pro_data['name'];
                }else{
                    empty($cate_cache) && $cate_cache = baseHelper::category_get_cache(10);
                    $doc['cat_name'] = $cate_cache[$catid]['category_title'];

                }
            }
            $list[] = $doc;
        }
    }
    return $list;
}
/**
 * 根据原图url, 返回缩放后图片url
 * @param $orginal_url 原图url
 * @param $type 1:课程 2:资料 3:试听课 4:活动 5:校区
新机构平台-- 21:课程 22:资料 31:学校logo 32:营业执照 33:首页幻灯片 34:校区环境
 * @param $size_type  图片尺寸 l:大图 m:中图 s:小图
 * @param bool $return_default 图片不存在时,是否返回默认图片
 * @return mixed|string
 */
function get_pic_url($orginal_url, $type, $size_type, $return_default = true) {
    $width = $height = null;
    if ($size_type) {
        $config = config('image', $type);
        $width = $config[$size_type][0];
        $height = $config[$size_type][1];
    }
    $size_str = $width && $height ? "_{$width}x{$height}" : '';

    if ($orginal_url) {
        $pos = strrpos($orginal_url, '.');
        $replace = ($width && $height) ? "_{$width}x{$height}." : ''.'';
        $pic_url = substr_replace($orginal_url, $replace, $pos, 1);

        $file_path = str_replace(PIC_URL, PIC_PATH, $pic_url);
        if (file_exists($file_path)) {
            return $pic_url . '?t='.filemtime($file_path);
        }
    }

    if ($return_default) {
        $file_path = PUBLIC_PATH . 'img/images/noimage' . $size_str . '.gif' ;
        if (file_exists($file_path)) {
            return IMG_URL . 'images/noimage' . $size_str . '.gif'  . '?t='.filemtime($file_path);
        }
    }

    return '';
}

/**
 * 根据产品类型, 产品id 返回指定尺寸主图url 不存在则返回默认图
 * @param int $type 1:课程 2:资料 3:试听课 4:活动 5:校区
 *                     新机构平台-- 21:课程 22:资料 31:学校logo 32:营业执照 33:首页幻灯片 34:校区环境 35:正方形学校logo
 * @param int $id
 * @param string $size_type 图片尺寸 l:大图 m:中图 s:小图
 * @param bool $return_default 图片不存在时,是否返回默认图片
 * @return string
 */
function get_pic_by_id($type, $id, $size_type, $return_default = true) {
    $width = $height = null;
    if ($size_type) {
        $config = config('image', $type);
        $width = $config[$size_type][0];
        $height = $config[$size_type][1];
    }
    $size_str = $width && $height ? "_{$width}x{$height}" : '';
    $type = str_pad($type, 2, '0', STR_PAD_LEFT);
    $p = $type . '/' . substr(md5($id . $type), 0, 2) . '/' . substr(md5($id . $type), 2, 2) . '/' . substr(md5($id . $type), 4, 2) . '_' . $id . '_01' . $size_str;
    $ext_conf = array('.jpg', '.jpeg', '.png', '.gif');
    foreach ($ext_conf as $ext) {
        $file_path = PIC_PATH . $p . $ext ;
        if (file_exists($file_path)) {
            return PIC_URL . $p . $ext  . '?t='.filemtime($file_path);
        }
    }
    if ($return_default) {
        $file_path = PUBLIC_PATH . 'img/images/noimage' . $size_str . '.gif' ;
        if (file_exists($file_path)) {
            return IMG_URL . 'images/noimage' . $size_str . '.gif'  . '?t='.filemtime($file_path);
        }
    }

    return '';
}

/**
 * 根据产品类型, 产品id 返回指定尺寸所有图片url
 * @param int $type 1:课程 2:资料 3:资讯 4:活动 5:校区
 *                 新机构平台-- 21:课程 22:资料 31:学校logo 32:营业执照 33:首页幻灯片 34:校区环境
 * @param int $id
 * @param string $size_type 图片尺寸 l:大图 m:中图 s:小图
 * @param nums 取图片数
 * @param bool $return_default 图片不存在时,是否返回默认图片
 * @return string
 */
function get_pic_arr_by_id($type, $id, $size_type, $nums = 5, $return_default = true) {
    $pic_url_arr = array();

    $width = $height = null;
    if ($size_type) {
        $config = config('image', $type);
        $width = $config[$size_type][0];
        $height = $config[$size_type][1];
    }
    $size_str = $width && $height ? "_{$width}x{$height}" : '';
    $type = str_pad($type, 2, '0', STR_PAD_LEFT);

    $order_arr = range(1, $nums);
    $ext_conf = array('.jpg', '.jpeg', '.png', '.gif');

    foreach ($order_arr as $order) {
        $order = str_pad($order, 2, '0', STR_PAD_LEFT);
        $p = $type . '/' . substr(md5($id . $type), 0, 2) . '/' . substr(md5($id . $type), 2, 2) . '/' . substr(md5($id . $type), 4, 2) . '_' . $id . '_' . $order . $size_str;
        foreach ($ext_conf as $ext) {
            $file_path = PIC_PATH . $p . $ext ;
            if (file_exists($file_path)) {
                $pic_url_arr[] = PIC_URL . $p . $ext  . '?t='.filemtime($file_path);
            }
        }
    }

    if ($return_default && empty($pic_url_arr)) {
        $file_path = PUBLIC_PATH . 'img/images/noimage' . $size_str . '.gif' ;
        if (file_exists($file_path)) {
            $pic_url_arr[] = IMG_URL . 'images/noimage' . $size_str . '.gif'  . '?t='.filemtime($file_path);
        }
    }

    return $pic_url_arr;
}

/**
 * 取本天 本周 下周 下月等时间区间
 * @param $now_time $now_time = time();
 * @param string $t  d 天 , w 周 , m 月
 * @param int $n 偏移量
 * @return array
 */
function get_date($now_time, $t = 'd', $n = 0) {
    $date = date("Y-m-d", $now_time);
    if ($t == 'd') {
        $firstday = date('Y-m-d 00:00:00', strtotime("$n day"));
        $lastday = date("Y-m-d 23:59:59", strtotime("$n day"));
    } elseif ($t == 'w') {
        if ($n != 0)
            $date = date('Y-m-d', strtotime("$n week"));
        $lastday = date("Y-m-d 23:59:59", strtotime("$date Sunday"));
        $firstday = date("Y-m-d 00:00:00", strtotime("$lastday -6 days"));
    } elseif ($t == 'm') {
        if ($n != 0)
            $date = date('Y-m-d', strtotime("$n months"));
        $firstday = date("Y-m-01 00:00:00", strtotime($date));
        $lastday = date("Y-m-d 23:59:59", strtotime("$firstday +1 month -1 day"));
    }
    return array($firstday, $lastday);
}

/**
 * 友好时间显示
 * @param $older_date 指定时间
 * @param bool $newer_date
 * @return bool|string
 */
function time_since($older_date, $newer_date=false)
{
    $chunks = array(
        array(60 * 60 * 24 * 365, '年'),
        array(60 * 60 * 24 * 30, '月'),
        //   array(60 * 60 * 24 * 7, '周'),
        array(60 * 60 * 24, '天'),
        array(60 * 60, '小时'),
        array(60, '分钟'),);
    $newer_date = $newer_date ? $newer_date : time() ;
    $since = $newer_date - $older_date ;     //根据自己的需要调整时间段，下面的24则表示小时，根据需要调整吧
    $chunks_count = count($chunks);
    if ($since < 60 * 60 * 24 * 30) {
        for ($i = 0, $j = $chunks_count; $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }
        $out = ($count == 1) ? '1' . $name : "$count{$name}";
        if (0 == $count) {
            return "刚刚";
        }
        return $out . "前";
    } else {
        return date('Y-m-d H:i',$older_date);
    }
}

/**
 * 设置全局js的变量
 *
 */
function kuaiji_js() {
    $js = "<script type=\"text/javascript\">\n" .
        "var BASE_DOMAIN = '" . BASE_DOMAIN . "';\n" .
        "</script>\n";
    echo $js;
}

/**
 *  产品详情页 ( 培训课 ,资料, 试听课)
 * @param $id
 * @return string  http://item.kuaiji.com/123.html
 */
function product_item_url($id) {
    return ITEM_URL . $id . '.html';
}

/**
 *  机构店铺详情页( 目前只有 活动, 资讯)
 * @param $id
 * @param $type
 * @param $agency_id
 * @return string  http://renhe.kuaiji.com/news/123.html
 */
function store_item_url($id, $type, $agency_id) {
    $type_conf = array(
        1 => 'news', //机构资讯
        2 => 'activity'//机构活动
    );
    return agency_url($agency_id) . '/' . $type_conf[$type] . '/' . $id . '.html';
}

/**
 *  店铺栏目 短url
 * @param $agency_id
 * @param $cateid
 * @param null $city_id
 * @param int $type 0 产品 1资讯 2活动
 * @return string
 */
function store_cate_short_url($agency_id, $cateid, $city_id = null, $type = 0) {
    $type_conf = array(
        0 => 'cate', //产品(课程, 资料, 资讯)
        1 => 'news', //机构资讯
        2 => 'activity'//机构活动
    );

    if (empty($city_id)) {
        $agency_data_cache = common_data('agencys', 'brand');
        $city_id = $agency_data_cache[$agency_id]['city_id'];
    }

    return agency_url($agency_id) . "/{$type_conf[$type]}/{$cateid}-{$city_id}-1-0.html";
}

/**
 *  店铺栏目 长url
 * @param $agency_id
 * @param $param
 * @param int $type  0 产品 1资讯 2活动
 * @return string
 */
function store_cate_long_url($agency_id, $param, $type = 0) {
    $type_conf = array(
        0 => 'cate', //产品(课程, 资料, 资讯)
        1 => 'news', //机构资讯
        2 => 'activity'//机构活动
    );
    return agency_url($agency_id) . '/' . $type_conf[$type] . '/' . implode('-', $param) . '.html';
}

/**
 *  机构店铺域名
 * @param $agency_id
 * @return string
 */
function agency_url($agency_id) {
    $domains_data_cache = common_data('domains_name', 'brand');
    $domain = !empty($domains_data_cache[$agency_id]) ? $domains_data_cache[$agency_id] : 'shop' . $agency_id;
    return 'http://' . $domain . BASE_DOMAIN;
}

/**
 *  搜索url
 * @param $param
 * @return string
 */
function so_url($param) {
    $p = array();
    foreach ($param as $k => $v) {
        if ($k == 'q') {
            $p[] = "{$k}=" . urlencode($v);
        } else {
            $p[] = "{$k}={$v}";
        }
    }

    return SO_URL . 's?' . implode('&', $p);
}

/**
 * 模板加载静态区块文件的方式
 *
 * @param $file ;
 */
function include_tpl($file) {
    $config = config('template');
    $file_path = WWW_PATH . trim($file, '/');
    if (file_exists($file_path)) {
        include $file_path;
    } else {
        return;
    }
}

/**
 * 取子级地区
 * @param $pid
 * @param $detail
 * @param $width_letter title是否包含字母(一般用于select)
 */
function area_get_child($pid, $detail = 0, $with_letter=false) {
    if( $with_letter ){
        $area_cache = common_data('area_letter', 'system');
    }else{
        $area_cache = common_data('area', 'system');
    }
    $result = array();
    if ($pid == 0) {//取省
        foreach ($area_cache as $v) {
            if ($v['pid'] == 0) {
                if ($detail) {
                    $result[$v['id']] = $v;
                } else {
                    $result[$v['id']] = array(
                        'id' => $v['id'],
                        'title' => $v['title'],
                        'alias' => $v['alias'],
                        'capital_id' => $v['capital_id'],
                        'capital_title' => $v['capital_title'],
                        'capital_pinyin' => $v['capital_pinyin']
                    );
                }
            }
        }
    } else {
        $child_id_str = isset($area_cache[$pid]) ? $area_cache[$pid]['nextchilds'] : '';
        if ($child_id_str) {
            $child_id_arr = explode(',', $child_id_str);
            foreach ($child_id_arr as $child_id) {
                if ($detail) {
                    $result[$child_id] = $area_cache[$child_id];
                } else {
                    $result[$child_id] = array(
                        'id' => $area_cache[$child_id]['id'],
                        'title' => $area_cache[$child_id]['title'],
                        'alias' => $area_cache[$child_id]['alias'],
                    );
                }
            }
        }
    }
    return $result;
}

function contentid_dir($content_id) {
    $content_id = intval($content_id);
    if ($content_id < 1000) {
        $path = "00/00/$content_id";
    } else {
        $content_id = $content_id . "";
        $path = $content_id{0} . $content_id{1} . "/" . $content_id{2} . $content_id{3} . "/$content_id";
    }

    return $path;
}

/**
 * 视频URL
 * @param type $aca
 * @param type $id
 * @return type
 */
function video_url($aca, $id) {
    return VIDEO_URL . $aca . '/' . $id . '.html';
}

/**
 * 加载数据源
 * @param type $dsnid       数据源ID
 * @return type
 */
function load_dsn($dsnid) {
    return table('dsn', $dsnid);
}

function check_zhuti_domain($key) {
    $domain = common_data("caikao_domain_alias", "brand");
    if (!isset($domain[$key])) {
        return false;
    } else {
        return $domain[$key];
    }
}

function caikao_url($cateid, $param = array()) {
    $caikao_domain = common_data("caikao_domain_catid", "brand");
    $domain = isset($caikao_domain[$cateid]) ? 'http://' . $caikao_domain[$cateid] . BASE_DOMAIN . '/' : WWW_URL;

    if (!empty($param)) {
        $typeid = isset($param['typeid']) ? $param['typeid'] : 0;
        $subtypeid = isset($param['subtypeid']) ? $param['subtypeid'] : 0;
        $page = isset($param['page']) ? (int) $param['page'] : 0;
        $sort = isset($param['sort']) ? (int) $param['sort'] : 0;
        $area_id = isset($param['area_id']) ? (int) $param['area_id'] : 0;
        return $domain . "list/{$typeid}-{$subtypeid}-{$area_id}-{$sort}-{$page}.html";
    } else {
        return $domain;
    }
}

/**
 *  更新索引
 * @param int $type 1:课程 2:资料 3:试听课 10:机构资讯 11:活动
 * @param int/array $id 主键id
 * @return mixed
 */
function so_update_index($type, $id) {

    $config = config('soindex', $type);

    $main_model = loader::model($config['main_table'], 'brands'); //主表
    if (!empty($config['slave_table'])) {
        $slave_model = loader::model($config['slave_table'], 'brands'); //从表
    }

    /* where 条件 */
    $where = is_array($id) ? "{$config['primary_id']} IN (" . implode(',', $id) . ')' : "{$config['primary_id']} = {$id} ";
    if (is_array($config['addition_param'])) {
        foreach ($config['addition_param'] as $k => $v) {
            $where .= " AND $k=" . ( is_int($v) ? $v : "'" . $v . "' " );
        }
    }
    /* end */

    //主表
    $main_data = $main_model->select($where, $config['main_fileds'], null, is_array($id) ? null : 1);
    if (isset($slave_model)) {//存在副表
        $slave_ids = array();
        foreach ($main_data as $v) {
            $slave_ids[] = $v[$config['primary_id']];
        }
        $slave_data = $slave_model->select($slave_ids, $config['slave_fileds'], null, is_array($id) ? null : 1);
        $slave_ids_key_arr = array_as_key($slave_data, $config['primary_id']);
        foreach ($main_data as $k => $v) {
            $main_data[$k] = array_merge($v, $slave_ids_key_arr[$v[$config['primary_id']]]);
        }
    }

    $put_data = array_as_key($main_data, $config['primary_id']);
    $httpsqs = httpsqs('default') ;
    return $httpsqs->put('solr_'.$config['core'], urlencode(json_encode(array('opt'=>1, 'data'=>$put_data))));
}

/**
 *  删除索引
 * @param int $type 1:课程 2:资料 3:试听课 10:机构资讯 11:活动
 * @param int/array $id 主键id
 */
function so_delete_index($type, $id) {
    $config = config('soindex', $type);

    !is_array($id) && $id = array($id);
    $put_data = array();
    foreach($id as $v){
        $put_data[$v] = array();
    }
    $httpsqs = httpsqs('default') ;
    return $httpsqs->put('solr_'.$config['core'], urlencode(json_encode(array('opt'=>0, 'data'=>$put_data))));
}

/**
 * 显示404页面
 */
function show_404() {
    $template = factory::template();
    switch('http://' . $_SERVER['HTTP_HOST'] . '/')
    {
        case VIDEO_URL:
            $file = $template->dir . 'album' . DS . '404.html';
            break;
        default:
            $file = PUBLIC_PATH . 'brand' . DS . '404.html';
            break;
    }
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");

    if (file_exists($file)) {

        include $file;
    } else {
        echo <<<"P"
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>404 Not Found</title>
        </head>
        <body>
        <h1 style="color: #0061A5; display: block;font: 172px 'TeXGyreScholaBold',Arial,sans-serif;position: relative;text-align: center;">404</h1>
        </body>
    </html>
P;
    }

    echo "<!--".date('Y-m-d H:i:s', time())."-->";
    exit;
}

function m_show_404(){
    $css_url = CSS_URL ;
    $img_url = IMG_URL ;
    $domain = BASE_DOMAIN ;
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
    echo <<<P
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="MobileOptimized" content="320" />
	<title>404错误！找不到该页面 - 会计网手机版</title>
    <link rel="stylesheet" type="text/css" href="{$css_url}html5/404.css">
</head>
<body>
<div class="fullscreen-bg"></div>
<div class="wrap">
	<div class="tips">404错误！<br>找不到该页面</div>
	<div class="btns">
		<a href="http://m{$domain}">返回首页</a>
		<a href="javascript:history.go(-1);">返回上页</a>
	</div>
</div>
<body>
<script id="statjs" src="{$img_url}js/stat.js?catid=0" type="text/javascript" charset="utf-8"></script>
</html>
P;
    echo "<!--".date('Y-m-d H:i:s', time())."-->";
    exit;
}
/**
 * 初始化httsps队列的对象
 *
 * @param $config 配置的key ;
 */
function httpsqs($config = 'default') {
    static $h_objects;
    if (!isset($h_objects['httpsqs'])) {
        $file = ROOT_PATH . 'extension' . DS . 'queue' . DS . 'include' . DS . 'httpsqs_client.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
    if (!isset($h_objects['httpsqs'][$config])) {
        $data = config('httpsqs');
        if (isset($data[$config])) {
            extract($data[$config]);
            $class = new httpsqs($host, $port, $auth, $charset);
            $h_objects['httpsqs'][$config] = $class;
        }
    }

    return $h_objects['httpsqs'][$config];
}

/**
 * 内容专辑模型 临时生成路径入口
 * @param  int  $contentid
 * @return string
 */
function album_uri_arr($contentid) {
    $r['realpath'] = 'video/album/' . $contentid{0} . $contentid{1} . '/' . $contentid{2} . $contentid{3} . '/' . $contentid . '/' . $contentid . '_1.shtml';
    $realpath = 'video/album/' . $contentid{0} . $contentid{1} . '/' . $contentid{2} . $contentid{3} . '/' . $contentid . '/' . $contentid . '_1.shtml';
    $basepath = PUBLIC_PATH . 'html/web/';
    $r = array(
        'path' => $basepath . $realpath,
        'basepath' => $basepath,
        'realpath' => $realpath,
        'url' => VIDEO_URL . 'album/' . $contentid . '.html',
    );

    loader::model('admin/album', 'album')->content->update(array('url' => $r['url'], 'realpath' => $r['realpath']), array('contentid' => $contentid));
    return $r;
}

/**
 * 动态页面更新缓存，或者获取缓存页面
 *
 * @param $cache_dir  清除的缓存目录;
 * @param $cache_file 清除缓存页面;
 * @param $cache_time 判断缓存时间;
 */
function dynamic_cache($cache_dir , $cache_file , $cache_time=900){
    $url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    if(isset($_GET['CC']) && ($_GET['CC'] == 1 || $_GET['reset'] == 2)){
        //记录url  通知cdn
        $url = str_replace("?CC=1", '', $url);
        $url = str_replace("?cc=1", '', $url);
        $url = str_replace("&cc=1", '', $url);
        $url = str_replace("&CC=1", '', $url);
        $url = str_replace("?reset=2", '', $url);
        $url = str_replace("&reset=2", '', $url);
        $url = 'http://'.$url;
        record2url($url);
        import('helper.folder') ;
        folder::delete($cache_dir) ;
    }else if(strpos($_SERVER['REQUEST_URI'], '_NGCACHE_') === false){
        if( strpos($url, WWW_URL . 'list/') !== false ){//若是列表页url, 通知cnd
            record2url('http://'.$url);
        }
        if(@ $f_time = filectime($cache_file) ){
            $n_time = time() ;
            if(($n_time - $f_time) < $cache_time ){
                echo file_get_contents($cache_file) ;
                echo '<!-- cache -->' ;
                exit() ;
            }
        }
    }

}

///******************************以下方法是临时出来brand与cmstop相互之间调取数据的方法，待相互迁移完成后即可删除*************************************

/**
 * brand调取cmstop的model方式
 *
 * @param $model ;
 * @param $app ;
 */
function cmstop_model($model, $app = null) {
    if (isset($_ENV['extapp']) && $_ENV['extapp'] == 'brand') {
        $extapp = $_ENV['extapp'];
        $_ENV['extapp'] = '';
        $m = loader::model($model, $app);
        $_ENV['extapp'] = $extapp;
        return $m;
    } else {
        return loader::model($model, $app);
    }
}

/**
 * brand调取cmstop的model方式
 *
 * @param $model ;
 * @param $app ;
 */
function cmstop_lib($model, $app = null) {
    if (isset($_ENV['extapp']) && $_ENV['extapp'] == 'brand') {
        $extapp = $_ENV['extapp'];
        $_ENV['extapp'] = '';
        $m = loader::lib($model, $app);
        $_ENV['extapp'] = $extapp;
        return $m;
    } else {
        return loader::lib($model, $app);
    }
}

/**
 * extapp调取cmstop的model方式
 *
 * @param $extapp 扩展app , 目前extapp有brand ;
 * @param $model ;
 * @param $app ;
 */
function extapp_model($extapp , $model, $app = null) {
    $tmp = '' ;
    if(isset($_ENV['extapp'])){
        $tmp = $_ENV['extapp'] ;
    }

    $_ENV['extapp'] = $extapp ;
    $m = loader::model($model, $app);
    $_ENV['extapp'] = $tmp ;
    return $m ;
}

/**
 * 格式化打印数据
 * @param $data
 * @param bool $die
 */
function printR($data, $die = true)
{
    header("Content-type: text/html; charset=utf-8");
    echo '<pre>';
    print_r($data);
    if($die)
    {
        die();
    }
}
/**
 * 格式化数据 写入文件
 * @param $data
 * @param bool $die
 */
function WT($data, $die = true)
{
    cache_write('phplog/' . date('YmdH', time()) . '.php', $data);
}

/**
 * 获得论坛城市 url
 * @param int $province_id 省份 ID，6 位数字
 * @param int $city_id 城市 ID，6 位数字
 * @param string $city_name 城市名称
 * @return string
 */
function get_bbs_city_url($province_id, $city_id = 0, $city_name = '')
{
    $province_id = (int)$province_id / 100;
    $city_id = (int)$city_id / 100;
    $url = BBS_URL . 'forum.php?mod=forumdisplay&action=list&fid=' . $province_id;
    $municipality = array('北京', '上海', '天津', '重庆');
    if(!in_array($city_name, $municipality))
    {
        $url .= '&filter=typeid&typeid=' . $city_id;
    }
    return $url;
}

/**
 * 获得论坛城市主题数据
 * @param int $city_id 城市 ID，6 位数字
 * @param int $province_id 省份 ID，6 位数字
 * @param int $attachment 0无附件 1普通附件 2有图片附件
 * @param int $limit 条数
 * @return array
 */
function get_bbs_city_thread($city_id, $province_id, $attachment = 0, $limit = 5)
{
    $bbs_api = load_rpc('bbs');
    return $bbs_api->city_thread($city_id, $province_id , $attachment, $limit);
}

/**
 * 获得论坛指定板块最新主题数据
 * @param int|array $fid 板块 ID
 * @param int $size 条数
 * @param bool $has_attachment 是否存在附件
 * @return array
 */
function get_bbs_newest_thread($fid, $size, $has_attachment = false)
{
    $bbs_api = load_rpc('bbs');
    return $bbs_api->newest_thread($fid, $size, $has_attachment);
}

/**
 * 获得论坛热帖
 * @param int $detail
 * @param int $thread_num
 * @param array $fid_arr
 * @param int $day
 * @param array $type_id
 * @param int $attachment
 * @return array
 */
function get_bbs_hot_thread($detail = 0, $thread_num = 0, $fid_arr = array(), $day = 7, $type_id = array(), $attachment = 0)
{
    $bbs_api = load_rpc('bbs');
    return $bbs_api->get_hot_thread($detail, $thread_num , $fid_arr , $day, $type_id , $attachment);
}

/**
 * 获得论坛会计秀图片数据
 * @param int $pic_num
 * @param int $bid
 * @return array
 */
function get_bbs_commend_pic($pic_num = 0, $bid = 0)
{
    $bbs_api = load_rpc('bbs');
    return $bbs_api->get_bbs_pic($pic_num , $bid);
}

/**
 * 地方站课程数据
 * @param $area_id
 * @return array
 */
function difangzhan_course($area_id){
    /* start 搜索条件 */
    $param = $param2 = array(); //总条件
    $area_id2 = 0;
    if(is_array($area_id))
    {
        $area_id2 = $area_id[1];
        $area_id = $area_id[0];
    }

    $query_ids = array(
        '会计考证'=>array(21110,21120,21130),//会计从业 初级会计 中级会计
        '注会'=>array(21150),// 注册会计师
        '注税'=>array(21160),//注册税务师
        '零基础就业'=>array(21501),//零基础就业
        '真账实操'=>array(21502),//真账实操
        '税务实操'=>array(21503),//税务实操
        '财务软件'=>array(21504),//财务软件
    );

    //最新
    foreach( $query_ids as $k=>$ids ){
        $id_str = '';
        foreach( $ids as $cid ){
            $id_str = empty($id_str) ? " catid:{$cid} " : $id_str . "|| catid:{$cid} ";
        }
        $course_additionalParameters = array(
            'sort' => 'add_time DESC',
            'fq' => " iscollect:0 && city_id:{$area_id} && ({$id_str})",
            'fl' => 'id,title,url,img_url,seller_id,price,show_price',
        );
        $param[$k] = array('_func' => 'query', 'course_v2', '', 0, 5, $course_additionalParameters);

        if($area_id2)
        {
            $course_additionalParameters2 = array(
                'sort' => 'add_time DESC',
                'fq' => " iscollect:0 && city_id:{$area_id2} && ({$id_str})",
                'fl' => 'id,title,url,img_url,seller_id,price,show_price',
            );
            $param2[$k] = array('_func' => 'query', 'course_v2', '', 0, 5, $course_additionalParameters2);
        }
    }

    //搜
    $so_result_m_course = so('multi_func', array($param));
    $so_result_m_course2 = so('multi_func', array($param2));

    //分析结果
    $course_data = array();
    foreach ($so_result_m_course as $k => $r) {
        !isset($course_data[$k]) && $course_data[$k] = array();
        if ( !empty($r) ) {
            foreach ($r['response']['docs'] as $v) {
                $doc = array(
                    'id' => $v['id'],
                    'title' => $v['title'],
                    'url' => $v['url'],
                    'kuaiji_price' => ($v['price'] > 0) && $v['show_price'] ? sprintf('%.2f', $v['price']) : '咨询',
                );

                //机构
                $additionalParameters = array();
                $additionalParameters['fq'] = " id:{$v['seller_id']}"; //城市id
                $additionalParameters['fl'] = " title";
                $additionalParameters['sort'] = "add_time desc";
                $so_param = array('seller', '', 0, 1, $additionalParameters);
                $so_result_seller = so('query', $so_param);
                $doc['agency_title'] = $so_result_seller['response']['docs'][0]['title'];
                $doc['agency_url'] = WWW_URL . 'xuexiao/'. $v['seller_id'];

                //开课校区
                $db = factory::db();
                $sql="SELECT school_id FROM cms_class,cms_content WHERE cms_class.`contentid`=cms_content.`contentid` AND cms_content.`status`=6 AND course_id={$v['id']}";
                $result = $db->select($sql);
                //获取校区id
                $doc['school_id'] = array();
                foreach($result as $v){
                    if(!$doc['school_id']){
                        $doc['school_id'][] = $v['school_id'];
                    }else{
                        $bj=1;
                        for($j=0; $j<count($doc['school_id']); $j++){
                            if($doc['school_id'][$j]==$v['school_id']){
                                $bj=0;
                                break;
                            }
                        }
                        if($bj){
                            $doc['school_id'][] = $v['school_id'];
                        }
                    }
                }
                if($doc['school_id']){
                    $sql="select `school_title` from cms_mall_school where school_id={$doc['school_id'][0]}";
                    $result = $db->get($sql);
                    $doc['school_str'] = $result['school_title'];
                    count($doc['school_id']) > 1 && $doc['school_str'] .= '等' . count($doc['school_id']) . '个校区';
                }
                $course_data[$k][] = $doc;
            }
        }
    }

    foreach ($so_result_m_course2 as $k => $r) {
        $exists_ids = array();
        if(isset($course_data[$k]))
        {
            foreach($course_data[$k] as $c_v)
            {
                $exists_ids[] = $c_v['id'];
            }
        } else {
            $course_data[$k] = array();
        }
        if ( !empty($r) ) {
            foreach ($r['response']['docs'] as $v) {
                if(in_array($v['id'], $exists_ids))
                {
                    continue;
                }
                if(count($course_data[$k]) >= 5)
                {
                    break;
                }
                $doc = array(
                    'id' => $v['id'],
                    'title' => $v['title'],
                    'url' => $v['url'],
                    'kuaiji_price' => ($v['price'] > 0) && $v['show_price'] ? sprintf('%.2f', $v['price']) : '咨询',
                );

                //机构
                $additionalParameters = array();
                $additionalParameters['fq'] = " id:{$v['seller_id']}"; //城市id
                $additionalParameters['fl'] = " title";
                $additionalParameters['sort'] = "add_time desc";
                $so_param = array('seller', '', 0, 1, $additionalParameters);
                $so_result_seller = so('query', $so_param);
                $doc['agency_title'] = $so_result_seller['response']['docs'][0]['title'];
                $doc['agency_url'] = WWW_URL . 'xuexiao/'. $v['seller_id'];

                //开课校区
                $db = factory::db();
                $sql="select `school_ids` from cms_mall_class_base where course_id={$v['id']} and seller_id={$v['seller_id']} and status=6 and able_status=1";
                $result = $db->select($sql);
                //获取校区id
                $doc['school_id'] = array();
                foreach($result as $v){
                    $school_id = explode(',',$v['school_ids']);
                    if(!$doc['school_id']){
                        $doc['school_id'] = $school_id;
                    }else{
                        for($i=0; $i<count($school_id); $i++){
                            $bj=1;
                            for($j=0; $j<count($doc['school_id']); $j++){
                                if($doc['school_id'][$j]==$school_id[$i]){
                                    $bj=0;
                                    break;
                                }
                            }
                            if($bj){
                                $doc['school_id'][] = $school_id[$i];
                            }
                        }
                    }
                }

                $sql="select `school_title` from cms_mall_school where school_id={$doc['school_id'][0]}";
                $result = $db->get($sql);
                $doc['school_str'] = $result['school_title'];
                count($v['school_id']) > 1 && $doc['school_str'] .= '等' . count($v['school_id']) . '个校区';

                $course_data[$k][] = $doc;
            }
        }
    }

    return $course_data ;
}

/**
 * 判断是否为VIP用户
 * @param $uid
 * @return mixed
 */
function check_vip_user($uid)
{
    $bbs_api = load_rpc('bbs');
    return $bbs_api->check_vip_user($uid);
}

/**
 * 输出属性的select下拉菜单
 * @param string $id
 * @param string $name
 * @param int $proid
 * @param string $value
 * @param string $width
 */
function property_once($id = "proid", $name = "proids", $proid = 0, $value = '',$width = '150px' )
{
    echo '<input id="'.$id.'" width="'.$width.'" class="selectree" name="'.$name.'" value="'.$value.'" initurl="?app=system&controller=property&action=name&proid=%s" url="?app=system&controller=property&action=cate&issingle=1&isonly='.$proid.'&proid=%s" paramVal="proid" paramTxt="name" />';
    echo '<script>$(function(){$(\'#'.$id.'\').selectree();})</script>';
}


/**
 * 获取属性的子元素  -> 无限级别
 * @param $id
 * @return mixed
 */
function get_property_child($id)
{
    $propertys = common_data('property_0', 'brand');
    $myself = $propertys[$id];
    if ($myself['childids']) {
        foreach($propertys  as $val) {
            if ($val['parentid'] == $id) {

                $return[$val['proid']] = $val;
                if ($val['childids'])$return[$val['proid']]['childs'] = get_property_child($val['proid']);
            }
        }
    }
    return $return;
}

/**
 * 获取属性的子元素  -> 无限级别 -> 只取 子元素有在 $array_keys();
 * @param $id
 * @return mixed
 */
function get_property_child_inarray($id, $array_keys)
{
    $propertys = common_data('property_0', 'brand');
    $myself = $propertys[$id];
    if ($myself['childids']) {
        foreach($propertys  as $val) {
            if ($val['parentid'] == $id) {
                $_hava = false;
                foreach($array_keys as $key) {
                    if (in_array($key, explode(',', $val['childids'])) || in_array($val['proid'], $array_keys) ){
                        $_hava = true;
                    }
                }
                if (!$_hava)continue;
                $return[$val['proid']] = $val;
                if ($val['childids'])$return[$val['proid']]['childs'] = get_property_child_inarray($val['proid'], $array_keys);
            }
        }
    }
    return $return;
}

/**
 * @param $city_id 城市 ID，6 位数字
 * @param $province_id 省份 ID，6 位数字
 * @param int $attachment -1 = 不存在附件条件，附件 0 = 无，1 = 普通，2 = 有图片附件
 * @param int $limit 条数
 * @return array
 */
function get_bbs_new_city_thread($city_id, $province_id, $attachment = -1, $limit = 5)
{
    $bbs_api = load_rpc('bbs');
    return $bbs_api->get_city_thread($city_id, $province_id , $attachment, $limit);
}

/**
 * 获得普通附件热门帖子
 * @param array $fid
 * @param array $typeid
 * @param int $day
 * @param int $size
 * @return array
 */
function get_attachment_hot_thread($fid = array(), $typeid = array(), $day = 7, $size = 10)
{
    $bbs_api = load_rpc('bbs');
    return $bbs_api->get_attachment_hot_thread($fid, $typeid, $day, $size);
}

/**
 *
 * 32位MD加密分目录
 * @param $dirname
 * @param int $len
 * @return string
 */

function splitdir($dirname, $len = 8) {
    $size = strlen($dirname);
    $frequency = $size/$len;
    for ($i=0; $i<$frequency; $i++) {
        $dir[] = substr($dirname, $i*$len, $len);
    }
    return $dir ? implode('/', $dir).'/' : '';


}


/**
 * 记录url 发布给CDN for silen
 *
 * 去掉list下面的页面时时更新
 * 去掉二级域名的url更新
 * @param $url
 */
function record2url($url)
{
    $is_www_url = false;
    if (is_array($url)){
        foreach ($url as $v) {
            $urls[] = $v;
            $is_www_url = true;
            if ((bool)strpos('silen'.$url, '/list/'))continue;
            if ((bool)strpos('silen'.$url, '/city/'))continue;
        }
        if ($urls)$url = implode("\r\n", $urls). "\r\n";
    } else {
        if (!(bool)strpos('silen'.$url, '/list/') && !(bool)strpos('silen'.$url, '/city/')) {
            $url = $url."\r\n";
            $is_www_url = true;
        }
    }
    $file = ROOT_PATH . 'data/cdn/filelist.txt';
    if ($url && $is_www_url) {
        write_file($file, $url, true);
    }
}


/**
 * 记录url 发布给CDN
 * @param $url
 */
function log2day($data, $name, $username)
{
    $detail = "\r\n-------- ".date('Y-m-d H:i:s', time()). "   " .  $username ."----------------\r\n";
    if (is_array($data)){
        $txt = implode("\r\n", $data). "\r\n";
    } else {
        $txt = $data."\r\n";
    }
    $name = $name ? $name : 'default';
    $file = ROOT_PATH . 'data/log2day/'.date('Ymd', time()).'/' . $name . '.txt';
    if ($txt) {
        write_file($file, $detail.$txt, true);
    }
}

/**
 * 会计网前台获取cookie的对象
 * @return cookie
 */
function KJcookie()
{
    $config =  array(
        'prefix' => 'KJ_',
        'path' => '/',
        'domain' => '.kuaiji.com',
    );
    import('core.cookie');
    //$config = config('cookie');
    return new cookie($config['prefix'], $config['path'], $config['domain']);
}

/**
 * die json
 *
 * @param $result
 */
function dieJson($result)
{
    header('Content-Type: application/json');
    $json = & factory::json();
    $result = $json->encode($result);
    if ($_GET['jsoncallback']) {
        echo $_GET['jsoncallback'] ."($result);";
    } else {
        echo $result;
    }
    exit;
}

/**
 * 对字符串进行加解密
 * @param $string
 * @param string $operation
 * @param string $key
 * @param int $expiry
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 6;

    $key = md5($key ? $key : KJ_AUTH_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE')
    {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16))
        {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 *  geohash编码
 * @param $latitude
 * @param $longitude
 * @param $deep
 * @return string
 */
function encode_geohash($latitude, $longitude, $deep)
{
    $BASE32	= '0123456789bcdefghjkmnpqrstuvwxyz';
    $bits = array(16,8,4,2,1);
    $lat = array(-90.0, 90.0);
    $lon = array(-180.0, 180.0);

    $bit = $ch = $i = 0;
    $is_even = 1;
    $i = 0;
    $geohash = '';
    while($i < $deep)
    {
        if ($is_even)
        {
            $mid = ($lon[0] + $lon[1]) / 2;
            if($longitude > $mid)
            {
                $ch |= $bits[$bit];
                $lon[0] = $mid;
            }else{
                $lon[1] = $mid;
            }
        } else{
            $mid = ($lat[0] + $lat[1]) / 2;
            if($latitude > $mid)
            {
                $ch |= $bits[$bit];
                $lat[0] = $mid;
            }else{
                $lat[1] = $mid;
            }
        }

        $is_even = !$is_even;
        if ($bit < 4)
            $bit++;
        else {
            $i++;
            $geohash .= $BASE32[$ch];
            $bit = 0;
            $ch = 0;
        }
    }
    return $geohash;
}

/**
 *  geohash解码
 * @param $geohash
 * @return array
 */
function decode_geohash($geohash)
{
    $geohash = strtolower($geohash);
    $BASE32	= '0123456789bcdefghjkmnpqrstuvwxyz';
    $bits = array(16,8,4,2,1);
    $lat = array(-90.0, 90.0);
    $lon = array(-180.0, 180.0);
    $hashlen = strlen($geohash);
    $is_even = 1;

    for($i = 0; $i < $hashlen; $i++ )
    {
        $of = strpos($BASE32,$geohash[$i]);
        for ($j=0; $j<5; $j++) {
            $mask = $bits[$j];
            if ($is_even) {
                $lon[!($of&$mask)] = ($lon[0] + $lon[1])/2;
            } else {
                $lat[!($of&$mask)] = ($lat[0] + $lat[1])/2;
            }
            $is_even = !$is_even;
        }
    }
    $point = array( 0 => ($lat[0] + $lat[1]) / 2, 1 => ($lon[0] + $lon[1]) / 2);
    return $point;
}



/**
 * 新机构后台链接构造
 * @param $ca
 * @param $params
 */
function mall_url($ca, $params = null) {
    $params =  is_array($params) ? http_build_query($params) : $params ;
    return MALL_URL . $ca . ( $params ? '?'.$params : '' ) ;
}


/**
 * 取地区缓存
 */
function area_get_cache() {
    return common_data('area', 'system');
}

/**
 * 取指定地区
 */
function area_get_info($id) {
    $area_cache = area_get_cache();
    return isset($area_cache[$id]) ? $area_cache[$id] : array();
}

/**
 * 取商圈缓存
 * @param string $id 为空:返回全部数据, 为商圈id:返回该商圈, 为省市区id:返回辖下所有商圈
 * @param string $pinyin 商圈拼音非空, 并且$id为市区id, 返回单个商圈(其他情况此参数无效)
 * @return array
 */
function area_sd_get_cache($id='', $pinyin=''){
    $cache = common_data('area_sd', 'system');
    if( ! $id ){//为空:返回全部数据,
        return $cache ;

    }elseif( $id >= 10000000 ){//为商圈id:返回该商圈,
        return isset($cache[$id]) ? $cache[$id] : array();

    }else{//为省市区id:返回辖下所有商圈
        if( $id%1000 == 0 ){
            $keyname = 'province_id';
        }elseif($id%100 == 0 ){
            $keyname = 'city_id';
        }else{
            $keyname = 'region_id' ;
        }

        $rs = array();
        foreach($cache as $v){
            if( $id == $v[$keyname] ){
                if( $pinyin){
                    if( in_array($keyname, array('city_id','region_id')) && $v['sd_pinyin'] == $pinyin ){
                        return $v ;
                    }
                }else{
                    $rs[] = $v ;
                }
            }
        }
        return $rs ;
    }
}

/**
 *  取属性分类缓存
 * @param int/array $type 0:全部
 * @return array
 */
function property_get_cache($type=0) {
    if( is_array($type) ){
        $result = array();
        foreach($type as $t){
            $result = $result + common_data('property_' . $t, 'brand') ;
        }
        return $result ;
    }else{
        return common_data('property_' . $type, 'brand');
    }
}

/**
 *  取系统分类缓存
 * @param int/array $category_type 1:培训班 2:资料 3:网校(试听课)  10:资讯 11:法规
 * @return array
 */
function category_get_cache($category_type) {
    if( is_array($category_type) ){
        $result = array();
        foreach($category_type as $type){
            $result = $result + common_data('category_' . $type, 'brand') ;
        }
        return $result ;
    }else{
        return common_data('category_' . $category_type, 'brand');
    }
}

/**
 * 取课程的上课形式分类
 */
function course_format_get_cache() {
    return common_data('course_format', 'brand');
}

/**
 * 检查课程的上课形式id是否正确
 */
function course_format_check_id($id) {
    !is_array($id) && $id = explode(',', trim(trim($id), ','));
    $cache_data = course_format_get_cache();
    foreach ($id as $key) {
        if (!isset($cache_data[$key]))
            return false;
    }
    return true;
}

/**
 *  取课程的上课时间分类
 */
function course_time_get_cache() {
    return common_data('course_time', 'brand');
}

/**
 *  检查课程的上课时间id是否正确
 */
function course_time_check_id($id) {
    !is_array($id) && $id = explode(',', trim(trim($id), ','));
    $cache_data = course_time_get_cache();
    foreach ($id as $key) {
        if (!isset($cache_data[$key])) {
            return false;
        }
    }
    return true;
}

/**
 *  取购买名单价格
 */
function enroll_price_get_cache() {
    return common_data('enroll_price', 'brand');
}

/**
 *  取购买名单 联系方式
 */
function enroll_method_get_cache() {
    return common_data('enroll_method', 'brand');
}

/**
 *  取购买名单 联系结果
 */
function enroll_result_get_cache() {
    return common_data('enroll_result', 'brand');
}

/**
 *  trim数组元素
 * @param $input
 * @return array|string
 */
function trim_array($input){
    if( is_array($input) ){
        $input = array_map('trim_array', $input);
    }else{
        $input = trim($input);
    }
    return $input ;
}


/**
 *  取上传配置
 * @param $file_type string 上传类型如 picture , file
 * @param $type int 关联类型(1:课程 2:资料 3:资讯 4:活动 5:校区)
 * @return mixed
 */
function get_upload_option($file_type, $type) {
    $config = config('upload', $file_type);
    return $config[$type];
}

/**
 * 移动异步上传的临时文件
 * @param array $url_array 图片url数组
 * @param int $type 类型
 * @param int $id ID
 * @param int $num 限制上传个数
 * @param string $temp_dir_name 临时目录名称
 * @return array
 */
/* 临时文件路径 http://file.t.kuaiji.com/tmp/20131010/138146143441206665.jpg */
/* 临时文件路径 http://file.t.kuaiji.com/tmp/20131010/50x50/138146143441206665.jpg */
/* 正式文件路径 upload/05/substr(md5($id."05"),0,2)/substr(md5($id."05"),2,2)/substr(md5($id."05"),4,2)_$id_01.jpg */
/* 正式文件路径 upload/05/substr(md5($id."05"),0,2)/substr(md5($id."05"),2,2)/substr(md5($id."05"),4,2)_$id_01_50x50.jpg */
/* 正式文件临时路径 upload/05/substr(md5($id."05"),0,2)/substr(md5($id."05"),2,2)/tmp */
function move_tmp_pic($url_array, $type, $id, $num = 5, $temp_dir_name = 'tmp') {

    $orginal_pics_abs = array(); //保存原始图片绝对路径

    /* 过滤$_POST['pic'] */
    $pics = array();
    if (is_array($url_array)) {
        foreach ($url_array as $p) {
            if (!is_string($p) || !$p = trim($p)) {
                continue;
            }//非string 或 空 -> do next
            $pics[] = $p; //保证 key 是 0 , 1 ,2 ...
            if (count($pics) == $num) {
                break;
            }//满5个, 退出
        }
    } else {
        return array();
    }
    /* end */

    // 常用变量
    $type = str_pad($type, 2, '0', STR_PAD_LEFT); //补足类型为2位
    $path_rel = $type . '/' . substr(md5($id . $type), 0, 2) . '/' . substr(md5($id . $type), 2, 2) . '/'; //正式相对路径
    $pic_pre_rel = $path_rel . substr(md5($id . $type), 4, 2) . '_' . $id . '_'; //正式文件路径前部

    /* copy到临时目录 */
    $tmp_path_abs = PIC_PATH . $path_rel . 'tmp/'; //临时目录路径
    mkdir($tmp_path_abs, 0755, true); //生成临时文件夹
    $id_pic_arr = glob(PIC_PATH . $pic_pre_rel . '*');
    if (is_array($id_pic_arr)) {
        foreach ($id_pic_arr as $p) {
            copy($p, $tmp_path_abs . basename($p));
        }
    }
    /* end */

    foreach ($pics as $k => $pic) {

        $order = str_pad($k + 1, 2, '0', STR_PAD_LEFT); //排序order补足为2位 ，从01开始
        //判断是临时图片 ，还是正式图片
        $file_path_rel = str_replace(PIC_URL, '', $pic); //图片相对路径
        if (!is_file(PIC_PATH . $file_path_rel)) {
            continue;
        }//不存在图片， next
        $path_parts = pathinfo(PIC_PATH . $file_path_rel); //取图片信息

        if (preg_match('/^' . $temp_dir_name . '\/\d+\/\d+\.\w+$/', $file_path_rel)) {//临时图片
            //移动原图
            rename(PIC_PATH . $file_path_rel, PIC_PATH . $pic_pre_rel . $order . '.' . $path_parts['extension']);
            //移动缩略图
            $thumb_arr = glob("{$path_parts['dirname']}/*/{$path_parts['basename']}");
            if (is_array($thumb_arr)) {
                foreach ($thumb_arr as $thumb) {
                    $pattern = $path_parts['dirname'] . '/*/' . $path_parts['basename'];
                    $pattern = '/^' . preg_quote($pattern, '/') . '$/';
                    $pattern = str_replace('\*', '(.*?)', $pattern);
                    if (preg_match($pattern, $thumb, $matches)) {
                        rename($thumb, PIC_PATH . "{$pic_pre_rel}{$order}_{$matches[1]}.{$path_parts['extension']}");
                    }
                }
            }
            $orginal_pics_abs[] = PIC_PATH . "{$pic_pre_rel}{$order}.{$path_parts['extension']}";
        } elseif (preg_match('/^' . preg_quote($pic_pre_rel, '/') . '\d+\.\w+$/', $file_path_rel)) {//正式图片
            //移动原图
            rename($tmp_path_abs . $path_parts['basename'], PIC_PATH . $pic_pre_rel . $order . '.' . $path_parts['extension']);
            //移动缩略图
            $thumb_arr = glob($tmp_path_abs . $path_parts['filename'] . "_*");
            if (is_array($thumb_arr)) {
                foreach ($thumb_arr as $thumb) {
                    $pattern = $tmp_path_abs . $path_parts['filename'] . '_*.' . $path_parts['extension'];
                    $pattern = '/^' . preg_quote($pattern, '/') . '$/';
                    $pattern = str_replace('\*', '(.*?)', $pattern);
                    if (preg_match($pattern, $thumb, $matches)) {
                        rename($thumb, PIC_PATH . "{$pic_pre_rel}{$order}_{$matches[1]}.{$path_parts['extension']}");
                    }
                }
            }
            $orginal_pics_abs[] = PIC_PATH . "{$pic_pre_rel}{$order}.{$path_parts['extension']}";
        } else {
            continue;
        }
    }

    /*  删除上传临时文件 */
    $dir_arr = scandir($tmp_path_abs);
    foreach ($dir_arr as $fname) {
        if ($fname == '.' || $fname == '..') {
            continue;
        }
        if (is_file($tmp_path_abs . $fname)) {
            @unlink($tmp_path_abs . $fname);
        }
    }
    @rmdir($tmp_path_abs);
    /* end */

    /*  删除正式目录多余文件 */
    $order_arr = array(); //正式文件序号
    foreach ($orginal_pics_abs as $k => $v) {
        $order_arr[] = str_pad($k + 1, 2, '0', STR_PAD_LEFT); //排序order补足为2位 ，从01开始
    }
    $id_pic_arr = glob(PIC_PATH . $pic_pre_rel . '*');
    if (is_array($id_pic_arr)) {
        foreach ($id_pic_arr as $p) {
            preg_match('/' . substr(md5($id . $type), 4, 2) . '_' . $id . '_(\d+)(_\w+)?\.(\w+)/', $p, $matches);
            //删除多余序号的
            !in_array($matches[1], $order_arr) && @unlink($p);
            //删除序号相同 但不同扩展名的
            if (strrchr($orginal_pics_abs[(int) $matches[1] - 1], '.') != '.' . $matches[3]) {
                @unlink($p);
            }
        }
    }
    /* end */

    /* 拼凑结果 */
    $result = array();
    foreach ($orginal_pics_abs as $k => $p) {
        $path_parts = pathinfo($p);
        $result[] = array(
            'img_name' => $path_parts['basename'],
            'img_path' => str_replace(PIC_PATH, '', $p),
            'img_url' => str_replace(PIC_PATH, PIC_URL, $p),
            'img_size' => filesize($p),
            'img_ext' => $path_parts['extension'],
            'add_time' => filemtime($p),
            'listorder' => $k + 1,
        );
    }
    /* end */

    return $result;
}

/**
 * 学校url
 */
function xuexiao_url($seller_id){
    return WWW_URL . 'xuexiao/' . $seller_id ;
}

/**
 * 截取字符串( 去除html标记 )
 * @param $str
 * @param $len
 * @param string $dash
 * @return string
 */
function text_cut($str, $len=0, $dash='...'){
    $str = trim(str_replace('　', '  ', strip_tags( str_replace(array("\r\n", "\r", "\n", '&nbsp;'), ' ', $str) )));
    if($len > 0 ){
        $dash = mb_strlen($str, 'utf-8') > $len ? $dash : '' ;
        $str = mb_substr( $str, 0, $len, 'utf-8' ) . $dash;
    }
    return htmlspecialchars($str, ENT_QUOTES, 'utf-8', false);
}

/**
 * 题库的题目url标识
 * @param $questionid
 * @return mixed
 */
function question2short($questionid)
{
    $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $key = "xx_silen";
    $urlhash = md5($key . $questionid);
    $len = strlen($urlhash);

    #将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
    for ($i = 0; $i < 2; $i++) {
        $urlhash_piece = substr($urlhash, $i * $len / 2, $len / 2);
        #将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
        $hex = hexdec($urlhash_piece) & 0x3fffffff; #此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常

        $short_url = "";
        #生成6位短连接
        for ($j = 0; $j < 6; $j++) {
            #将得到的值与0x0000003d,3d为61，即charset的坐标最大值
            $short_url .= $charset[$hex & 0x0000003d];
            #循环完以后将hex右移5位
            $hex = $hex >> 5;
        }
        $short_url_list[] = $short_url;
    }

    return $short_url_list ? 'q_' . implode('', $short_url_list) : '';
}

/**
 * 子码 获取首字母（中文）
 * @param $s0
 * @return null|string
 */
function getfirstchar($s0){
    $fchar = ord($s0{0});
    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
    $s1 = iconv("UTF-8","gb2312", $s0);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "I";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return null;
}

/**
 * 题库科目相关SEO
 * @param $subjectid
 * @param string $type
 * @return mixed
 */
function examSEO($subjectid, $type = 'k')
{
    $t_k = config::get('exam', 't_k');
    return $t_k[$subjectid][$type];
}

/**
 * 科目的CP  字母关联
 * @param $subjectid
 * @return string
 */
function examurl2cp($subjectid)
{
    $url_cp = config::get('exam','url_cp');
    foreach($url_cp as $k=>$v)
    {
        if($subjectid == $v)
        {
            return WWW_URL .'exam/'. $k.'.html';
        }
    }
    return WWW_URL .'exam/index.html';
}


/**
 * 机构平台手机报名表单输出
 * @param int $contentid 开班id
 * @param int $course_id 课程id
 * @param int $seller_id 学校id
 * @param int $show_school 是否显示校区(0:显示选择省市区 1:显示选择校区)
 */
function verifyform($contentid = 0, $course_id = 0, $seller_id = 0, $show_school=0)
{
    $html = '';
    if( !$show_school ){
        $json = array();
        foreach(common_data('area', 'system') as $city) {
            $json[] = "{$city['id']}:{$city['alias']}";
        }
        $json = json_encode($json);
        $html = '<script type="text/javascript">' .
            '$(function () {' .
            'var data = '. $json . ';' .
            '$(\'#sele2\').ganged({\'data\': data});' .
            '});' .
            '</script>';
    }

    $html .= '<script type="text/javascript">' .
        'var contentid = '.$contentid.';' .
        'var course_id =  '.$course_id.';' .
        'var seller_id =  '.$seller_id.';' .
        'var show_school = ' . $show_school .';'.
        '</script>';

    echo $html;
    $template = factory::template();
    $template->assign('show_school', $show_school);
    $template->display("city/verifyform.html");
}

/**
 * 机构平台首字母输出
 */

function peixunAlpha()
{
    $Alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    foreach($Alpha as $val) {
        echo "<a href=\"".WWW_URL."xuexiao/initial_{$val}_1\" >{$val}</a>";
    }
}


/**
 * cnzz 统计
 * @version 1.0
 */
class CS {

    private $siteId;
    private $scheme;
    private $imageDomain = 'c.cnzz.com';

    /**
     *
     * @param Integer $siteId 站点ID
     */
    public function __construct($siteId) {
        $this->setAccount($siteId);
        $this->initScheme();
    }

    /**
     * 设置站点ID
     * @param type $siteId
     */
    public function setAccount($siteId) {
        $this->siteId = $siteId;
    }

    private function initScheme() {
        $this->scheme = $this->getScheme();
    }

    /**
     * 得到url中的scheme
     * @return String
     */
    private function getScheme() {
        return (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] !== "off") ? 'https://' : 'http://');
    }

    /**
     *
     * @return String 回传数据的请求字符串
     */
    public function trackPageView() {
        return $this->getImageUrl();
    }

    private function getImageUrl() {
        $imageLocation = $this->scheme . $this->imageDomain . '/wapstat.php';
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $query = array();
        array_push($query, 'siteid=' . $this->siteId * 1);
        array_push($query, 'r=' . urlencode($referer));
        array_push($query, 'rnd=' . mt_rand(1, 2147483648));
        $imageUrl = $imageLocation . '?' . implode('&', $query);
        return $imageUrl;
    }

}

function _cnzzTrackPageView($siteId) {
    $cs = new CS($siteId);
    return $cs->trackPageView();
}


function book($url)
{
    $contentid = false ;
    if(preg_match("/\/([0-9]+)(_[0-9]+)?/" , $url , $arr)){
        $contentid = $arr[1] ;
    }

    if(!$contentid) {
        show_404() ;
    }
    $content = loader::model('content', 'system');
    $r = $content->get(array('contentid'=>$contentid, 'modelid'=>17));
    if(!$r || intval($r['status']) !=6 ) {
        show_404() ;
    }
    $book = loader::model('admin/book','book');
    if($r = $book->html_write($contentid)){
        $url = str_replace('?CC=1', '', $url);
        $file_path = '';
        kuaiji_article_show($contentid, $file_path, 1);
        //go($url);

    }

    show_404() ;
}
/**
 * 文章内页的生成
 *
 * @param $url ;
 */
function article($url){
    $contentid = false ;
    $type = false ;
    if(preg_match("/(news|fagui|shiwu)\/([0-9]+)(_[0-9]+)?/" , $url , $arr)){
        $type = $arr[1] ;
        $contentid = $arr[2] ;
        if(isset($arr[3]) && !empty($arr[3])){
            $page = substr($arr[3] , 1) ;
        }
    }

    if(!$contentid&&preg_match("/([0-9]+)(_[0-9]+)?(\?utm_source=([a-zA-Z0-9]+))?$/" , $url , $arr)){
        $contentid = $arr['1'];
        if(isset($arr[2]) && !empty($arr[2])){
            $page = substr($arr[2] , 1) ;
        }
        if(isset($arr[3]) && !empty($arr[3])){
            $canshu = substr($arr[3] , 1);
            $can    = $arr[3];
        }
    }
    if($page) $url_page = '_'.$page;


    // 文章ID不存在
    if(!$contentid) {
        show_404() ;
    }

    // 法规库旧链接跳转到新链接
    if($type == 'fagui' && $contentid <= 1415195 ){
        fagui($contentid) ;
    }
    $content = loader::model('content', 'system');
    $r = $content->get($contentid);

    preg_match('|http:\/\/([a-z0-9]+)\.kuaiji\.com\/(.*)|i', $_SERVER['HTTP_URLGAGA'], $m1);
    //财考下面的二级域名
    if($content->category[$r['catid']]['parentid'] == '11000' && strpos($_SERVER['HTTP_URLGAGA'], $r['url']) === false && $m1[1] == 'www'){
        if(!$r || intval($r['status']) !=6 ) {
            show_404() ;
        }
        $uri_lib = loader::lib('uri', 'system');
        $r1 = $uri_lib->content($contentid);
        $r = array_merge($r, $r1);
        $file_path = ROOT_PATH . "public/html/web/" . $r['realpath'];
        preg_match('|http:\/\/([a-z0-9]+)\.kuaiji\.com\/(.*)|i', $r['url'], $m);

        if(file_exists($file_path)){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location:http://{$m[1]}.kuaiji.com/".$contentid.$url_page.$can);
        }else{
            $article = loader::model('admin/article','article');
            if($article->html_write($contentid)){
                header("HTTP/1.1 301 Moved Permanently");
                header("Location:http://{$m[1]}.kuaiji.com/".$contentid.$url_page.$can);
            }
        }
        //header("HTTP/1.1 301 Moved Permanently");
        //header("Location:http://kuaijicongye.kuaiji.com/".$contentid.$url_page.$can);
        exit;
    }

    // 文章不存在或者文章状态 404 ;
    if(!$r || intval($r['status']) !=6 ) {
        show_404() ;
    } else{

        if($type == 'fagui'){
            if( !in_array( $r['catid'] , array('17500'))){
                show_404() ;
            }
        } else if($type == 'shiwu'){
            if(!in_array( $r['catid'] , array('16000','17000'))){
                show_404() ;
            }

        } else {
            if( !in_array( $r['catid'] , array('11100','11200','11300','11400','11500','11600','13000','13100','12300','12900','12700','12600','11700','13400','13200','13300','12100','11900','11800','12000','12800','12500','12200','12400','15000','17600','17700','15100'))){
                show_404() ;
            }
        }

        $file_path = '' ;
        $page = $page * 1;
        // 判断文章的首页存在时，就直接返回页面数据
        kuaiji_article_show($contentid , $file_path , $page);

        $template = factory::template();
        $config = config::get('config');
        $system = setting::get('system');
        $template->assign('CONFIG',  $config);
        $template->assign('SYSTEM',  $system);

        switch($r['modelid']){
            case '1' :
                $article = loader::model('admin/article','article');
                if($article->html_write($contentid)){
                    kuaiji_article_display($file_path) ;
                }
                break;
            case '2' :
                $picture = loader::model('admin/picture','picture');
                if($picture->html_write($contentid)){
                    kuaiji_article_display($file_path) ;
                }
                break;
            case '4' :
                $video = loader::model('admin/video','video');
                if( $video->html_write($contentid)){
                    kuaiji_article_display($file_path) ;
                }
                break;
            default :
                // 模型不支持生成;
                show_404() ;
                break;
        }
    }

    // 生成页面失败，返回404页面
    show_404() ;
}


/**
 * 验证文章的首页是否存在，如存在直接输出页面，不需要判断分页情况（避免外部访问无效的分页时每次都执行生成静态化的动作）
 * 否则当URL是分页时，获取分页页面的文件地址
 *
 * @param $contentid  文章ID;
 * @param $file_path  文件路径值;
 * @param $page       页码;
 */
function kuaiji_article_show($contentid , &$file_path , $page = 1){
    $uri_class = loader::lib('uri', 'system');
    // 获取文章的第一页的文件路径
    $p = $uri_class->content($contentid);
    if($p['path']){
        $file_path = $p['path'] ;
        if($page > 1){
            $p_p = $uri_class->content($contentid , $page);

            // URL是分页时,保存当前分页的文件地址
            $file_path = $p_p['path'] ;

            // 首页存在，分页不存在时，返回404
            if(file_exists($p['path'])){
                if(!file_exists($p_p['path'])){
                    show_404() ;
                }else{
                    kuaiji_article_view($p_p['path']) ;
                }
            }

            // 首页不存在时，直接return 到生成动作
            return true;
        }

        kuaiji_article_display($p['path']) ;
    }
}

/**
 * 法规库的详细页，对应调整到新版的news域名下
 *
 * @param $contentid ;
 */
function fagui($contentid){
    $arr = include dirname(__FILE__) . '/rewrite/fagui.php' ;
    if(isset($arr[$contentid]) && $new_contentid = $arr[$contentid]){
        $url = WWW_URL . 'fagui/' . $new_contentid ;
        go($url) ;
    }else{
        show_404() ;
    }
}
/**
 * 全拼跳short
 * @param $citys
 * @param $pinyin
 * @return mixed
 */
function getCity_short($pinyin)
{
    $citys =  common_data('area_city', 'system');
    foreach ($citys as $v) {
        if ($pinyin != $v['short'] && $pinyin == $v['pinyin'])return $v['short'];
    }
}

/**
 * 全拼跳short
 * @param $citys
 * @param $pinyin
 * @return mixed
 */
function getCity_pinyin($short)
{
    $citys =  common_data('area_city', 'system');
    foreach ($citys as $v) {
        if ($short == $v['short'])return $v['pinyin'];
    }
}

/**
 * 301转向
 *
 * @param $url ;
 */
function go($url){
    header("HTTP/1.1 301 Moved Permanently");
    header('location:'.$url);
    exit();
}

/**
 * 获取城市下面的区
 */
function getCity_region($cityid)
{
    $citys =  common_data('area_letter', 'system');
    $result = array();
    foreach ($citys as $v) {
        if ($cityid == $v['city_id'] && $v['level'] == 3)$result[$v['region_id']] = $v;
    }
    return $result;
}


/**
 * 获取城市下面区县的培训班数据
 */
function getCity_region_seller($cityid)
{
    $citys = getCity_region($cityid);
    $region = implode(',', array_keys($citys));

    $schools = get_schools($region);
    foreach($schools as $v) {
        $i = 1;
        foreach ($v as $_v) {
            $seller_id[] = $_v;
            if ($i == 4)break;
            $i++;
        }
    }
    $seller_id = implode(',', $seller_id);
    $sellers = get_seller($seller_id);
    foreach($schools as $k=>$v) {
        $i = 1;
        foreach ($v as $_v) {
            $sellers[$_v] && $result[$k][$_v] = $sellers[$_v];
            if ($i == 4)break;
            $i++;
        }
    }
    return array('region'=>$citys, 'data'=>$result);
}

/**
 * 根据区县ID 获取校区数据
 */

function get_schools($region) {
    $brand_db = config::get('brand_db');
    $db = factory::db($brand_db);
    $sql = "SELECT seller_id,region_id FROM #table_mall_school WHERE `region_id` in($region) AND `able_status`=1 AND iscollect=0 GROUP BY seller_id";
    $lists = $db->select($sql);
    foreach ($lists as $v) {
        $result[$v['region_id']][$v['seller_id']] = $v['seller_id'];
    }
    return $result;
}


/**
 * 根据机构ID获取机构数据
 */
function get_seller($seller_id)
{
    $db = factory::db();
    $sql = "SELECT seller_id,seller_name FROM #table_mall_seller WHERE seller_id in($seller_id) AND able_status=1 AND check_status=1 AND iscollect=0";
    $lists = $db->select($sql);
    foreach ($lists as $v) {
        $result[$v['agency_id']]['url'] = xuexiao_url($v['seller_id']);
        $result[$v['agency_id']]['title'] = $v['seller_name'];
        $result[$v['agency_id']]['thumb'] = get_pic_by_id(31, $v['seller_id'],'');
    }

    return $result;
}
/**
 * 返回页面文件的数据
 *
 * @param $file 文件路径;
 */
function kuaiji_article_display($file){
    if(file_exists($file)){
        kuaiji_article_view($file) ;
    }
}

/**
 * 输出页面数据
 *
 * @param $file 文件路径;
 */
function kuaiji_article_view($file){
    ob_start() ;
    echo file_get_contents($file) ;
    ob_end_flush() ;
    exit;
}

/**
 * 格式化python->json 的数据！ 城市数据
 * @param $tmp_city
 * @return array|bool
 */
function formatting2city_dfz($tmp_city)
{
    //根据地区获取培训班数据
    //if (file_exists('/opt/wwwroot/kuaiji/project/data/cache/index/'.$tmp_city.'.json'))$city_data = json_decode(file_get_contents('/opt/wwwroot/kuaiji/project/data/cache/index/'.$tmp_city.'.json'));
    if (file_exists(CACHE_PATH.'index/'.$tmp_city.'.json'))$city_data = json_decode(file_get_contents(CACHE_PATH.'index/'.$tmp_city.'.json'), true);
    $_list = array();
    if ($city_data) {
        foreach ($city_data as $k=> $_val) {
            foreach ($_val as $k1=>$val) {
                $vals = array();
                if ($k == 'jigou') {
                    $vals['url'] = xuexiao_url($val['seller_id']);
                    $vals['title'] = $val['seller_name'];
                    $vals['thumb'] = get_pic_by_id(31, $val['seller_id'],'');
                } else {
                    $vals['url'] = kuaiji_url('product_detail/detail' , array('product_id'=>$val['product_id']) , 'item');
                    $vals['thumb'] = get_pic_by_id($val['product_type'], $val['product_id'], 'l' , true );
                    $vals['title'] = $val['product_title'];
                    $vals['subtitle'] = $val['agency_title'];
                    $vals['suburl'] = agency_url($val['agency_id']);
                    $vals['price'] = $val['kuaiji_low_price'];
                    $vals['sale_nums'] = $val['sale_nums'];
                }
                $_list[$k][$k1] = $vals;
            }
        }
        $_list = promote_dfz($tmp_city, $_list);
        $n = count($_list['jigou']);
        if ($n != 5) {
            $_n = 5 - $n;
            for ($i = 0; $i < $_n; $i++) {
                $vals['url'] = 'http://www.kuaiji.com/zhaoshang';
                $vals['title'] = '会计网机构营销平台';
                $vals['thumb'] = IMG_URL. 'images/index_v2/jigou.jpg';
                $_list['jigou'][$n+$i] = $vals;
            }

        }
    }
    return $_list;

}

/**
 * 获取推广信息
 * @param $cityid
 * @param $city_data
 * @return bool
 */
function promote_dfz($cityid, $city_data)
{


    $key = md5('cityid_'.$cityid);
    //if (!$cache = $this->cache->get($key) && empty($_GET['CC'])) {
    $_karr = array(100=>0,80=>1,60=>2,40=>3,20=>4,0=>5);
    //扩展属性的城市ID与会计网地区城市ID不同 所以切切掉后面的O
    $cid = preg_replace('/0+$/','',$cityid);
    $_ENV['extapp'] = '';
    $config = config::get('db');
    //$config = require_once ROOT_PATH .'config/db.php';
    $db = factory::db($config);

    $list = $db->select("SELECT title,thumb,url,weight,subtypeid FROM cms_content WHERE subtypeid in (11001,11002) AND status=6 AND catid=21900 AND zoneid=$cid LIMIT 10");
    foreach($list as $val) {
        $val['promote'] = 1;
        if ($val['subtypeid'] == 11001) {
            $city_data['jigou'][$_karr[$val['weight']]] = $val;
        } else if ($val['subtypeid'] == 11002){
            $city_data['peixun_hot'][$_karr[$val['weight']]] = $val;
        }
    }

    //$this->cache->set($key, $city_data, 3600);
    // }
    return  $city_data;
}

/**
 * 注册会计网用户
 * bbs and brand
 * @param $name
 * @param $password
 * @param $email
 * @param $groupid  论坛用户组ID
 */
function kj_register($name, $password, $email, $groupid = 10)
{

    if (!$name || !$password || !$email)return array('error'=>'unknow');
    $bbs_api = load_rpc('bbsonline');
    $data = $bbs_api->register($groupid, array('name'=>$name, 'password'=>$password, 'email'=>$email));
    if ($data['error'])return $data;
    $add_data = array(
        'user_id' => $data['uid'],
        'name' => $data['username'],
        'email' => $data['email'],
        'mobile' => 0,
        'real_name' => '',
        'sex' => 0,
        'reg_time' => time(),
        'log_time' => time()
    );
    loader::model('member', 'exam')->insert($add_data);
    return $data;
}



/**
 * cache 单例
 *
 * @return cache
 */
function redis($prefix = '')//恢复14年前的兼容20150119
{
    import('cache.cache');
    $extapp = $_ENV['extapp'];
    $_ENV['extapp'] = '';
    $config = config('cache');
    $config['storage'] = 'redis';
    $config['path'] = CACHE_PATH;
    if (!empty($config['storage']))
    {
        $_config = config($config['storage']);
        $config[$config['storage']] = $_config;
        //增加源选择
        $config['caching'] = 1;
    }
    $_ENV['extapp'] = $extapp;
    $config['prefix'] = $prefix ? $prefix : 'exam_';
    return new cache($config);
}

/**
 * 题库获取redis数据
 * @param $key
 */
function exam_redis_get($key)
{

    $redis = redis();
    $val = $redis->get($key);
    return json_decode($val, true);
}


function referto($id = "referto", $name = "options", $value = '',$width = '150px' )
{
    $values = array();
    if(strpos($value, ",")){//其实就是通过一个参数传递多个价值 referto,typeid,zoneid
        $values = explode(",", $value);
        $value = $values[0];
    }

    echo '<script>$(function(){$(\'#'.$id.'\').selectree();})</script>';
    $echo_typeid =$values[1]? "block":"none";
    $echo_subtypeid =$values[2]? "block":"none";
    $echo_zoneid =$values[3]? "block":"none";
    echo '<span id="typeid_span" style="display:'.$echo_typeid.'">&nbsp;&nbsp;<input id="typeid_init_value" value="'.$values[1].'" type="hidden" /><input id="typeid_init_id" value="typeid_'.$id.'" type="hidden" /><input id="typeid_init_name" value="typeid" type="hidden" /><input id="typeid_init_width" value="'.$width.'" type="hidden" />栏目扩展：<span id="typeid_selector"></span></span>';
    echo '<span id="subtypeid_span" style="display:'.$echo_subtypeid.'">&nbsp;&nbsp;<input id="subtypeid_init_value" value="'.$values[2].'" type="hidden" /><input id="subtypeid_init_id" value="subtypeid_'.$id.'" type="hidden" /><input id="subtypeid_init_name" value="subtypeid" type="hidden" /><input id="subtypeid_init_width" value="'.$width.'" type="hidden" />内容扩展：<span id="subtypeid_selector"></span></span>';
    echo '<span id="zoneid_span" style="display:'.$echo_zoneid.'">&nbsp;&nbsp;<input id="zoneid_init_value" value="'.$values[3].'" type="hidden" /><input id="zoneid_init_id" value="zoneid_'.$id.'" type="hidden" /><input id="zoneid_init_name" value="zoneid" type="hidden" /><input id="zoneid_init_width" value="'.$width.'" type="hidden" />地区属性：<span id="zoneid_selector"></span></span>';
}


/**
 * 微信授权并插入用户
 * @return array
 */
function wechat_token()
{

    //return array('openid'=>'o3XbwjkBy__6VqMRXKrKsWxN7DEQ', 'nickname'=>'Silen', 'headimgurl'=>'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDZyllaic8vQv3sDXw9YV1uqFQTh5mgNXdH1BaDzZyzXyOjOaiafQUZxery2UCHBCCgquetPIjNx8qDM5bL4LYqvoCPlfaiagpDFE/0');
    $nowurl = 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $appid = "wx60ed64ba065668bf";
    $secret = "72de5fad165cf9ec065bced50e11ead7";

    if ($_GET['callback'])$code = $_GET['code'];

    import('core.cookie');
    $Cookie = new cookie('KJ_', '/', 'm'.BASE_DOMAIN);
    $wechat_openid = $Cookie->get('wechat_openid');
    $wechat_member_model = loader::model('wechat_member', 'wap');
    /* if(strtolower($_SERVER['HTTP_HOST']) == 'm.p.kuaiji.com' && !$wechat_openid){
        $r = $wechat_member_model->get('', 'openid', 'rand()');
        $wechat_openid = $r['openid'];
        $Cookie->set('wechat_openid', $wechat_openid, 10*3600);
    } */

    if ($wechat_openid) {
        $r = $wechat_member_model->get(array('openid'=>$wechat_openid), 'intro');
        $info = unserialize($r['intro']);
        $info['nickname'] = is_base64_encode($info['nickname']) ?  base64_decode($info['nickname']) : $info['nickname'];
        return $info;
    }

    if($code){
        $url ="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret=$secret&code={$code}&grant_type=authorization_code";
        $r = iGet($url);
        $r = json_decode($r, true);
        if ($r['access_token']) {
            $userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$r['access_token']}&openid={$appid}&lang=zh_CN";
            $user_info = iGet($userinfo_url);
            $user_info = json_decode($user_info, true);
            if ($user_info['openid']) {
                $Cookie->set('wechat_openid', $user_info['openid'], 10*3600);
                $user_info['nickname'] = base64_encode($user_info['nickname']);
                $info = array(
                    'openid' => $user_info['openid'],
                    'unionid' => $user_info['unionid'],
                    'appid' => $appid,
                    'nickname' =>$user_info['nickname'],
                    'created' => time(),
                    'intro' => serialize($user_info),
                );
                if (!$r = $wechat_member_model->get(array('openid'=>$user_info['openid']), 'openid')) {
                    $wechat_member_model->insert($info);
                } else {
                    unset($info['openid']);
                    $wechat_member_model->update($info,array('openid'=>$r['openid']));
                }
                $info['openid'] = $user_info['openid'];
                $info['nickname'] =is_base64_encode($user_info['nickname']) ?  base64_decode($user_info['nickname']) : $user_info['nickname'];
                return $info;
            }
        }
    }

    $go_url = strpos($nowurl , '?') !== false ? $nowurl."&callback=1" : $nowurl."?callback=1";
    $REDIRECT_URI =  urlencode($go_url);
    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri=$REDIRECT_URI&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
    header("Location:".$url);
}
function is_base64_encode($str)
{
    if ($str == base64_encode(base64_decode($str))) {
        return  true;
    }else{
        return false;
    }
}
/**
 * 判断是否是合格的手机客户端
 *
 * @return boolean
 */
function is_mobile()
{
    return false;
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    if (preg_match('/playstation/i', $user_agent) OR preg_match('/ipad/i', $user_agent) OR preg_match('/ucweb/i', $user_agent))
    {
        return false;
    }

    if (preg_match('/iemobile/i', $user_agent) OR preg_match('/mobile\ssafari/i', $user_agent) OR preg_match('/iphone\sos/i', $user_agent) OR preg_match('/android/i', $user_agent) OR preg_match('/symbian/i', $user_agent) OR preg_match('/series40/i', $user_agent))
    {
        return true;
    }

    return false;
}

/**
 * 判断是否处于微信内置浏览器中
 *
 * @return boolean
 */
function in_weixin()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    if (preg_match('/micromessenger/i', $user_agent))
    {
        return true;
    }

    return false;
}



/**
 * 判断是否wechat浏览器访问
 *
 */
function is_wechat($st = '')
{
    if (!in_weixin()){
        if ($st)return true;
        header("Content-type: text/html; charset=utf-8");
        echo '<link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css"><div class="page_msg"><div class="inner"><span class="msg_icon_wrp"><i class="icon80_smile"></i></span><div class="msg_content"><h4>请在微信客户端打开链接</h4></div></div></div>';
        exit;
    }

}

/**
 * get请求
 * @param $url
 * @param array $data
 * @param array $header
 * @param array $cookie
 * @return mixed
 */
function iGet($url,$data = array(),$header = array(),$cookie = array()){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; InfoPath.2)");
    curl_setopt($curl, CURLOPT_REFERER,'http://m.kuaiji.com');
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    if(!empty($cookie)) curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    if(!empty($header)) curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $tmpInfo = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);
    }
    curl_close($curl);
    return $tmpInfo;
}


/**
 * 判断是否手机端
 * @return bool
 */
function isMobile() {
    if(isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if(isset ($_SERVER['HTTP_VIA'])) {
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    if(isset($_SERVER['HTTP_USER_AGENT'])) {
        //此数组有待完善
        $clientkeywords = array ('nokia','android','ericsson','mot','samsung','htc','sgh','lg','sharp','sie\-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','up\.browser','smartphone','obigo','au\.browser','wxd\.mms','wxdb\.browser','up\.link','km\.browser','semc\-browser','mini','sonyericsson','nec','benq','amoisonic','amoi','capitel','mitsu','motorola','wapper','eg900','cect','compal','kejian','bird','g900\/v1\.0','arima','ctl','tdg','daxian','dbtel','eastcom','pantech','dopod','haier','konka','soutec','sagem','sec','sed','emol','inno55','zte','windows ce','wget','java','curl','opera');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if(preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }

    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }

    return false;
}

/************** start  aliyun openSearch ***************************/

function getCloudsearchClient($debug=false , $isgzip = true) {
    $config = config('aliyun');
    loader::import('opensearch.CloudsearchClient', ROOT_PATH . 'extension/');
    $cloudsearchClient = new CloudsearchClient(
        $config['accessKeyId'],
        $config['secret'],
        array(
            'host' => 'http://opensearch.aliyuncs.com',
            'gzip' => $isgzip ,
            'debug' => $debug,
        ),
        'aliyun'
    );
    return $cloudsearchClient ;
}

function getCloudsearchSearch($debug=false , $isgzip = true) {
    loader::import('opensearch.CloudsearchSearch', ROOT_PATH . 'extension/');
    $client = getCloudsearchClient($debug, $isgzip);
    $search = new CloudsearchSearch($client);
    // 指定搜索返回的格式。
    $search->setFormat('json');
    return $search ;
}

function getCloudsearchDoc($app_name, $debug=false , $isgzip = true) {
    loader::import('opensearch.CloudsearchDoc', ROOT_PATH . 'extension/');
    $client = getCloudsearchClient($debug, $isgzip);
    $doc= new CloudsearchDoc($app_name, $client);
    return $doc ;
}

function addSearchIndex($cloudsearchSearch, $index){
    $index_arr = array_map('trim', explode(';', $index));
    if( BASE_DOMAIN != '.kuaiji.com' ){
        foreach($index_arr as $k=>$v){
            $index_arr[$k] = 'p_' . $v ;
        }
    }
    $cloudsearchSearch->addIndex( implode(';', $index_arr) );
}

/**
 * 首页会计培训
 * @param int cityid  地区id
 * @return array
 */
function peixun($cityid)
{
    $extapp =  $_ENV['extapp'];
    $_ENV['extapp'] = '';
    $zoneid = $cityid/100;//content表中地区id需要去掉后面的两个0
    $db = & factory::db();
    $_ENV['extapp'] = $extapp;
    $sql = "select `thumb`,`title`,`url` from kj_cms.cms_content where catid=21900 and modelid=3 and status=6 and subtypeid=11002 and zoneid={$zoneid} order by weight desc limit 4";
    $result = $db->select($sql);
    for($i=0; $i<count($result); $i++){
        $peixun_hot[$i]['title'] = $result[$i]['title'];
        $peixun_hot[$i]['thumb'] = $result[$i]['thumb'];
        $peixun_hot[$i]['url'] = $result[$i]['url'];
    }
    $data['peixun_hot'] = $peixun_hot;
    $province = area_get_info($cityid);//获取省的信息
    $additionalParameters = array(
        'sort' => 'random_'.mt_rand(1,1000).' desc',
        'fq' => "iscollect:0 && city_id:".$cityid,
        '__fp' => "news_list"
    );
    $param['city_query'] = array('_func' => 'query', 'course_v2', '', 0, 14, $additionalParameters);
    $additionalParameters = array(
        'sort' => 'random_'.mt_rand(1,1000).' desc',
        'fq' => "iscollect:0 && province_id:".$province['province_id'],
        '__fp' => "news_list"
    );
    $param['province_query'] = array('_func' => 'query', 'course_v2', '', 0, 14, $additionalParameters);
    $additionalParameters = array(
        'sort' => 'enroll_num desc',
        'fq' => "iscollect:0",
        '__fp' => "news_list"
    );
    $param['enroll_query'] = array('_func' => 'query', 'course_v2', '', 0, 14, $additionalParameters);
    //搜
    $so_result = so('multi_func', array($param));
    $arr = array();
    foreach($so_result as $k=>$v){
        foreach($v['response']['docs'] as $kk=>$vv){
            $docs[$k][$vv['id']]['title'] = $vv['title'];
            $docs[$k][$vv['id']]['url'] = $vv['url'];
            $docs[$k][$vv['id']]['thumb'] = $vv['img_url'];
            $docs[$k][$vv['id']]['price'] = $vv['price'];
            $docs[$k][$vv['id']]['show_price'] = $vv['show_price'];
            $docs[$k][$vv['id']]['seller_id'] = $vv['seller_id'];
        }

        if($docs[$k]){
            if(count($arr) < 14){
                $arr += $docs[$k];
            }
        }
    }

    foreach($arr as $v){
        $num = count($data['peixun_hot']);
        if($num < 9){
            $data['peixun_hot'][$num] = $v;
            //机构
            $additionalParameters = array();
            $additionalParameters['fq'] = " id:{$v['seller_id']}"; //机构id
            $additionalParameters['fl'] = " title";
            $additionalParameters['sort'] = "add_time desc";
            $so_param = array('seller', '', 0, 1, $additionalParameters);
            $so_result_seller = so('query', $so_param);
            $data['peixun_hot'][$num]['subtitle'] = $so_result_seller['response']['docs'][0]['title'];
            $data['peixun_hot'][$num]['suburl'] = WWW_URL . 'xuexiao/'. $v['seller_id'];
        }else{
            $num1 = count($data['peixun_news']);
            $data['peixun_news'][$num1] = $v;
            $additionalParameters = array();
            $additionalParameters['fq'] = " id:{$v['seller_id']}"; //机构id
            $additionalParameters['fl'] = " title";
            $additionalParameters['sort'] = "add_time desc";
            $so_param = array('seller', '', 0, 1, $additionalParameters);
            $so_result_seller = so('query', $so_param);
            $data['peixun_news'][$num1]['subtitle'] = $so_result_seller['response']['docs'][0]['title'];
            $data['peixun_news'][$num1]['suburl'] = WWW_URL . 'xuexiao/'. $v['seller_id'];
            if($num1 >= 4) {break;}
        }
    }
    return $data;
}

/************** end  aliyun openSearch ***************************/

/**
 * 写入html文件(静态化html专用)
 *
 * @param string $file 文件名
 * @param string $data 文件内容
 * @return int
 */
function write_html($file, $data)
{
    // start: 阻止锁定文件的静态化生成，By mohy - 2015-01-20
    $relative_path = str_replace(ROOT_PATH, '', $file);
    $lock_file = @include_once ROOT_PATH . 'data' . DS . 'backup' . DS . 'static_file' . DS . 'lock.php';
    if($lock_file && in_array($relative_path, $lock_file))
    {
        return false;
    }
    // end

    $dir = dirname($file);
    $flag = true;
    if (!is_dir($dir)) {
        $flag = mkdir($dir, 0755, true);
    }
    return $flag ? file_put_contents($file, $data) : false ;
}

/*************** checkLeve ******************************/

function checkLevel($exper)
{
    $_lv = config::get('my', 'lv');
    arsort($_lv);
    foreach ($_lv as $k=>$v){
        if ($exper >= $v)return $k;
    }
    return 0;
}


function checkUpgrade($exper, $lv , $uid = null)
{
    $configLv = config::get('my', 'lv');
    $_lv = checkLevel($exper);
    if ($_lv != $lv) {
        if ($uid) {
            loader::model('member_front', 'member')->update(array('exper'=>$exper, 'level'=>$_lv), $uid);
        }
    }
    $nextExper = $lv+1 >= 5 ? $configLv[5] : $configLv[$lv+1];
    $nowExper = $exper - $configLv[$_lv];
    $percentage = round($nowExper/$nextExper, 2) * 100 . '%';
    return array('percentage' => $percentage, 'nowLv'=>$_lv, 'nextExper'=>$nextExper, 'nowExper'=>$nowExper);


}

/**
 * CDN不缓存
 */
function header_nocache(){
    header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
    header( 'Cache-Control: no-store, no-cache, must-revalidate' );
    header( 'Cache-Control: post-check=0, pre-check=0', false );
    header( 'Pragma: no-cache' );
}

/**
 * 机构平台url解析
 */

function mall_parse_url($uri){
    $rewrite = array(

        '\/xuexiao\/([1-9][0-9]*)\/?' => array('ca' => '/index', 'var' => array('seller_id'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/([a-z]+)\/?' => array('ca' => '/', 'var' => array('seller_id','action'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/([a-z]+)_([1-9][0-9]*)\/?' => array('ca' => '/', 'var' => array('seller_id','action','page'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/news([1-9][0-9]*)\/?' => array('ca' => '/news', 'var' => array('seller_id','typeid'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/news([1-9][0-9]*)_([1-9][0-9]*)\/?' => array('ca' => '/news', 'var' => array('seller_id','typeid','page'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/news\/([1-9][0-9]*)' => array('ca' => '/news_detail', 'var' => array('seller_id','article_id'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/fenxiao([1-9][0-9]*)\/?' => array('ca' => '/fenxiao', 'var' => array('seller_id','region_id'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/fenxiao([1-9][0-9]*)_([1-9][0-9]*)\/?' => array('ca' => '/fenxiao', 'var' => array('seller_id','region_id','page'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/fenxiao\/([1-9][0-9]*)' => array('ca' => '/fenxiao_detail', 'var' => array('seller_id','school_id'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/kecheng_([1-9][0-9]*)\/?' => array('ca' => '/kecheng', 'var' => array('seller_id','page'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/kecheng([1-9][0-9]*)_([1-9][0-9]*)\/?' => array('ca' => '/kecheng', 'var' => array('seller_id','catid','page'), 'param'=>array()),
        '\/xuexiao\/([1-9][0-9]*)\/([a-z]+)([1-9][0-9]*)\/?' => array('ca' => '/', 'var' => array('seller_id','action','catid'), 'param'=>array()),

        '\/kecheng\/([1-9][0-9]*)' => array('ca' => '/kecheng_detail', 'var' => array('course_id'), 'param'=>array()),
        '\/ban\/([1-9][0-9]*)' => array('ca' => '/ban_detail', 'var' => array('class_id'), 'param'=>array()),

    );

    $param_arr = explode('?', $uri);
    if (!empty($param_arr[1])) {
        $queryParts = explode('&', $param_arr[1]);
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $_GET[$item[0]] = $item[1];
        }
    }

    $uri = array_shift(explode('?', $uri));

    $flag = false ;
    foreach ($rewrite as $pattern => $val) {
        if( preg_match('/^' . $pattern . '$/', $uri, $matches) ){
            unset($matches[0]);
            foreach($val['var'] as $var_name){
                $_GET[$var_name] = array_shift($matches);
            }
            $_GET = array_merge($_GET, $val['param']) ;
            $ca_arr = @explode('/', $val['ca']);
//            !empty($ca_arr[0]) && $_GET['controller'] = $ca_arr[0] ;
            !empty($ca_arr[1]) && $_GET['action'] = $ca_arr[1] ;
            $flag = true ;
            break ;
        }
    }
    if( ! $flag ){
        show_404();//todo
    }

    $_GET['app'] = 'kuaiji';
    $_GET['controller'] = 'xuexiao';
    $_REQUEST = array_merge($_REQUEST, $_GET);

}

/**
 * 获取全部地区名称和id
 */
function diqu(){

    $area_cache = common_data('area_letter', 'system');
    $area_hot = array();
    $area_list = array();
    foreach($area_cache as $v){
        $v['alias'] = mb_substr($v['alias'],2,20,'utf-8');
        if( $v['alias'] == '省直辖行政区' ){
            continue ;
        }
        if( $v['level'] ==2 ){
            if( $v['is_hot']==2 ){
                $area_hot[] = array(
                    'id'=>$v['id'],
                    'alias'=>$v['alias'],
                    'short'=>$v['short'],
                );
            }
            $area_list[$v['first_letter']][] = array(
                'id'=>$v['id'],
                'alias'=>$v['alias'],
                'short'=>$v['short'],
            );
        }

    }
    $result = array('area_hot'=>$area_hot, 'area_list'=>$area_list);
    return $result;

}

/**
 * WWW新列表页url  www.kuaiji.com/peixun/gz_dongpu_p3s2
 * @param $path 'peixun' 'xuexiao'
 * @param $param array(
'city_py'=>'gz',//城市拼音
'cat_py'=>'kuaijicongye',//分类拼音
'region_py'=>'tianhe',//区拼音
'sd_py'=>'dongpu',//商圈拼音
'page'=>2,//页码
'sort'=>3//排序
);
 * @return string
 */
function www_list_url($path, $param){
    $url = WWW_URL . $path  ;
    if( !empty($param['cat_py']) ){
        $url .= '/' .$param['cat_py'] ;
    }
    $url .= '/' . $param['city_py'] ;
    if( !empty($param['sd_py']) ){
        $url .= '_'.$param['sd_py'] ;
    }elseif( !empty($param['region_py']) ){
        $url .= '_'.$param['region_py'] ;
    }
    $filt = '' ;
    if( isset($param['page']) &&  $param['page'] > 1 ){
        $filt .= 'p'.$param['page'] ;
    }
    if( isset($param['sort']) &&  $param['sort'] > 1 ){
        $filt .= 's'.$param['sort'] ;
    }
    if( $filt ){
        $url .= ($param['city_py'] ? '_' : '' ) . $filt ;
    }

    return trim($url, '/') ;
}

/**
 * 百度短网址api
 * @param $url
 * @return mixed
 */
function baiduDz($url)
{
    $return = request('http://dwz.cn/create.php', array('url'=>$url));

    if ($return['httpcode'] == 200)$content = json_decode($return['content'], true);
    return $content ? $content['tinyurl'] : $url;
}

function store2subDomain()
{
    $subDomain = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.') );
    if ($subDomain == 'store') {

        $subDomain = substr($_SERVER['HTTP_URLGAGA'], 7, strpos($_SERVER['HTTP_URLGAGA'], '.') - 7 );
    }
    define('SUB_DOMAIN', $subDomain);
}

/**
 * 输出信息
 *
 * @param string  $message 信息
 * @param string  $url     返回链接
 */
function _showMsg($message, $url = '', $ms = 2000, $success = false) {

    $handler = factory::template();
    $handler->assign('message', $message);
    $handler->assign('url', $url);
    $handler->assign('CONFIG', config::get('config'));
    $handler->assign('ms', $ms);
    $handler->assign('success', $success);
    $handler->display('site/showmessage');
    exit;
}



function get_categroy_childids($id, $category)
{
    $catids = explode(',', $category[$id]['childids']);
    foreach ($catids as $catid) {
        $_categroy[$catid] = $category[$catid];
    }
    return $_categroy;
}

/**
 * 动态缩略图
 * @param $url 原图url( 仅支持 img.kuaiji.com/... )
 * @param $width 缩略图 宽
 * @param $height 缩略图 高
 * @return mixed
 */
function img_thumb($url, $width, $height){
    if( !$url ){
        return '' ;
    }
    $pos = strrpos($url, '.');
    $replace = ($width && $height) ? "_{$width}x{$height}." : ''.'';
    $thumb_url =  substr_replace($url, $replace, $pos, 1);
    return str_replace('.kuaiji.com', '.kuaiji.com/resize', $thumb_url);
}