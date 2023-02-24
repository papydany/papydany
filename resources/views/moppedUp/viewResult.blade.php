@extends('layouts.display')
@section('title','Display Result')
@section('content')


             
                  <div class="row" style="min-height: 520px;">
        <div class="col-sm-12">
      @if(isset($u))
       @if(count($u) > 0)
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR</p>
    <p class="text-center" style="font-size:16px; font-weight:700;">CALABAR</p>
  
    <div class="col-sm-9 www">
  
    <p>FACULTY: {{$f->faculty_name}}</p>
      <p>DEPARTMENT: {{$d->department_name}}</p>
      <p>Mop Up Report Sheet</p>
    </div>
  <div class="col-sm-3 ww">
       <p><strong>Semester : </strong>{{$c->semester_id}} </p>
        <p><strong>Course  : </strong>{{$c->reg_course_code}}</p>

    </div>

    </td></tr>
 
  
  
</table>

            
                       
                 <table class="table table-bordered table-striped">
                 <tr>
                     
                        <th class="text-center">S/N</th>
                        <th class="text-center">MATRIC NUMBERS</th>
                        <th class="text-center">NAMES</th>
                        <th class="text-center">Session</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">GRADE</th>
                          </tr>
                            {{!!$c = 0}}
                      @foreach($u as $v)
                  
                      {{!$c = ++$c}}
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td>{{$v->matric_number}}</td>

                        <td>{{strtoupper($v->surname." ".$v->firstname." ".$v->othername)}}</td>
                      <td>{{isset($v->session) ? $v->session: ''}}</td>
                     <td>{{isset($v->total) ? $v->total: ''}}</td>
                        <td class="text-center">
                   {{isset($v->grade) ? $v->grade :''}}
                      </td>
                      </tr>
                     
                      @endforeach
                  </table>


                       @else
                        <p class="alert alert-warning">No Records  is available</p>
                        @endif
                        
  @endif
                  </div>
                    </div>
                    </div>
                    </div>
                    </div>
  @endsection 
             