{{!!$e = 0}}
<table>
    <tr>
    <td colspan="2">Course Title :</td>
    <td colspan="3">{{$title}}</td>
    <td></td>
    <td colspan="2">{{$code}}</td>
    </tr>
 
    <tr>
    <td colspan="2">Session :</td>
    <td>{{$session}} MOP UP</td>
    <td></td>
    
    <td></td>
    </tr>
    <tr><td colspan="7"></td></tr>
    <tr>
    <th>#</th>
    <th>MatricNo</th>
    <th>NAMES</th>
 </tr> @foreach($user as $v)
    {{! $fullname = $v->surname.'  '.$v->firstname.' '.$v->othername}}
<tr>
    <td>{{++$e}}</td>
    <td>{{$v->matric_number}}</td>
    <td>{{$fullname}}</td>
    </tr>
    @endforeach
    </table>
                
                 
                  
                  
                
                