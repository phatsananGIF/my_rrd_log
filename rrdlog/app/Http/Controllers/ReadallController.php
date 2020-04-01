<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use File;
use DB;

class ReadallController extends Controller
{
    public function index(){

        $date = "2018/05/01 - 2018/05/31";
        $myCID = explode("-",$date);

        $start_date = trim($myCID[0]);
        $end_date = trim($myCID[1]);
        
        $dir_allfile = config('ima.rrd_log_path_selectfile');
        $dir_file_tmp = config('ima.rrd_log_path_tmp');
        $serial = "300A40348";
        $lengthselect = 10;
        $files = [];

        //อ่านไฟล์ในโฟเดอ//
        $dir = config('ima.rrd_log_path_selectfile');
        $contents = File::allFiles($dir, $hidden = false);
        foreach ($contents as $infofile) {
            //เช็คนามสกุลไฟล์ เอาแค่ไฟล์ log
            $file = pathinfo($infofile,PATHINFO_BASENAME);
            if(pathinfo($infofile,PATHINFO_EXTENSION)=="log" && pathinfo($infofile,PATHINFO_DIRNAME)."/"==$dir &&
                date( "Y/m/d", filemtime($dir.$file)) >= $start_date && date( "Y/m/d", filemtime($dir.$file)) <= $end_date )
            {
                $fileDate = date( "Y/m/d H:i:s", filemtime($dir.$file));
                $files[$fileDate] = $file;
            }
        }
        
        krsort($files);

        echo '<pre>';
        print_r($files);
        echo  '</pre>';

        $linedata = [];
        foreach($files as $file){
            echo $file."+";
            //--- copy file to tmp --//
            File::copy($dir_allfile.$file, $dir_file_tmp.$file);
            $dir = $dir_file_tmp.$file;
            $dir_delete = $dir_file_tmp;

            //---อ่านไฟล์ ทั้งหมดเลย---//
            $lines = file($dir);

            //--- ค้นหา serial ---//
            if($serial != ""){
                $lines = preg_grep("[".$serial."]", $lines);
            }

            echo '<pre>';
            print_r($lines);
            echo  '</pre>';

            //krsort($lines);
            array_splice($linedata,0,0,$lines);

            echo '<pre>';
            print_r($linedata);
            echo  '</pre>';

            if(count($linedata) > $lengthselect){
                $linedata = array_splice($linedata, count($linedata)-$lengthselect);
                break;
            }

 
        }

        krsort($linedata);
        
        echo '<pre>';
        print_r($linedata);
        echo  '</pre>';


        
    }//f.index

}//class 