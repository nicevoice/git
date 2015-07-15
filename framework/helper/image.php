<?php
define('WATERMARK_DIR', ROOT_PATH.'resources'.DS.'watermark'.DS);

class image extends object 
{
	public $source,
	       $thumb_width = null,
	       $thumb_height = null,
	       $thumb_quality = 80,
	       $watermark,
	       $watermark_ext,
	       $watermark_im,
	       $watermark_width,
	       $watermark_height,
	       $watermark_minwidth = 300,
	       $watermark_minheight = 300,
	       $watermark_position = 9,
	       $watermark_trans = 65,
	       $watermark_quality = 80;
	       
	private $imginfo,
			$imagecreatefromfunc,
	        $imagefunc,
	        $animatedgif = 0;
	
	function set_source($source)
	{
		if (!file_exists($source)) return false;
		$this->source = $source;
		$this->animatedgif = false;
		$this->imginfo = @getimagesize($this->source);
		switch($this->imginfo['mime'])
		{
			case 'image/jpeg':
				$this->imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$this->imagefunc = (imagetypes() & IMG_JPG) ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$this->imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$this->imagefunc = (imagetypes() & IMG_GIF) ? 'imagegif' : '';
				break;
			case 'image/png':
				$this->imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$this->imagefunc = (imagetypes() & IMG_PNG) ? 'imagepng' : '';
				break;
		}
		if($this->imginfo['mime'] == 'image/gif') 
		{
			if($this->imagecreatefromfunc && !@imagecreatefromgif($this->source)) 
			{
				$this->errno = 1;
				$this->imagecreatefromfunc = $this->imagefunc = '';
				return false;
			}
			$this->animatedgif = strpos(file_get_contents($this->source), 'NETSCAPE2.0') === false ? false : true;
		}
		return !$this->animatedgif;
	}
	
	function set_thumb($width = null, $height = null, $quality = 80)
	{
		$this->thumb_width = is_null($width) ? null : intval($width);
		$this->thumb_height = is_null($height) ? null : intval($height);
		$this->thumb_quality = min($this->thumb_quality, intval($quality));
	}
	
	function thumb($source, $target = null)
	{
        if (! function_exists('imagecreatetruecolor')
           || ! function_exists('imagecopyresampled')
           || ! function_exists('imagejpeg')
           || ! $this->set_source($source)) return false;

        list($source_width, $source_height) = $this->imginfo;

        if((! $this->thumb_width && ! $this->thumb_height)
           || ($this->thumb_width >= $source_width && $this->thumb_height >= $source_height)) return false;

        $thumb_width = min($this->thumb_width, $source_width);
        $thumb_height = min($this->thumb_height, $source_height);

        $ratio_w = doubleval($thumb_width) / doubleval($source_width);
        $ratio_h = doubleval($thumb_height) / doubleval($source_height);
        $ratio = (! $ratio_w || ! $ratio_h) ? max($ratio_w, $ratio_h) : ($ratio_w < $ratio_h ? $ratio_w : $ratio_h);

        $target_width = $source_width * $ratio;
        $target_height = $source_height * $ratio;

        $thumb_width = $thumb_width ? min($thumb_width, $target_width) : $target_width;
        $thumb_height = $thumb_height ? min($thumb_height, $target_height) : $target_height;

        if ($thumb_width == $source_width && $thumb_height == $source_height)
        {
            if($target == $source)
            {
                return false;
            }
            else{
                @copy($source,$target);
                return true;
            }
        }

        if (is_null($target)) $target = $this->source;
		$imagecreatefromfunc = $this->imagecreatefromfunc;
		$img_photo = $imagecreatefromfunc($this->source);
		$thumb_photo = imagecreatetruecolor($thumb_width, $thumb_height);
		imagecopyresampled($thumb_photo, $img_photo, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
		clearstatcache();

		$imagefunc = $this->imagefunc;
		$result = $this->imginfo['mime'] == 'image/jpeg' ? $imagefunc($thumb_photo, $target, $this->thumb_quality) : $imagefunc($thumb_photo, $target);
		return $result;
	}

	/**
	 * 生成缩略图，严格按照指定尺寸生成图片
	 * 先按比例缩放，再把多余部分截取
	 * 如果无法满足指定尺寸，允许使用第二种缩略方案
	 * 
	 * @param string $source 原图
	 * @param string $target 目标图
	 * @param int    $cutpos 裁剪位置 ，0 表示从左上角开始，1表示从中间计算
	 * @param boolean $second 无法满足指定尺寸时，是否使用等比缩略图方案，默认不使用
	 * @param boolean $interlace  是否启用隔行扫描
	 * @return boolean
	 */
	function thumb_cut($source, $target = null,$cutpos=0, $interlace=true)
	{
		if (! function_exists('imagecreatetruecolor')
            || ! function_exists('imagecopyresampled')
            || ! function_exists('imagejpeg')
            || ! $this->set_source($source)) return false;
        list($img_w, $img_h) = $this->imginfo;
		$thumb_w = $this->thumb_width;
		$thumb_h = $this->thumb_height;
		if(!$thumb_w && !$thumb_h) return false;
		if($img_w <= $thumb_w && $img_h <= $thumb_h) return false;
        if(!$thumb_w || !$thumb_h) return $this->thumb($source,$target);
		if($img_w < $thumb_w) $thumb_w = $img_w;
        if($img_h < $thumb_h) $thumb_h = $img_h;
		if (is_null($target)) $target = $this->source;
		
		$pathinfo = pathinfo($source);
        $type = isset($pathinfo['extension']) ? $pathinfo['extension'] : 'jpeg';
		$type =	strtolower($type);
		$interlace = $interlace? 1:0;
        unset($info);
		// 计算缩放比例
		$x = $y = 0;
		if(($thumb_w/$thumb_h)>=($img_w/$img_h))
		{
			//宽不变,截高，从中间截取 y=
			$width	= $img_w;
			$height	= (int)$img_w*($thumb_h/$thumb_w);
			if($cutpos)
			{
				$y  = (int)($img_h-$height)*0.5;
			}
		}
		else
		{
			//高不变,截宽，从中间截取，x=
			$width	= (int)$img_h*($thumb_w/$thumb_h);
			$height	= $img_h;
			if($cutpos)
			{
				$x	= (int)($img_w-$width)*0.5;
			}
		}
		// 载入原图
        $createFun = $this->imagecreatefromfunc;
        $srcImg    = $createFun($source);
        //创建缩略图
        $thumbImg = imagecreatetruecolor($thumb_w, $thumb_h);
		// 新建PNG缩略图通道透明处理
        if('png'==$type) {
            imagealphablending($thumbImg, false);//取消默认的混色模式
            imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息
        }elseif('gif'==$type) {
        // 新建GIF缩略图预处理，保证透明效果不失效
            $background_color  =  imagecolorallocate($thumbImg, 0,255,0);  //  指派一个绿色
            imagecolortransparent($thumbImg,$background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
        }
		// 复制图片
		if(function_exists("ImageCopyResampled")){
			ImageCopyResampled($thumbImg, $srcImg, 0, 0, $x, $y, $thumb_w, $thumb_h, $width, $height);
		}else{
			ImageCopyResized($thumbImg, $srcImg, 0, 0, $x, $y, $thumb_w, $thumb_h, $width, $height);
		}
		ImageDestroy($srcImg);
		clearstatcache();
		
		// 对jpeg图形设置隔行扫描
        if('jpg'==$type || 'jpeg'==$type)
        {
        	imageinterlace($thumbImg,$interlace);
        }

        // 生成图片
        $imagefunc = $this->imagefunc;
        $result = $this->imginfo['mime'] == 'image/jpeg' ? $imagefunc($thumbImg, $target, $this->thumb_quality) : $imagefunc($thumbImg, $target);
		return $result;
	}
	
	function set_watermark($watermark, $minwidth = null, $minheight = null, $position = null, $trans = null, $quality = null)
	{
		if (!file_exists($watermark)) return false;
		
		$this->watermark = $watermark;
		$this->watermark_ext = strtolower(pathinfo($watermark, PATHINFO_EXTENSION));
		if (!in_array($this->watermark_ext, array('gif', 'png')) || !is_readable($this->watermark)) return false;
		
		$this->watermark_im	= $this->watermark_ext == 'png' ? @imagecreatefrompng($this->watermark) : @imagecreatefromgif($this->watermark);
		if(!$this->watermark_im) return false;
		
		$watermarkinfo	= @getimagesize($this->watermark);
		$this->watermark_width	= $watermarkinfo[0];
		$this->watermark_height	= $watermarkinfo[1];
		
		if (!is_null($minwidth)) $this->watermark_minwidth = intval($minwidth);
		if (!is_null($minheight)) $this->watermark_minheight = intval($minheight);
		if (!is_null($position)) $this->watermark_position = intval($position);
		if (!is_null($trans)) $this->watermark_trans = min($this->watermark_quality, intval($trans));
		if (!is_null($quality)) $this->watermark_quality = min($this->watermark_quality, intval($quality));
	}
	
	function watermark($source, $target = null)
	{
		if (!$this->set_source($source) || ($this->watermark_minwidth && $this->imginfo[0] <= $this->watermark_minwidth) || ($this->watermark_minheight && $this->imginfo[1] <= $this->watermark_minheight) || !function_exists('imagecopy') || !function_exists('imagealphablending') || !function_exists('imagecopymerge')) return false;
	
		if (is_null($target)) $target = $source;

		list($img_w, $img_h) = $this->imginfo;

		$wmwidth = $img_w - $this->watermark_width;
		$wmheight = $img_h - $this->watermark_height;
		if($wmwidth < 10 || $wmheight < 10) return false;

		switch($this->watermark_position)
		{
			case 1:
				$x = +5;
				$y = +5;
				break;
			case 2:
				$x = $wmwidth / 2;
				$y = +5;
				break;
			case 3:
				$x = $wmwidth - 5;
				$y = +5;
				break;
			case 4:
				$x = +5;
				$y = $wmheight / 2;
				break;
			case 5:
				$x = $wmwidth / 2;
				$y = $wmheight / 2;
				break;
			case 6:
				$x = $wmwidth;
				$y = $wmheight / 2;
				break;
			case 7:
				$x = +5;
				$y = $wmheight - 5;
				break;
			case 8:
				$x = $wmwidth / 2;
				$y = $wmheight - 5;
				break;
			default:
				$x = $wmwidth - 5;
				$y = $wmheight - 5;
		}
		
		$im = imagecreatetruecolor($img_w, $img_h);
		$imagecreatefromfunc = $this->imagecreatefromfunc;
		$source_im = @$imagecreatefromfunc($this->source);
		imagecopy($im, $source_im, 0, 0, 0, 0, $img_w, $img_h);
			
		if($this->watermark_ext == 'png')
		{
			imagecopy($im, $this->watermark_im, $x, $y, 0, 0, $this->watermark_width, $this->watermark_height);
		}
		else
		{
			imagealphablending($this->watermark_im, true);
			imagecopymerge($im, $this->watermark_im, $x, $y, 0, 0, $this->watermark_width, $this->watermark_height, $this->watermark_trans);
		}
		clearstatcache();
		
		$imagefunc = $this->imagefunc;
		$result = $this->imginfo['mime'] == 'image/jpeg' ? $imagefunc($im, $target, $this->watermark_quality) : $imagefunc($im, $target);
		@imagedestroy($im);
		return $result;
	}
}