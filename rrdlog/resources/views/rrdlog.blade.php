@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">RRD log</div>

                <div class="panel-body">
                   
                {!! Form::open(['url' => '/rrdlog/viewlogRRD']) !!}
                    {{ Form::token() }}

                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('cidfile', 'CID') }}
                            {{ Form::text('cidfile', $cid, ['class'=>'form-control', 'placeholder'=>'CID' ]) }}
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('serial', 'S/N') }}
                            {{ Form::text('serial',$serial, ['class'=>'form-control', 'placeholder'=>'serial' ]) }}
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('listFile', 'file') }}
                            {{ Form::select('listFile', $files, $file_select, ['class'=>'form-control' ]) }}
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('length', 'length') }}
                            {{ Form::select('length', $length, $lengthselect, ['class'=>'form-control' ]) }}
                        </div>
                    </div>

                    <div class="col-xs-10">
                        <div class="form-group">
                            {{ Form::submit('view', ['class'=>'btn btn-success']) }}
                            {{ Form::button('download',['class'=>'btn btn-primary', 'onclick'=>'downloadfile()']) }}
                            {{ Form::button('delete',['class'=>'btn btn-danger', 'onclick'=>'deletefile()']) }}
                        </div>
                    </div>

                {!! Form::close() !!}

                    
                </div>
            </div><!-- panel-->
        </div>


        <div class="col-md-12 ">
            <div class="panel panel-default">
                
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9">
                            @if (count($data_rrdlog) > 0 || $name_cus != "")
                                <h4>{{ $name_cus }}</h4>
                            @endif
                        </div>
                    </div><!-- row-->
                </div>
                    
                

                <div class="panel-body">
                <div class="table-responsive">

                    <table class= "table table-striped table-bordered table-hover table-condensed" id = "tb-showlist">
                        <thead>
                            <tr>
                                <th>NO.</th>
                                
                                @foreach ( config('ima.column_rrd_log') as $data)
                                <th>{{ $data }}</th>
                                @endforeach
                            

                            </tr>
                        </thead>
                       

                        <tbody>
                        @if (count($data_rrdlog) > 0)
                            <?php $n=1 ?>
                            @foreach ($data_rrdlog as $arrdata)
                            <?php  ?>
                                <tr>
                                    <td> {{ $n++ }} </td>                               
                                    @foreach ($arrdata as $data)
                                    <td> {{ $data }} </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
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


    @if (count($errors) > 0)
        <script>
            swal({
                title: "Please select",
                text: "<?php foreach( $errors->all() as $error ){ echo $error.' '; } ?>",
                icon: "warning",
                dangerMode: true,
            })
        </script>
    @endif

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
window.onload = function() {
    $('#tb-showlist').DataTable( {
        dom: 'i<"html5buttons" B>f',
        stateSave: true,
        buttons: [
            {
                extend: 'colvis',
                collectionLayout: 'fixed three-column'
            }
        ],
        bPaginate : false,
        language: {
            buttons: {
                colvis: 'Columns'
            }
        }

    } );

}



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

</script>

@endsection