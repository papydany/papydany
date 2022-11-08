@extends('layouts.admin')
@section('title','Enter Result')
@section('content')
@inject('r','App\Models\R')
 <!-- Page Heading -->
 <style type="text/css">
 .fc {padding:0px;text-align: center;font-weight: bolder;font-size: 14px;}
 .table>tbody>tr>td{padding:4px;}
 .cc {width:6%;}
        </style>
                <div class="row">
                
                 
           
                    <div class="col-lg-12">
                        <?php $fos= $r->get_fos($c->fos_id) ?> 
                        <h4> Course Title : {{$c->reg_course_title}}</h4>
                        <ol class="breadcrumb">
                            <li class="active" style="font-weight: bolder;">
                     Course Code : {{$c->reg_course_code}}
                               &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                               Course Unit : {{$c->reg_course_unit}}
                               &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                               <?php $next = $c->session + 1;?>
                             Session: {{$c->session.' / '.$next}}
                              &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                              Level: {{$c->level_id}}00
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                Field Of Study :{{$fos}}
                            </li>
                        </ol>
                    </div>
                </div>
                  <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Enter Result
                  &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                  &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                  &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                  <span class="text-center text-success"><strong>Result Type :</strong>&nbsp;{{$rt}}</span></div>

                
                <div class="panel-body">
                <div class="col-sm-12">
                 @if(isset($u))
                
                      <form class="form-horizontal" role="form" method="POST" action="{{ url('/eo_insert_result') }}" data-parsley-validate>
                   
                        {{ csrf_field() }}
                  <input type="hidden" name="flag" value="{{$rt}}">
                   <input type="hidden" name="faculty_id" value="{{$f}}">
                   

                 <table class="table table-bordered table-striped">
                 <tr>
                        <th width="3%"></th>
                        <th width="3%">S/N</th>
                        <th>martic Number</th>
                        <th>Names</th>
                        <th width="15%">Script No</th>
                        <th class="cc" >CA</th>
                         <th class="cc" >Exams</th>
                          <th class="cc" >Total</th>
                          </tr>
                            {{!!$c = 0}}
                      @foreach($u as $v)
                      {{!$c = ++$c}}
                      <tr>
                      <td>
                      <input type="checkbox" class="checkbox" name="id[{{$c}}]" id="check[{{$c}}]" value="{{$c}}">
                      <input type="hidden" name="coursereg_id[{{$c}}]" value="{{$v->id}}">
                      <input type="hidden" name="user_id[{{$c}}]" value="{{$v->user_id}}">
                       <input type="hidden" name="matric_number[{{$c}}]" value="{{$v->matric_number}}">
                        <input type="hidden" name="course_id[{{$c}}]" value="{{$v->course_id}}">
                      <input type="hidden" name="cu[{{$c}}]" value="{{$v->course_unit}}">
                       <input type="hidden" name="session[{{$c}}]" value="{{$v->session}}">
                        <input type="hidden" name="semester[{{$c}}]" value="{{$v->semester_id}}">
                      <input type="hidden" name="level_id[{{$c}}]" value="{{$v->level_id}}">
                       <input type="hidden" name="season[{{$c}}]" value="{{$v->period}}">
                         <input type="hidden" name="entry_year[{{$c}}]" value="{{$v->entry_year}}">




                      </td>
                      <td>{{$c}}</td>
                       <td>{{$v->matric_number}}</td>
                        <td>{{strtoupper($v->surname." ".$v->firstname." ".$v->othername)}}</td>
                    
                
                   
                 
                  

 <td>
  <input type="number" class="form-control fc " name="scriptNo[{{$c}}]" value="" />
  </td>
   <td>
  <input type="" class="form-control fc " name="ca[{{$c}}]" id='ca{{$c}}'  onKeyUp="CA(this,'exam{{$c}}','d{{$c}}','check[{{$c}}]')" value="" />
  </td>
  <td>
 <input type="" class="form-control fc " name="exams[{{$c}}]" id='exam{{$c}}'   onKeyUp="updA(this,'ca{{$c}}','d{{$c}}','check[{{$c}}]')" value="" />
 </td>
 <td>
<input type="" class="form-control fc " name="total[{{$c}}]"  value="" id='d{{$c}}' readonly='true' onChange="if (this.value!='') document.getElementById('check[{{$c}}]').checked=true"  />
</td>
</tr>
@endforeach
<tr>
<td colspan="4"></td>
<td colspan="3" style="padding-top: 18px;padding-bottom:10px;">
 <input type="hidden" name="url" value="{{$url}}">
                        <button type="submit" class="btn btn-danger btn-block ">
                                    Enter Result
                                </button>
                                </td>
                                </tr>
                                </table>

                        

       </form>

                </div>

{{$u->setPath($url)->render()}}
                       @else
                        <p class="alert alert-warning">No Course is avalable</p>
                        @endif
                        

                  </div>
                    </div>
                    </div>
                    </div>
                    </div>
  @endsection 

  @section('script')

  <script >


      function updA(e,c,d,h){
var c=document.getElementById(c);
var t=document.getElementById(d);
var chk=document.getElementById(h);
 if(e.value > 70){alert('Exam scores can not be more than 70');e.value='';

var ca =c.value;
var ex =e.value;
var total =Number(ca) + Number(ex);
  t.value = total;}
else{
 
if(e.value < 71){
var ca =c.value;
var ex =e.value;
var total =Number(ca) + Number(ex);
if(total >100)
{
  alert('Total scores can not be more than 100');total='';e.value='';
}

  t.value = total;
}
  
}
if(t.value!=''){
  chk.checked=true;
}else{
  chk.checked=false;
}
}


 function CA(c,e,d,h)
 {

  var e=document.getElementById(e); 
  var t=document.getElementById(d); 
  var chk=document.getElementById(h);
  if(c.value > 40)
    {alert('CA scores can not be more than 40');
  c.value='';
e.value='';

t.value = '';

}
else{
 
if(c.value < 41){
var ca =c.value;
var ex =e.value;
var total =Number(ca) + Number(ex);
if(total >100)
{
  alert('Total scores can not be more than 100');total='';c.value='';e.value='';
}

  t.value = total;
  }
}
if(t.value!=''){chk.checked=true;}else{chk.checked=false;}}

  </script>


@endsection              