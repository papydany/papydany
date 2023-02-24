@inject('r','App\Models\R')
 <table>
 <tr>
    <td colspan="5">UNIVERSITY OF CALABAR</td></tr>
   <tr> <td colspan="5">MOP UP REGISTERED STUDENTS</td></tr>
   <?php $e = 0; ?>
   @foreach($user as $k =>$item)
   <?php $e = ++$e; ?>
   <?php  $department = $r->get_departmetname($k);
    ?>
   <tr><td colspan="5">{{$department}}</td></tr>
   <tr>
<th>S/N</th>
                        <th>Mat Number</th>
                        <th>Surname</th>
                        <th>Names</th>
                      <th>Other names</th>
                  
                      <th>Courses</th>
                  
                          </tr>
                            <?php $c = 0; ?>
                      @foreach($item as $v)
                 
                      <?php $c = ++$c; 
                      
                      ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                      <td>{{$v->matric_number}}</td>
                       <td>{{strtoupper($v->surname)}}</td>
                       <td>{{strtoupper($v->firstname)}}</td>
                        <td>{{strtoupper($v->othername)}}</td>
                     <td>
                            <?php
                            if(isset($cA[$v->id])){
                             foreach($cA[$v->id]['value'] as $vv){
                            if ($loop->last){
                                echo '&nbsp;'.$vv->course_code;
                            }else{
                                echo '&nbsp;'.$vv->course_code.',&nbsp;';
                            }
                               
                            }
                          }
                            ?>
                         </td>
                       </tr>
                     
                      @endforeach
                      @endforeach
                  </table>
                


                        

             