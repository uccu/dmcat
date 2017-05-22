<?php

namespace App\Project\Controller;

use Controller;

use AJAX;

use Request;
use Route;
use App\Project\Model\UserModel as User;
use App\Project\Model\LessionModel as Lession;
use Model;

use PHPExcel_Writer_Excel2007;
use PHPExcel;
use View;


class TestController extends Controller{


    function __construct(){

        // $get = Request->get;

        // var_dump($get);

    }



    function main($cc){

        $cc = Request::getInstance()->get('cc','s');
        var_dump($cc);
 
    }

    function ec(){

        
        
        echo 'ok';
        
       

        

    }


    function getLessionById($name = null,$id = null){

        //var_dump(func_get_args());
        //echo '123';

        echo Lession::getInstance()->where('id=%d',1)->get();

    }

    function haml(){

        View::addData(['g'=>['title'=>'zz','keywords'=>'baka']]);

        View::hamlReader('Test/my','App');


    }


    function t(){
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Shared/ZipStreamWrapper.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Shared/String.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/IWriter.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007.php';
        
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/WriterPart.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/ContentTypes.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/DocProps.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/Rels.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/Theme.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/Style.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/Workbook.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/Worksheet.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/Drawing.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/Comments.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Writer/Excel2007/StringTable.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/HashTable.php';
        require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Settings.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/IComparable.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/Font.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/Color.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/Fill.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/Borders.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/Border.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/Alignment.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/NumberFormat.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style/Protection.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/WorksheetIterator.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Calculation/Function.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Calculation/Functions.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Calculation.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Style.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/DocumentSecurity.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/DocumentProperties.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet/ColumnDimension.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet/RowDimension.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet/Protection.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet/SheetView.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet/HeaderFooter.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet/PageMargins.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet/PageSetup.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/CachedObjectStorage/ICache.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/CachedObjectStorage/CacheBase.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/CachedObjectStorage/Memory.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/CachedObjectStorageFactory.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/ReferenceHelper.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/IComparable.php';
        // require_once VENDOR_ROOT.'os/php-excel/PHPExcel/Worksheet.php';
        require_once VENDOR_ROOT.'os/php-excel/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setCellValue('A1', '1');
        $sheet->setCellValue('A2', '2');
        $sheet->setCellValue('A3', '3');

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(BASE_ROOT."log/05featuredemo.xlsx");

    }


}