<?php
/**
 * 数据字典
 *
 * @aca 数据字典
 */
class controller_admin_dictionary extends dictionary_controller_abstract
{
	protected $db;
	
	public function __construct(& $app)
	{
		parent::__construct($app);
	}

    /**
     * 数据字典
     *
     * @aca 数据字典
     */
	public function index(){
		$db = & factory::db();
		$config = & factory::config();
		$fields = array();
		$list = array();
		$tables_info = array();
		$info = array();
		$info_table = array();
		$used_tables = array();
		$table = array();
		$db_name = config::get('db','dbname');

		$tables=$db->list_tables();
		/**
		 * 遍历所有表名
		 * $fields[$table]=$field; 是各个表名为下标，将各个字段信息为值的一个三维数组
		 * $tabl[$table]=$tab; 是各个表名为下标，将各个表的注释信息为值的一个三维数组
		 */
		foreach ($tables as $value) {
			if (in_array($value, $used_tables)) continue;
			$used_tables[] = $value;
			$field=$db->select("SELECT COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_KEY,COLUMN_DEFAULT,EXTRA,COLUMN_COMMENT FROM information_schema.COLUMNS WHERE table_name='$value' and TABLE_SCHEMA='$db_name'");
			$fields[$value]=$field;
			$tab=$db->select("SELECT TABLE_NAME,TABLE_COMMENT FROM information_schema.tables WHERE table_name='$value' and table_schema='$db_name'");
			$table[$value]=$tab;
		}
		/**
		 *遍历表的数据信息，
		 *  $tables_info；是将表名为下标，表名注释信息为值的一个数组（有些数据是我们不想要的）。
		 * $info; 是将我们想要的信息取出来后拼装好的一个二维数组，表名为下标，表明注释为值。
		 * 
		 */
		foreach ($table as $k1 => $v1){
			foreach ($v1 as $k2 => $v2){
				$tables_info[$v2['TABLE_NAME']]=$v2['TABLE_COMMENT'];
				foreach ($tables_info as $k3 => $table_info){
					$info_table=explode(';',$table_info);
					$info[$k3]=$info_table['0'];
				}
			}
		}
		$this->view->assign('table_info',$info);
		$this->view->assign('fields',$fields);
		if(isset($_GET['download']))
		{
			header("content-type: application/octet-stream");
			header("content-disposition: attachment; filename=dictionary.html");
		}
		$this->view->display('dictionary');
	}
}