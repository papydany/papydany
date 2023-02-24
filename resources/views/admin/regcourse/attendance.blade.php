{{!!$e = 0}}
<table>
  
    <tr>
    <td colspan="2">Department :</td>
    <td colspan="3">{{$department->department_name}}</td>
    </tr>

    <tr>
    <td colspan="2">Course :</td>
  <td colspan="2">{{$code}}</td>
    </tr>
 
    <tr>
    <td colspan="2">Session :</td>
    <td>{{$session}}</td>
    <td></td>
    <td>Semester :</td>
    <td>@if($semester == 1) First @else Sedcond @endif</td>
    <td></td>
    </tr>
    <tr><td colspan="8"></td></tr>
    <tr>
    <th>#</th>
    <th>MatricNo</th>
    <th>NAMES</th>
    <th>ScriptNo</th>
    <th>CA</th>
    <th>EXAM</th>
    <th>TOTAL</th>
    <th>Grade</th>
   
 </tr> @foreach($courseReg as $v)
    {{! $fullname = $v->surname.'  '.$v->firstname.' '.$v->othername}}
<tr>
    <td>{{++$e}}</td>
    <td>{{$v->matric_number}}</td>
    <td>{{$fullname}}</td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    </tr>
    @endforeach
    </table>
                
                 
                  
                  
                
                