@extends('layouts.admin')
@section('title','resultUploadRight')
@section('content')
        <!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">Result Upload Right</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('uploadRight') }}" data-parsley-validate>
                    {{ csrf_field() }}
                    <div class="form-group">



                        <div class="col-sm-3">
                            <label for="fos" class=" control-label">Enter Code</label>
                           <input type="text" name="token" class='form-control'/>

                        </div>




                        <div class="col-sm-3">
                            <br/>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-btn fa-user"></i> Send
                            </button>
                        </div>
                    </div>
   

                </form>
                </div>
            </div>
        </div>
    
            </div>
        </div>
    </div>
</div>


@endsection

