<?php
/**
* 
*/
class AppConfig
{
	public static function Run()
	{
		self::_initPathConst();
		self::_ExcelFiles();
	}
	/*
		编辑输出路径，暂定读取当前路径下的excelFlie内的excel文件
		输出lua到当前文件下的outputLua
		输出xml到当前文件下的outputXml
	*/
	private static function _initPathConst()
	{
		define('ROOT_PATH', getCWD() . '/Demo/');// 自定义excel对应的根目录
		define('EXCEL_PATH', ROOT_PATH . 'excelFile/');
		define('LUA_PATH',ROOT_PATH . 'outputLua/');
		define('XML_PATH', ROOT_PATH . 'outputXml/');
		define('XML_ROOT_NAME', 'config');
	}
	//excel文件配置集合，key为xml表头文件名，value为excel文件名
	public static function _ExcelFiles()
	{
		$GLOBALS['excelfile'] = array(
								'gift'=>"gift.xlsx",
		        				'work'=>"work.xlsx"
							);
	}
}

