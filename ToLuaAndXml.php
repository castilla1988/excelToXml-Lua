<?php
/**
 * @author Liuzr
 * @version 1.00
 */
error_reporting(E_ALL);
date_default_timezone_set('Asia/ShangHai');
/** PHPExcel_IOFactory */
require_once 'Classes/PHPExcel/IOFactory.php';
require 'Classes/AppConfig.php';
AppConfig::Run();
$file = array(
        'gift'=>"gift.xlsx",
        'work'=>"work.xlsx"
    );

$reader = PHPExcel_IOFactory::createReader('Excel2007'); //设置格式
foreach ($file as $key => $value) {
    $value = "config/".$value;
    if (!file_exists($value)) {
        exit("not found $value.\n");
    }
    $PHPExcel = $reader->load($value); // 载入excel文件
    $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
    $sheetName = $sheet->getTitle();
    $highestRow = $sheet->getHighestRow(); // 取得总行数
    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
    //echo "string$sheetName";
    $myFile = fopen("luaConfig/".$sheetName.".lua", 'w') or die("Unable to open file!");
    fwrite($myFile, "$sheetName = {\n");
    /** 循环读取每个单元格的数据 */
    $str = "";
    //以map形式的lua表
    /*
    for ($row = 4; $row <= $highestRow; $row++){//行数是以第1行开始
        $str = "\t[".($row-3)."] = {";
        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
            if ($sheet->getCell($column.(3)) == "string") {
                $str = $str.$sheet->getCell($column.(2))." = \"".$sheet->getCell($column.$row)."\", ";
            }else{
                $str = $str.$sheet->getCell($column.(2))." = ".$sheet->getCell($column.$row).", ";
            }
            //$dataset[] = $sheet->getCell($column.$row)->getValue();
            //echo $column.$row.":".$sheet->getCell($column.$row)->getValue()."\n";
        }
        fwrite($myFile, $str."},\n");
        $str="";
    }
    */
    //以List形式输出lua表
    for ($column='A'; $column <= $highestColumm; $column++) { 
        $str = "\t".$sheet->getCell($column.(2))." = {";
        for ($row = 4; $row <= $highestRow; $row++) { 
            if ($sheet->getCell($column.(3)) == "string") {
                $str .= "\"".$sheet->getCell($column.$row)."\",";
            }else{
                $str .= $sheet->getCell($column.$row).",";
            }
        }
        $str .= "},\n";
        fwrite($myFile, $str);
        $str = "";
    }
    fwrite($myFile, "}\n");
    fclose($myFile);
    //eche "$sheetName.lua is put out";

    //生成xml
$string = <<<XML
<?xml version='1.0' encoding='utf-8'?>
<config>
</config>
XML;
    $xml = simplexml_load_string($string);
    for ($row = 4; $row <= $highestRow; $row++){ 
        $item = $xml->addChild($key);
        //$node = $item->addChild($key, $row);
        for ($column='A'; $column <= $highestColumm; $column++) { 
            $item->addAttribute($sheet->getCell($column.(2)), $sheet->getCell($column.$row));
        }
    }
    $xml->asXML("xmlConfig/".$sheetName.".xml");
    $f="xmlConfig/".$sheetName.".xml"; 
    file_put_contents($f,str_replace('><',">\n<",file_get_contents($f))); 
    echo $sheetName." lua & xml put out scceed!\n";
}

?>




