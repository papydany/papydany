{{!$next = $s + 1}}
@inject('r','App\Models\R')
<?php 
if($old == 1){
  $faculty =$r->get_facultymetnameOld($f); 
  $department = $r->get_departmetnameOld($d);
}else{
$faculty =$r->get_facultymetname($f); 
$department = $r->get_departmetname($d);
}?>
  <table>
      <tr><td colspan='2'>FACULTY:</td> <td colspan="4"> {{$faculty}}</td></tr>
        <tr>  <td colspan='2'>DEPARTMENT :  </td> <td colspan="4">{{$department}}</td></tr>
        <tr>  <td colspan='2'>Session:  </td> <td colspan="4">{{$s}} / {{$next}} </td></tr>
  </table>                         



<table>
<tr>
<th>S/N</th>
<th>Matric number</th>
<th>Surname</th>
<th>Firstname</th>
<th>Othername</th>
<th>Level</th>
</tr>
{{!!$c = 0}}
@if(isset($reg))
@if(count($reg) > 0)
<tr>
<th colspan="6">Students on Withdrawal</th>

</tr>
 @foreach($reg as $v)
 {{!$c = ++$c}}
 <tr>
  <td>{{$c}}</td>
<td>{{$v['mat']}}</td>
<td>{{$v['surname']}}</td>
<td>{{$v['firstname']}} </td>
<td>{{$v['othername']}} </td>
<td>{{$v['level']}} </td>
</tr>
 @endforeach
@endif
@endif

@if(isset($w))
@if(count($w) > 0)
<tr>
<th colspan="6">Students on Change of programme</th>

</tr>
 @foreach($w as $v)

 {{!$c = ++$c}}
 <tr>
  <td>{{$c}}</td>
<td>{{$v['mat']}}</td>
<td>{{$v['surname']}}</td>
<td>{{$v['firstname']}} </td>
<td>{{$v['othername']}} </td>
<td>{{$v['level']}} </td>
</tr>
 @endforeach
 @endif
 @endif

 @if(isset($p))
@if(count($p) > 0)
<tr>
<th colspan="6">Students on Probation</th>

</tr>
 @foreach($p as $v)
 {{!$c = ++$c}}
 <tr>
  <td>{{$c}}</td>
<td>{{$v['mat']}}</td>
<td>{{$v['surname']}}</td>
<td>{{$v['firstname']}} </td>
<td>{{$v['othername']}} </td>
<td>{{$v['level']}} </td>
</tr>
 @endforeach
 @endif
 @endif
</table>
             

                  



                 
             