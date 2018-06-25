@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">RRD log</div>

                <div class="panel-body">
                   
                {!! Form::open() !!}

                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('cidfile', 'CID') }}
                            {{ Form::text('cidfile', $cid, ['class'=>'form-control', 'placeholder'=>'CID' ]) }}
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('namecid', 'Name') }}
                            {{ Form::text('namecid',null, ['class'=>'form-control', 'readonly'=>'readonly' ]) }}
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('serial', 'Search') }}
                            {{ Form::text('serial',null, ['class'=>'form-control', 'placeholder'=>'Search' ]) }}
                        </div>
                    </div>

                    

                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('length', 'Length') }}
                            {{ Form::select('length', $length, null, ['class'=>'form-control' ]) }}
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('listFile', 'File') }}
                            {{ Form::select('listFile', $files, $file_select, ['class'=>'form-control' ]) }}
                        </div>
                    </div>

                    <div class="col-xs-10">
                        <div class="form-group">
                            {{ Form::button('Search',['class'=>'btn btn-success', 'onclick'=>'getdata()']) }}
                            {{ Form::button('download',['class'=>'btn btn-primary', 'onclick'=>'downloadfile()']) }}
                            {{ Form::button('delete',['class'=>'btn btn-danger', 'onclick'=>'deletefile()']) }}
                        </div>
                    </div>

                {!! Form::close() !!}

                    
                </div>
            </div><!-- panel-->
        </div>



        <div class="col-md-12 " id="table_box" style="visibility: hidden">
            <div class="panel panel-default">
                
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9">
                            <h4 id="hcus"></h4>
                        </div>
                    </div><!-- row-->
                </div>
                                    

                <div class="panel-body">
                <div class="table-responsive">

                    <table class= "table table-striped table-bordered table-hover table-condensed" id = "tb-showlist">
                        <thead>
                            <tr>
                                
                                @foreach ( config('ima.column_rrd_log') as $data)
                                <th>{{ $data }}</th>
                                @endforeach

                            </tr>
                        </thead>
                       
                        <tbody>
                       
                        </tbody>

                    </table>

                </div>
                </div>
            </div><!-- panel-->
        </div>
        

    </div>
</div>
@endsection

@section('footer')


    @if ($notfile != "")
        <script>
            swal({
                title: "warning",
                text: "<?php  echo $notfile;  ?>",
                icon: "warning",
                dangerMode: true,
            })
        </script>
    @endif


    @if ($deletefile != "")
        <script>
            swal({
                title: "Success",
                text: "<?php  echo $deletefile;  ?>",
                icon: "success",
            })
        </script>
    @endif




<script>


function getdata(){
    document.getElementById('table_box').style.visibility = "hidden";
    $('#tb-showlist').DataTable().clear().destroy();

    var cidfile = document.getElementById("cidfile").value;
    var serial = document.getElementById("serial").value;
    var length = document.getElementById("length").value;
    var listFile = document.getElementById("listFile").value;
    

    dataI={'_token': '{{ csrf_token() }}', "cidfile":cidfile, "serial":serial, "length":length, "listFile":listFile}; 
    $.ajax({ 
        type:"POST",
        url:"{{ url('rrdlog/viewlogRRD') }}",
        cache:false,
        dataType:"JSON",
        data:dataI,
        async:true,
        beforeSend: function() {
            showPleaseWait();
        },
        success:function(result){
            var resultdata = null;

            if(result.status == 'not found'){
                hidePleaseWait();
                swal({
                    title: "warning",
                    text: "File CID "+cidfile+" not found.",
                    icon: "warning",
                    dangerMode: true,
                })
                $("#hcus").html(result.name_cus);
                document.getElementById("namecid").value = result.name_cus;
            }else if(result.status == 'success'){
                resultdata = result.data;
                
                $("#hcus").html(result.name_cus);
                document.getElementById("namecid").value = result.name_cus;
            }

            var showlist = $('#tb-showlist').DataTable( {
                data: resultdata,
                dom: 'li<"html5buttons" B>ftp',
                pageLength: 50,
                lengthMenu: [
                    [ 50, 100, 1000 ],
                    [ '50', '100', '1,000' ]
                ],
                buttons: [
                    {
                        extend: 'colvis',
                        collectionLayout: 'fixed three-column'
                    }
                ],
                language: {
                    buttons: {
                        colvis: 'Columns'
                    }
                },
                columnDefs: [
                    {
                        targets: [ {{config('ima.columnDefs')}} ] ,
                        visible: false
                    }
                ]
            } );//end DataTable

            hidePleaseWait();
            document.getElementById('table_box').style.visibility = "visible";
            
        }//end success
    });//end $.ajax 

}//f.getdata




function downloadfile() {

    var cidfile = document.getElementById("cidfile").value;
    var namefile = document.getElementById("listFile").value;

    if(cidfile!=""){
        window.location.href="{{ url('rrdlog/downloadcid/') }}/"+cidfile;
    }else{
        window.location.href="{{ url('rrdlog/downloadAll/') }}/"+namefile;
    }

        
}//f.downloadfile



function deletefile(){

    var cidfile = document.getElementById("cidfile").value;
    var namefile = document.getElementById("listFile").value;

    if(cidfile != ""){

        swal({
            title: "Are you sure deleted file "+cidfile+" ?",
            text: "Once deleted, you will not be able to recover this file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                window.location.href="{{ url('rrdlog/deletefileCID/') }}/"+cidfile;
            } else {
                return false;
            }
        });

    }else if(namefile=="snmpd.log"){

        swal({
            title: "Can not delete this file.",
            icon: "warning",
            dangerMode: true,
        })

    }else{

        swal({
            title: "Are you sure deleted file "+namefile+" ?",
            text: "Once deleted, you will not be able to recover this file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                window.location.href="{{ url('rrdlog/deletefileselect/') }}/"+namefile;
            } else {
                return false;
            }
        });

    }


}//f.deletefile




/**
 * Displays overlay with "Please wait" text. Based on bootstrap modal. Contains animated progress bar.
 */
function showPleaseWait() {
    var modalLoading = '<div class="modal" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false" role="dialog">\
        <div class="modal-dialog">\
            <div class="modal-content">\
                <div class="modal-header">\
                    <h4 class="modal-title">Please wait...</h4>\
                </div>\
                <div class="modal-body">\
                    <div class="progress">\
                      <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"\
                      aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%; height: 40px">\
                      </div>\
                    </div>\
                </div>\
            </div>\
        </div>\
    </div>';
    $(document.body).append(modalLoading);
    $("#pleaseWaitDialog").modal("show");
}


/**
 * Hides "Please wait" overlay. See function showPleaseWait().
 */
function hidePleaseWait() {
    $("#pleaseWaitDialog").modal("hide");
}

</script>

@endsection