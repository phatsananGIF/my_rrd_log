<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\RrdlogRequest;
use File;
use DB;

class RrdlogController extends Controller
{
    public function index(){
        $file_select = "snmpd.log";
        $lengthselect = 10;
        $cid = "";
        $serial = "";
        $name_cus = "";
        $notfile = "";
        $deletefile = "";
        $data_rrdlog =[];
        

        $length = config('ima.length_row');

        if(session()->has('cidfile')){
            $cid = session('cidfile');
            $lengthselect = session('lengthselect');
            $name_cus = session('name_cus');

        }
        if(session()->has('notfile')){
            $notfile = session('notfile');
        }
        if(session()->has('deletefile')){
            $deletefile = session('deletefile');
        }
        if(session()->has('data_rrdlog')){
            $data_rrdlog = session('data_rrdlog');
        }
        if(session()->has('serial')){
            $serial = session('serial');
        }
        if(session()->has('listFile')){
            $file_select = session('listFile');
        }

        //อ่านไฟล์ในโฟเดอ//
        $dir = config('ima.rrd_log_path_selectfile');
        $DIRNAME = 
        $contents = File::allFiles($dir, $hidden = false);

        foreach ($contents as $infofile) {
            //เช็คนามสกุลไฟล์ เอาแค่ไฟล์ log
            if(pathinfo($infofile,PATHINFO_EXTENSION)=="log" && pathinfo($infofile,PATHINFO_DIRNAME)."/"==$dir){
                $file = pathinfo($infofile,PATHINFO_BASENAME);
                $files[$file] = $file.' ('.date( "d/m/Y H:i:s", filemtime($dir.$file)).')';
            }
        }


        return view('rrdlog', [ 'files'=>$files, 
                                'file_select'=>$file_select, 
                                'length'=>$length, 
                                'lengthselect'=>$lengthselect, 
                                'cid'=>$cid,
                                'serial'=>$serial, 
                                'name_cus'=>$name_cus, 
                                'notfile'=>$notfile,
                                'deletefile'=>$deletefile,
                                'data_rrdlog'=>$data_rrdlog 
                                ]);
                                

    }//f.index





    public function viewlogRRD(RrdlogRequest $request){

       $cidfile = $request->cidfile;
       $serial = $request->serial;
       $lengthselect  = $request->length;
       $listFile = $request->listFile;

        
        $dir = config('ima.rrd_log_path');
        $dircid = config('ima.rrd_log_path/cid');
        $dircid_tmp = config('ima.rrd_log_path_tmp/cid');
        $dir_selectfile = config('ima.rrd_log_path_selectfile');
        $dir_selectfile_tmp = config('ima.rrd_log_path_tmp');
        $name_cus = "";


        if($cidfile != ""){
            $dir = $dircid.$cidfile.".log";

            //-- ค้นหาไฟล์ --//
            $Find_files = File::glob($dir);

            if (count($Find_files)==0){
                return redirect()->action('RrdlogController@index')->with( [
                    'cidfile' => $cidfile,
                    'serial' => $serial,
                    'lengthselect' => $lengthselect,
                    'listFile' => $listFile,
                    'notfile'=>'File CID '.$cidfile.' not found.'
                    ] );
            }

            //--- copy file to tmp --//
            File::copy($dircid.$cidfile.".log",$dircid_tmp.$cidfile.".log");
            $dir = $dircid_tmp.$cidfile.".log";
            $dir_delete = $dircid_tmp;


            //--- get name cus ---//
            $results = DB::select('SELECT name FROM im_customer WHERE cus_code = '.$cidfile);
            $results = json_decode(json_encode($results), True);
            $name_cus = $results[0]['name'];

        }else if($listFile != ""){
            //--- copy file to tmp --//
            File::copy($dir_selectfile.$listFile, $dir_selectfile_tmp.$listFile);
            $dir = $dir_selectfile_tmp.$listFile;
            $dir_delete = $dir_selectfile_tmp;
        }
        
        //---อ่านไฟล์---//
        $file = fopen($dir, 'r');
        $row=[];

        $line = '';
        $countlines = 1;
        $cursor = 0;

        fseek($file, $cursor--, SEEK_END);
        $char = fgetc($file);

        while ($countlines <= $lengthselect) {

            if( $char !== false && $char !== "\n" && $char !== "\r"){
                $line = $char . $line;

            }else if($char == "\n" && $line!=""){
                //--text=>array--//
                $item = explode(",",$line);

                //ตัดเอาวันที่และเวลา
                $myDate = $item[0];
                $myDate = explode(" ",$myDate);
               
                if(count($myDate) >= 2){
                    $item[0]=$myDate[0]." ".$myDate[1];
                }
                
                
                if(count($item) >= 33){
                    if($serial != "" && $serial == trim($item[2])){
                        $row[] = $item;
                        $countlines++;
                    }else if($serial == ""){
                        $row[] = $item;
                        $countlines++;
                    }

                }else if(count($item) >= 19 ){

                    for($col=count($item); $col<33; $col++){
                        $item = array_merge($item, [""]); 
                    }
                    
                    if($serial != "" && $serial == trim($item[2])){
                        $row[] = $item;
                        $countlines++;
                    }else if($serial == ""){
                        $row[] = $item;
                        $countlines++;
                    }
                }

                $line = '';

            }
            
            $pointer= fseek($file, $cursor--, SEEK_END);
            $char = fgetc($file);

            if($pointer=='-1'){
                if($line!=""){
                    //--text=>array--//
                    $item = explode(",",$line);
                    
                    //ตัดเอาวันที่และเวลา
                    $myDate = $item[0];
                    $myDate = explode(" ",$myDate);
                
                    if(count($myDate) >= 2){
                        $item[0]=$myDate[0]." ".$myDate[1];
                    }


                    if(count($item) >= 33){
                        if($serial != "" && $serial == trim($item[2])){
                            $row[] = $item;
                            $countlines++;
                        }else if($serial == ""){
                            $row[] = $item;
                            $countlines++;
                        }
                    }else if(count($item) >= 19 ){
                        for($col=count($item); $col<=33; $col++){
                            $item = array_merge($item, [""]); 
                        }

                        if($serial != "" && $serial == trim($item[2])){
                            $row[] = $item;
                            $countlines++;
                        }else if($serial == ""){
                            $row[] = $item;
                            $countlines++;
                        }
                    }
                }
                break;
            }

        }
        fclose($file);

        //-- delete all tmp --//
        File::cleanDirectory($dir_delete);

        return redirect()->action('RrdlogController@index')->with( [
            'cidfile' => $cidfile,
            'serial' => $serial,
            'name_cus'=> $name_cus,
            'lengthselect' => $lengthselect,
            'listFile' => $listFile,
            'data_rrdlog' => $row
        ] );

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
