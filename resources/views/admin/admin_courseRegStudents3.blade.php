@inject('r','App\Models\R')
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
	   width: 35%;
	   float:left;
	font-size: 0.6em;
	
	
	}
	.float_left1{
	   width: 35%;
	   float:left;
	font-size: 0.6em;
	
	}
	.float_left3{
	   width: 30%;
	   float:left;
	font-size: 0.6em;
	padding: 2px 0px;
	
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
	padding: 1px;
	font-size: 0.6em;
	
}



header{

	top:-7;
	left:0px;
	right:0px;
	height:150px;
  
	
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
    <?php
    
      $department = $r->get_departmetname($d);
     $faculty = '';//$r->get_facultymetname(Auth::user()->faculty_id);
            $fos =$r->get_fos($fos);
    $next = $s + 1;
    $semester =DB::table('semesters')->where('semester_id',$sm)->first();
                  ?>
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
			<th ><span style="background-color:blue; padding:45px; color:white;font-weight:bolder;">REGISTERED STUDENTS AND COURSES</th>
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
				<td><b>FACULTY : </b></td>
			</tr>
			<tr>
				<td><b>DEPARTMENT : </b>{{$department}}</td>
			</tr>
			<tr>
				<td><b>PROGRAMME: : </b> {{$fos}}</td>
			</tr>
			
			</thead>
		</table>
	</div>
			
					<div class="float_left1">
						<table >
							<thead>
							 <tr>
          
								<td><b>Level :  </b>{{$l}}00</td>
							</tr>
							<tr>
								<td><b>Session  : </b>{{$s.' / '.$next}}</td>
							</tr>
							<tr>
								<td><b>Semester : </b> {{$semester->semester_name}}</td>
							</tr>
							</thead>
						</table>
					</div>
							<div class="clear"></div>
					
							<div><hr/></div> 
									
						</div>
					</header>
                  
                  <div class="row">
        
      @if(isset($u))
       @if(count($u) > 0)    
       <?php $a = 0; ?>    
@foreach($u as $k => $item)  
<table  style="margin-top:5px;">
<tr><th> {{++$a}}  </th>
    <th> {{$rd[$k]['title']}}  </th>
<th> {{$rd[$k]['code']}} </th>
<th>{{$rd[$k]['unit']}} Unit</th>
<th>

@if($rd[$k]['status'] =='C')
Compulsary
@elseif($rd[$k]['status'] =='E')
Elective
@elseif($rd[$k]['status'] == 'G')
Carry Over Courses
@endif
</th></tr>
</table>

                 <table class='degree'>
                 <tr>
                     
                        <th>#</th>
                        <th>MATRICULATION NUMBER</th>
                        <th>NAMES</th>
                     
                        
                        <th>Signature</th>
                          </tr>
                            <?php $c = 0; ?>
                      @foreach($item as $v)
                 
                      <?php $c = ++$c; ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td>{{$v->matric_number}}</td>

                        <td>{{strtoupper($v->surname."  ".$v->firstname."  ".$v->othername)}}</td>
                
                   
                     <td></td>
                       
                      </tr>
                     
                      @endforeach
                  </table>
                  @endforeach


                       @else
                        <p class="alert alert-warning">No Register students  is avalable</p>
                        @endif
                        
  @endif
                  </div>
                    </div>
                    </div>
                    </div>
                   
                    </body>
</html>	
             