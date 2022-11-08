{{!!$e = 0}}
<table>
    <tr>
    <td colspan="2">Faculty :</td>
    <td colspan="3">{{$faculty}}</td>
    </tr>
    <tr>
    <td colspan="2">Department :</td>
    <td colspan="3">{{$department}}</td>
    </tr>

    <tr>
    <td colspan="2">Course Title :</td>
    <td colspan="3">{{$title}}</td>
    <td></td>
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
    <th>Status</th>
 </tr> @foreach($user as $v)
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
                
                 
                  
                  
                
                