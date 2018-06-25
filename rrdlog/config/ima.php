<?php


return [
    /*--------- RRD logController -------------*/

    
    'column_rrd_log' => ['NO.','col 1','2','3','4','5','6','7','8','9','10',
                        '11','12','13','14','15','16','17','18','19','20',
                        '21','22','23','24','25','26','27','28','29','30',
                        '31','32','33'
                        ],

    'columnDefs' => '10,11,12,13,14,15,21,22,23,24,25,26,27,28,29,30,31,32,33',//ใส่ลำดับคอลัมที่ไม่ต้องการให้แสดง (ลำดับเริ่มจาก 0)

    'length_row' => ['10' => '10', '50' => '50', '100' => '100', '500' => '500', '1000' => '1,000', '5000' => '5,000', '10000' => '10,000'],        
    'rrd_log_path_selectfile' => "/xampp/htdocs/my_rrd_log/logs/",//โฟรเดอเก็บไฟล์ log
    'rrd_log_path_tmp' => "/xampp/htdocs/my_rrd_log/logs/tmp/",
    'rrd_log_path' => "/xampp/htdocs/my_rrd_log/logs/snmpd.log",
    'rrd_log_path/cid' => "/xampp/htdocs/my_rrd_log/logs/snmpd/",//โฟรเดอเก็บไฟล์ log cid
    'rrd_log_path_tmp/cid' => "/xampp/htdocs/my_rrd_log/logs/snmpd/tmp/",
    

];
