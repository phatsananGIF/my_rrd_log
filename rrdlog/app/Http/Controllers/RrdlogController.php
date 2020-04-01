<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use File;
use DB;

class RrdlogController extends Controller
{
    public function index(){
        $file_select = "snmpd.log";
        $cid = "";
        $notfile = "";
        $deletefile = "";
        $all_cus = [];

        $length = config('ima.length_row');

        if(session()->has('cidfile')){
            $cid = session('cidfile');

        }
        if(session()->has('notfile')){
            $notfile = session('notfile');
        }
        if(session()->has('deletefile')){
            $deletefile = session('deletefile');
        }

        //อ่านไฟล์ในโฟเดอ//
        $dir = config('ima.rrd_log_path_selectfile');
        
        $contents = File::allFiles($dir, $hidden = false);

        foreach ($contents as $infofile) {
            //เช็คนามสกุลไฟล์ เอาแค่ไฟล์ log
            if(pathinfo($infofile,PATHINFO_EXTENSION)=="log" && pathinfo($infofile,PATHINFO_DIRNAME)."/"==$dir){
                $file = pathinfo($infofile,PATHINFO_BASENAME);
                $filedate = date( "Y/m/d H:i:s", filemtime($dir.$file));
                $files[$file] = ' ('.$filedate.')'.$file;
                
            }
        }

        asort($files);

        //--- get name cus ---//
        $results = DB::select('SELECT cus_code, name FROM im_customer');
        foreach($results as $cus){
            $all_cus[ $cus->cus_code ] = $cus->name;
        }
        $all_cus = json_encode($all_cus);


        return view('rrdlog', [ 'files'=>$files, 
                                'file_select'=>$file_select, 
                                'length'=>$length, 
                                'cid'=>$cid,
                                'all_cus'=>$all_cus,
                                'notfile'=>$notfile,
                                'deletefile'=>$deletefile
                                ]);
                                

    }//f.index





    public function viewlogRRD(Request $request){

       $cidfile = $request->cidfile;
       $serial = $request->serial;
       $lengthselect  = $request->length;
       $listFile = $request->listFile;
       $datepicker =  $request->datepicker;

       $arrayData=[];
       $files = [];
       $linedata = [];

        
        $dir = config('ima.rrd_log_path');
        $dircid = config('ima.rrd_log_path/cid');
        $dircid_tmp = config('ima.rrd_log_path_tmp/cid');
        $dir_allfile = config('ima.rrd_log_path_selectfile');
        $dir_file_tmp = config('ima.rrd_log_path_tmp');
        $name_cus = "";


        if($cidfile != ""){
            $dir = $dircid.$cidfile.".log";

            //-- ค้นหาไฟล์ --//
            $Find_files = File::glob($dir);

            //--ถ้าหาไฟล์ไม่เจอ--//
            if (count($Find_files)==0){
                return array(
                    'status' => 'not found',
                    'name_cus'=> null,
                    'data' => null);
            }

            //--- copy file to tmp --//
            File::copy($dircid.$cidfile.".log",$dircid_tmp.$cidfile.".log");
            $dir = $dircid_tmp.$cidfile.".log";
            $dir_delete = $dircid_tmp.$cidfile.".log";

            //--- get name cus ---//
            $results = DB::select('SELECT name FROM im_customer WHERE cus_code = '.$cidfile);
            $results = json_decode(json_encode($results), True);
            $name_cus = $results[0]['name'];

            //---อ่านไฟล์ ทั้งหมดเลย---//
            $lines = file($dir);

            //--- ค้นหา serial ---//
            if($serial != ""){
                $lines = preg_grep("[".$serial."]", $lines);
            }
            
            krsort($lines);//sort key array จาก มากไปน้อย
            array_splice($linedata,0,0,$lines);

            //ลบไฟล์ in tmp
            File::delete($dir_delete);

            if(count($linedata) > $lengthselect){
                $linedata = array_splice($linedata, count($linedata)-$lengthselect);
            }

        }else if($datepicker != ""){
            $datepicker = explode("-",$datepicker);
            $start_date = trim($datepicker[0]);
            $end_date = trim($datepicker[1]);
            //อ่านไฟล์ในโฟเดอ//
            $dir = $dir_allfile;
            $contents = File::allFiles($dir, $hidden = false);
            foreach ($contents as $infofile) {
                //เช็คนามสกุลไฟล์ เอาแค่ไฟล์ log และวันที่
                $file = pathinfo($infofile,PATHINFO_BASENAME);
                if(pathinfo($infofile,PATHINFO_EXTENSION)=="log" && pathinfo($infofile,PATHINFO_DIRNAME)."/"==$dir &&
                    date( "Y/m/d", filemtime($dir.$file)) >= $start_date && date( "Y/m/d", filemtime($dir.$file)) <= $end_date )
                {
                    $fileDate = date( "Y/m/d H:i:s", filemtime($dir.$file));
                    $files[$fileDate] = $file;
                }
            }
            krsort($files);

            foreach($files as $file){

                //--- copy file to tmp --//
                File::copy($dir_allfile.$file, $dir_file_tmp.$file);
                $dir = $dir_file_tmp.$file;
                $dir_delete = $dir_file_tmp.$file;
  
                //---อ่านไฟล์ ทั้งหมดเลย---//
                $lines = file($dir);
    
                //--- ค้นหา serial ---//
                if($serial != ""){
                    $lines = preg_grep("[".$serial."]", $lines);
                }

                array_splice($linedata,0,0,$lines);
    
                //ลบไฟล์ in tmp
                File::delete($dir_delete);

                if(count($linedata) > $lengthselect){
                    $linedata = array_splice($linedata, count($linedata)-$lengthselect);
                    break;
                }
    
     
            }//for read

            krsort($linedata);

        }//if-else
        

        $no=1;
        foreach($linedata as $line){
            $text_td = "";
            //--text=>array--//
            $line = str_replace($serial , '<my class="bg-primary">'.$serial."</my>" , $line , $count);
            $item = explode(",",$line);



            //ตัดเอา cid
            $myCID = $item[0];
            $myCID = explode(":",$myCID);

            $cid =[];
            if(isset($myCID[2])){
                $cid = array(trim($myCID[2]));
                
            }
            array_splice($item,1,0,$cid);


            //ตัดเอาวันที่และเวลา
            $myDate = $item[0];
            $myDate = explode(" ",$myDate);

        
            if(count($myDate) >= 2){
                $item[0]=$myDate[0]." ".$myDate[1];
            }


            
            
            if(count($item) >= 33){

                array_unshift($item, $no);
                $arrayData[] = $item;
                $no++;

            }else if(count($item) >= 19){

                for($col=count($item); $col<33; $col++){
                    $item = array_merge($item, [""]); 
                }

                array_unshift($item, $no);
                $arrayData[]=$item;
                $no++;
            }
            
            

        }//end foreach
        
        
        /*
        echo '<pre>';
        print_r($arrayData);
        echo  '</pre>';


        die();
        */
        
        
        return array(
                'status' => 'success',
                'name_cus'=> $name_cus,
                'data' => $arrayData
            );
                

    }//f.viewlogRRD


    public function downloadfileAllRRD($filename){

        File::copy(config('ima.rrd_log_path_selectfile').$filename, config('ima.rrd_log_path_tmp').$filename);
        $dir = config('ima.rrd_log_path_tmp').$filename;
        return response()->download($dir);

    }//f.downloadfileAllRRD


    public function downloadfileRRDcid($filename){

        $dir = config('ima.rrd_log_path/cid').$filename.'.log';

        //-- ค้นหาไฟล์ --//
        $Find_files = File::glob($dir);

        if (count($Find_files)==0){
            return redirect()->action('RrdlogController@index')->with( [
                'cidfile' => $filename,
                'notfile'=>'File CID '.$filename.' not found.'
                ] );
        }

        File::copy(config('ima.rrd_log_path/cid').$filename.'.log', config('ima.rrd_log_path_tmp/cid').$filename.'.log');
        $dir = config('ima.rrd_log_path_tmp/cid').$filename.'.log';
        return response()->download($dir);

    }//f.downloadfileRRDcid


    public function deletefileCID($filename){

        $dir = config('ima.rrd_log_path/cid').$filename.'.log';

        //-- ค้นหาไฟล์ --//
        $Find_files = File::glob($dir);

        if (count($Find_files)==0){
            return redirect()->action('RrdlogController@index')->with( [
                'cidfile' => $filename,
                'notfile'=>'File CID '.$filename.' not found.'
                ] );
        }

        //ลบไฟล์ 
        File::delete($dir);
        return redirect()->action('RrdlogController@index')->with( [
            'deletefile'=>'File '.$filename.' delete already.'
        ] );

    }//f.deletefileCID


    public function deletefileselect($filename){

        $dir = config('ima.rrd_log_path_selectfile').$filename;

        //ลบไฟล์ 
        File::delete($dir);
        return redirect()->action('RrdlogController@index')->with( [
            'deletefile'=>'File '.$filename.' delete already.'
        ] );

    }//f.deletefileselect





}
