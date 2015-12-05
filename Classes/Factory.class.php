<?php
/**
* 
*/
class Factory
{
	
	public  function run(){
		require 'Classes/AppConfig.php';
		AppConfig::Run();
		self::readExcel();
	}
	private function readExcel($key = 0)
	{
		error_reporting(E_ALL);
		date_default_timezone_set('Asia/ShangHai');
		require_once 'Classes/PHPExcel/IOFactory.php';
		$reader = PHPExcel_IOFactory::createReader('Excel2007'); //设置格式
		foreach ($GLOBALS['excelfile'] as $key => $value) {
			$value = EXCEL_PATH . $value;
			if (!file_exists($value)) {
        		exit("not found $value.\n");
    		}
    		$PHPExcel = $reader->load($value); // 载入excel文件
		    $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		    $sheetName = $sheet->getTitle();
		    $allRow = $sheet->getHighestRow(); // 取得总行数
		    $allColumm = $sheet->getHighestColumn(); // 取得总列数
		    if ($key == 1) {
		    	$this->outputLuaByMap($sheet,$sheetName,$allRow,$allColumm);
		    }else{
		    	$this->outputLuaByList($sheet,$sheetName,$allRow,$allColumm);
		    }
		    $this->outputXml($sheet,$sheetName,$allRow,$allColumm,$key);
		}
	}
	private function outputLuaByList($sheet,$sheetName,$allRow,$allColumm)
	{
	    $fileName = $sheetName . ".lua";
	    $luaFile = fopen(LUA_PATH . $fileName, 'w') or die("Unable to open file!");
	    fwrite($luaFile, "$sheetName = {\n");
	    /** 循环读取每个单元格的数据 */
	    $str = "";
	    for ($column='A'; $column <= $allColumm; $column++) { 
	        $str = "\t".$sheet->getCell($column.(2))." = {";
	        for ($row = 4; $row <= $allRow; $row++) { 
	            if ($sheet->getCell($column.(3)) == "string") {
	                $str .= "\"".$sheet->getCell($column.$row)."\",";
	            }else{
	                $str .= $sheet->getCell($column.$row).",";
            	}
        	}
	        $str .= "},\n";
	        fwrite($luaFile, $str);
	        $str = "";
	    }
	    fwrite($luaFile, "}\n");
	    fclose($luaFile);
	    echo "$fileName out put succeed\n";
	}
	private function outputLuaByMap($sheet,$sheetName,$allRow,$allColumm)
	{
		$fileName = $sheetName . ".lua";
		$luaFile = fopen(LUA_PATH . $fileName, 'w') or die("Unable to open file!");
	    fwrite($luaFile, "$sheetName = {\n");
		for ($row = 4; $row <= $allRow; $row++){ //行数是以第1行开始
	        $str = "\t[".($row-3)."] = {";
	        for ($column = 'A'; $column <= $allColumm; $column++) {//列数是以A列开始
	            if ($sheet->getCell($column.(3)) == "string") {
	                $str = $str.$sheet->getCell($column.(2))." = \"".$sheet->getCell($column.$row)."\", ";
	            }else{
	                $str = $str.$sheet->getCell($column.(2))." = ".$sheet->getCell($column.$row).", ";
	            }
	        }
	        fwrite($luaFile, $str."},\n");
	        $str="";
	    }
	   	fwrite($luaFile, "}\n");
	    fclose($luaFile);
	    echo "$fileName out put succeed\n";
	}
	private function outputXml($sheet,$sheetName,$allRow,$allColumm,$theName)
	{
		$fileName = $sheetName . ".xml";
		$xmlFile = XML_PATH . $fileName;
		$doc = new DOMDocument('1.0','utf-8');
		$doc->preserveWhiteSpace=false;
		$doc->formatOutput=true;
		$config = $doc->createElement(XML_ROOT_NAME);
		for ($row=4; $row <=$allRow ; $row++) { 
			$name = $doc->createElement($theName);
			for ($column='A'; $column <= $allColumm; $column++) { 
				$name->setAttribute($sheet->getCell($column.(2)), $sheet->getCell($column.$row));
			}
			$config->appendChild($name);
		}
		$doc->appendChild($config);
		$doc->save($xmlFile );
		echo "$fileName out put succeed\n\n";
	}
}







