@if(isset($u))
    
    @inject('r','App\Models\R')
    <?php $department = $r->get_departmetname($d);?>
  <table>
  <tr><th></th><th>UNIVERSITY OF CALABAR</th></tr>
  <tr><th></th><th>GRADUATE LIST </th></tr>
      <tr><th></th>  <td>DEPARTMENT OF  {{$department}}</td></tr>

 
  </table>                         

{{!!$e = 0}}

        <table>
          
                 <tr>
                     
                        <th>S/N</th>
                        <th>Matric Number</th>
                        <th>Surname</th>
                        <th>Firstname</th>
                        <th>OtherName</th>
                        <th>Cgpa</th>
                        <th>Class Of Degree</th>
                    </tr>
                    @if(count($u) > 0)
                            {{!!$c = 0}}
                   
                      @foreach($u as $v)
                      <?php 
                      if($v->programme_id == 3)
                      {
$season ='VACATION';
                      }else{
$season ='RESIT';
                      }
                      
                      $cgpa =$r->get_cgpa($y,$v->id,$season); 

                      if($v->programme_id== 3){
                       $classDegree =$r->G_degree($cgpa,false);
                      }else{
 $classDegree =$r->D_degree($cpga,false ); 

                      }
                      ?>
                      {{!$c = ++$c}}
                      <tr>
                      
                      <td>{{$c}}</td>
                      <td>{{$v->matric_number}}</td>
                      <td>{{$v->surname}}</td>
                      <td>{{$v->firstname}}</td> 
                         <td>{{$v->othername}}</td>
                         <td>{{$cgpa}}</td>
                         <td>{{$classDegree}}</td>
                         
                    </tr>
                     
                      @endforeach
                      @else
                      <tr><td colspan="4">No records of graduate students</td></tr>
                      @endif

                  </table>
                
     @endif
                        
  
                 
             