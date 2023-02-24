@inject('r','App\Models\R')
<?php use Illuminate\Support\Facades\Auth; ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Registered Students</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style type="text/css">
table {
    font-family: arial, sans-serif;
	border-spacing: 0px;
    width: 100%;
    
}
.row{
	 margin-right: -.75rem;
    width: 100%;
	height:auto;
}

.page-break {
    page-break-after: always;
}
 .float_left{
	   width: 55%;
	   float:left;
	font-size: 0.6em;

	}
	.float_left1{
	   width: 40%;
	   float:left;
	font-size: 0.6em;
  }
  .float_left > td {padding: 2px;
		}
    .float_left1 > td {padding: 2px;
		}
	
	.header_left{
		width: 10%;
		float:left;
		padding: 0px;
		
		}

		.header_left2{
		width: 80%;
		float:left;
		
		
		}
		.header_left3{
		width: 10%;
		float:right;
		padding: 0px;
		
		}
		.content_left{
	   width: 100%;
	
	font-size: 0.60em;
	
	}



   .clear{clear:both;
   }
   




.degree >td, .degree >th {
	color: black;
	border: 1px solid blue;
	padding: 2px;
	font-size: 0.7em;
	
}



header{

	top:-7;
	left:0px;
	right:0px;
	height:140px;
  
	
	}
  footer{
	   position: fixed;
	   left: 0;
	   right: 0;
	   height: 40px;
	   bottom: 30px;
}
.f1{
		 
		 width: 30%;
		 float: left;
	   font-size: 0.6em;
	   margin-left:1%;
	  }
	
		 .fa{
		 
		 width: 30%;
		 float: left;
		 font-size: 0.6em;
		 
		 }
     .ff{
		 width:30%;
		 float: right;
		 font-size: 0.6em;
		 margin-left: 1%;
		 
	  }
	 .ff >td {padding: 10px;
		}
    .fa >td {padding: 10px;
		}
    .f1 >td {padding: 10px;
		}
   .clear{clear:both;
   }
</style>
</head>
<body>
	<script type="text/php">
		if ( isset($pdf) ) {
			// OLD 
			// $font = Font_Metrics::get_font("helvetica", "bold");
			// $pdf->page_text(72, 18, "{PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(255,0,0));
			// v.0.7.0 and greater
			$x = 620;
			$y = 40;
			$text = "{PAGE_NUM} of {PAGE_COUNT}";
			$font = $fontMetrics->get_font("helvetica", "bold");
			$size = 10;
			$color = array(0,0,0);
			$word_space = 0.0;  //  default
			$char_space = 0.0;  //  default
			$angle = 0.0;   //  default
			$pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
		}
	</script>


             
         
      @if(isset($u))
       @if(count($u) > 0)
     <?php  $department = $r->get_departmetname(Auth::user()->department_id);
     $faculty = $r->get_facultymetname(Auth::user()->faculty_id);
            $fos =$r->get_fos($f_id);


     ?>
                   
                   {{!$next = $s + 1}}
                  {{! $semester =DB::table('semesters')
                  ->where('semester_id',$sm)->first()}}
                  <header>
	<div class="row">
	<div class="header_left">
		<table >
			<thead>
			 <tr>
		<img src="{{asset('logo.png')}}" width="40px;" >
			 </tr>
			</thead>
		</table>
	</div>
	<div class="header_left2">
	
	<table>
		<thead align="center">
		 <tr style="font-size:1em;">
			<th>UNIVERSITY OF CALABAR, CALABAR, NIGERIA</th>
		</tr>
		<tr style="font-size:1em;">
			<th ><span style="background-color:blue; padding:45px; color:white;font-weight:bolder;">RESULT REPORT</th>
		</tr>
		
		</thead>
	</table>
	</div>

	<div class="clear"></div>
	</div>
   
	<div class="row">
	<div class="float_left">
		<table>
			<thead>
  
			
			<tr>
				<td><b>FACULTY: </b> {{$faculty}}</td>
			</tr>
			<tr>
				<td><b>DEPARTMENT: </b>{{$department}}</td>
			</tr>
			<tr>
				<td><b>PROGRAMME: </b>  {{$fos}}</td>
      </tr>
      <tr>
        <td><b>LECTURER: </b> {{Auth::user()->title.' '.Auth::user()->name}}</td>
			</tr>
      <tr><td><b>DATE : </b><?php echo date('d-M-Y'); ?></td></tr>
			
			</thead>
		</table>
	</div>
			
					<div class="float_left1">
						<table >
							<thead>
							 <tr>
          
								<td><b>LEVEL : </b>{{$l}}00 </td>
							</tr>
							<tr>
								<td><b>SESSION : </b>{{$s.' / '.$next}}</td>
							</tr>
							<tr>
								<td><b>SEMESTER : </b> {{$semester->semester_name}}</td>
              </tr>
              <tr>
                <td><strong>COURSE  : </strong>{{$course_code}}</td>
                <td><strong>Unit : </strong>{{$reg->reg_course_unit}}</td>
							</tr>
              <tr><td>{{strtoupper($reg->reg_course_title)}}</td></tr>
							</thead>
						</table>
					</div>
							<div class="clear"></div>
					
							<div><hr/></div> 
									
						</div>
					</header>

          <footer>
						
						<hr/>
						<div class="row">
							
						

						<div class="f1">
							
            <table>
								
								
                <tr><td><br/>Signature & Date</td>
                  <td><br/><span style="color:#000; padding:20px 0 0 3px; font-size:10px;">
                    .............................................................</span>
                </td>
                </tr>
                <tr><td>Name : </td><td><span style="color:#000; padding:20px 0 0 3px; font-size:10px;">
                    .............................................................</span></td></tr>
              
              
              </table>
							
							</div>
						
							<div class="fa">
              <table>
								
								
                <tr><td><br/>Signature & Date</td>
                  <td><br/><span style="color:#000; padding:20px 0 0 3px; font-size:10px;">
                    .............................................................</span>
                </td>
                </tr>
                <tr><td>Name : </td><td><span style="color:#000; padding:20px 0 0 3px; font-size:10px;">
                    .............................................................</span></td></tr>
              
              
              </table>
								
								</div>
							<div class="ff">
								<table>
								
								
									<tr><td><br/>Signature & Date</td>
										<td><br/><span style="color:#000; padding:20px 0 0 3px; font-size:10px;">
											.............................................................</span>
									</td>
									</tr>
									<tr><td>Name : </td><td><span style="color:#000; padding:20px 0 0 3px; font-size:10px;">
											.............................................................</span></td></tr>
								
								
								</table>
							
								
									
									
									
								
								
								</div>
								<div class="clear"></div>
						</div>

				</footer>	
                  

<div class="row">
        <div class="col-sm-12">
		@foreach($u as $k => $items)          
                 <table class="degree">
				 <tr><th colspan="8">{{$k}}00 Level  &nbsp; &nbsp;&nbsp;  @if($k > $l)CARRY OVER RESULT @endif </th></tr>
               
                 <tr>
                     
                        <th width="3%">S/N</th>
                        <th width="17%">Martic Number</th>
                        <th>SURNAME</th>
                        <th>FIRST NAME</th>
                        <th>OTHER NAME</th>
                        <th width="7%">Script No</th>
                        <th width="5%">CA</th>
                        <th width="5%">Exams</th>
                        <th width="5%">Total</th>
                        <th width="5%">Grade</th>
                          </tr>
                            {{!!$c = 0}}
                      @foreach($items as $result)
                    
                      {{!$c = ++$c}}
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td>{{$result->matric_number}}</td>
                       <td>{{strtoupper($result->surname)}}</td>
                       <td>{{strtoupper($result->firstname)}}</td>

                        <td>{{strtoupper($result->othername)}}</td>
                        <td>{{isset($result->scriptNo) ? $result->scriptNo : ''}}</td>
                         <td>{{isset($result->ca) ? $result->ca : ''}}</td>
                       <td>{{isset($result->exam) ? $result->exam: ''}}</td>
                     <td>{{isset($result->total) ? $result->total: ''}}</td>
                        <td class="text-center">
                   
                    

                   
                       
{{isset($result->grade) ? $result->grade :''}}
                      </td>
                      </tr>
                     
                      @endforeach
                  </table>

				  @endforeach
                       @else
                        <p class="alert alert-warning">No Register students  is available</p>
                        @endif
                        
  @endif
                  </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </body>
</html>	
             