<?php
define('TEMP_DIR', UPLOAD_PATH.'temp'.DS);
class zip extends object 
{
	public $temp_dir=TEMP_DIR;
	public $name=array();

	function __construct($temp_dir='',$format='Y') 
	{
		
		import('helper.folder');
		$this->format=$format;
		if(!empty($temp_dir))
		{ 
			$this->temp_dir=$temp_dir;
		}
		if (!is_dir($this->temp_dir))
    	{
    		folder::create($this->temp_dir);
    	}
	}
	
	//解压并返回所有图片文件路径
    public function decompression($source,$target='')
    {
    	if(!empty($target)) $this->temp_dir=$target;
    	
        $zip = new ZipArchive;
        if ($zip->open($source)===true)
        {
            $zip->extractTo($this->temp_dir);
            $zip->close();
            import('helper.folder');
            
            $imgext=array('jpeg','jpg','gif','png');
			$imgArrs=folder::tree($this->temp_dir,'file');
			if(is_array($imgArrs))foreach($imgArrs as $val)
			{
					 $extname=strtolower(folder::file_ext_name($val));
					 if(in_array($extname,$imgext))
					 { 
						$Arrs[] =$val;
					 }
			}
        }
        return $Arrs;
    }
    
	//打包压缩
    public function compression($source, $target)
    {
        $zip = new ZipArchive;
        $source = is_array($source) ? $source : array($source);
        if ($zip->open($target, ZipArchive::CREATE) === true)
        {
            foreach ($source as $item)
                if (file_exists($item))
                    $this->addZipItem($zip, realpath(dirname($item)).'/', realpath($item).'/');
            $zip->close();
            return true;
        }
        return false;
    }
    
   
}