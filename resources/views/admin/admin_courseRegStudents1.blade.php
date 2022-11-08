@extends('layouts.display')
@section('title','Display Result')
@section('content')
@inject('r','App\Models\R')

             
                  <div class="row" style="min-height: 520px;">
        <div class="col-sm-12">
      @if(isset($u))
       @if(count($u) > 0)
     <?php  $department = $r->get_departmetname($d);
     $faculty = '';//$r->get_facultymetname(Auth::user()->faculty_id);
            $fos =$r->get_fos($fos);
    $next = $s + 1;
    $semester =DB::table('semesters')->where('semester_id',$sm)->first();
                  ?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">REGISTERED STUDENTS</p>
    <div class="col-sm-9 www">
  
    <p>FACULTY: {{$faculty}}</p>
      <p>DEPARTMENT: {{$department}}</p>
      <p>PROGRAMME:  {{$fos}}</p>
 
      </div>
  <div class="col-sm-3 ww">
      <p> <strong>Level : </strong>{{$l}}00 </p>
      <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
       <p><strong>Semester : </strong>{{$semester->semester_name}} </p>
        

    </div>

    </td></tr>
 
  
  
</table>

            
@foreach($u as $k => $item)  
<p><strong>TITLE : </strong> {{$rd[$k]['title']}}  <strong> CODE: </strong>   ( {{$rd[$k]['code']}}) 
<strong>Unit : </strong>{{$rd[$k]['unit']}} <strong>STATUS : </strong> <strong>{{$rd[$k]['status']}}</strong></p>

                 <table class="table table-bordered table-striped">
                 <tr>
                     
                        <th>S/N</th>
                        <th>Martic Number</th>
                        <th>Names</th>
                      <!--<th>Profile Pic</th>-->
                        
                        <th width="15%">Signature</th>
                          </tr>
                            <?php $c = 0; ?>
                      @foreach($item as $v)
                 
                      <?php $c = ++$c; ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td>{{$v->matric_number}}</td>

                        <td>{{strtoupper($v->surname." ".$v->firstname." ".$v->othername)}}</td>
                         <!--<td><img src="{{asset('img/student/'.$v->image_url)}}"></td>-->
                         {{! $img ="https://unicalexams.edu.ng/img/student/".$v->image_url}}
                        <!-- <td><img src="{{$img}}" width="25%"></td>-->
                   
                     <td></td>
                       
                      </tr>
                     
                      @endforeach
                  </table>
                  @endforeach


                       @else
                        <p class="alert alert-warning">No Register students  is avalable</p>
                        @endif
                        
  @endif
                  </div>
                    </div>
                    </div>
                    </div>
                    </div>
  @endsection 
             