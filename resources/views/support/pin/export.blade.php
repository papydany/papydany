

  <table>
      <tr>
      
          <td>Serial number</td>
          <td>Pins</td>
    </tr>
   
                       

{{!!$e = 0}}

@foreach($pin as $v)

      <tr>
         
          <td>{{$v->id}}</td>
          <td>{{$v->pin}}</td>
       
    </tr>
 @endforeach
  </table>


             