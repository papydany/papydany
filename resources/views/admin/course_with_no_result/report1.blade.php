
    @if(isset($reg))
    @if(count($reg) > 0)
    {{!$next = $s + 1}}
    @inject('r','App\Models\R')
    <?php $faculty =$r->get_facultymetname($f); 
$department = $r->get_departmetname($d);?>
  <table>
      <tr>
          <td>FACULTY:  {{$faculty}}</td>
          <td>DEPARTMENT :  {{$department}}</td>
    </tr>
    <tr>
        <td> Status :</td>
         <td>C =Compulsary</td> 
         <td>G : Carry Over Courses or Drop </td>
    
    </tr>
  </table>                         

{{!!$e = 0}}

@foreach($reg as $k => $value)
<table>
      <tr>
          <td>Level : {{$k}}00</td>
       
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
                <th>Unit :  {{$fos}}</th>
            </tr>
                 <tr>
                     
                        <th>S/N</th>
                        
                        
                        <th>Code</th>
                        <th>Status</th>
                        <th>Semester</th>
                      
                          </tr>
                            {{!!$c = 0}}
                      @foreach($itemfosid as $v)
                   
                      {{!$c = ++$c}}
                      <tr>
                      
                      <td>{{$c}}</td>
                      

                        
                         <td>{{$v->reg_course_code}}</td>
                         <td>{{$v->reg_course_status}}</td>
                         <td>{{$v->semester_id == 1 ? 'First' : 'Second'}} </td>
                    
                      </tr>
                     
                      @endforeach

                  </table>
                
                  @endforeach
                  
                  
                
                  @endforeach

                  


                        @endif
                        
  @endif
                 
             