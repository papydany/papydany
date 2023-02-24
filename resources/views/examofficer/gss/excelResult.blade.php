                         

{{!!$e = 0}}
<table>
    <tr><th colspan="2">Title</th>
    <th colspan="5">{{$title}}</th></tr>
    <tr><th>Code</th><th colspan="5">{{$code}}</th></tr>
            <tr>
                <th>#</th>
                <th>MatricNo</th>
                <th>NAMES</th>
                <th>CA</th>
                <th>EXAM</th>
                <th>TOTAL</th>
                <th>Grade</th>
               
            </tr>
                           
                      @foreach($user as $v)
                      {{! $fullname = $v->surname.'  '.$v->firstname.' '.$v->othername}}
                
                      <tr>
                      
                      <td>{{++$e}}</td>
                       <td>{{$v->matric_number}}</td>
                         <td>{{$fullname}}</td>
                         <td>{{$v->ca}} </td>
                         <td>{{$v->exam}} </td>
                         <td>{{$v->total}} </td>
                         <td>{{$v->grade}} </td>
                         <td> </td>
                         <td> </td>
                      </tr>
                     
                      @endforeach

                  </table>
                
                 
                  
                  
                
                