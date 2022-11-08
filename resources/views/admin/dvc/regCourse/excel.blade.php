
    @if(isset($reg))
    @if(count($reg) > 0)
    {{!$next = $s + 1}}
    @inject('r','App\Models\R')
    <?php $faculty =$r->get_facultymetname($f); 
$department = $r->get_departmetname($d);?>
  <table>
  <tr><th></th><th>UNIVERSITY OF CALABAR</th></tr>
  <tr><th></th><th>REGISTERED COURSES </th></tr>
      <tr><th></th><td>FACULTY OF  {{$faculty}}</td></tr>
      <tr><th></th>  <td>DEPARTMENT OF  {{$department}}</td></tr>
      <tr><th></th> <td> @if($sm == 1)
       First Semester
    @else
    Second Semester
@endif</td>
    </tr>
 
  </table>                         

{{!!$e = 0}}

@foreach($reg as $k => $value)
<table>
      <tr>
      <th></th>    <td>Level : {{$k}}00</td>
       
    </tr>
  </table> 

{{!!$ee = 0}}
        
        <?php 
        
        $collectionFosId =$value->groupBy('fos_id');
      
        ?>  
                 
        {{!!$eee = 0}}
        @foreach($collectionFosId as $keyfosid =>  $itemfosid)
        <?php $fos =$r->get_fos($keyfosid);?>
       
        <table>
          
                 <tr>
                     
                        <th>S/N</th>
                        
                        
                        <th>Code</th>
                     
                      
                          </tr>
                            {{!!$c = 0}}
                      @foreach($itemfosid as $v)
                   
                      {{!$c = ++$c}}
                      <tr>
                      
                      <td>{{$c}}</td>
                      

                        
                         <td>{{$v->reg_course_code}}</td>
                         
                        
                    
                      </tr>
                     
                      @endforeach

                  </table>
                
                  @endforeach
                  
                  
                
                  @endforeach

                  


                        @endif
                        
  @endif
                 
             