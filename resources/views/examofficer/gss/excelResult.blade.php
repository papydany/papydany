                         

{{!!$e = 0}}
<table>
            <tr>
                <th>#</th>
                <th>MatricNo</th>
                <th>NAMES</th>
                <th>CA</th>
                <th>EXAM</th>
                <th>TOTAL</th>
                <th>Grade</th>
               <th>{{$title}}</th>
                <th>{{$code}}</th>
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
                
                 
                  
                  
                
                