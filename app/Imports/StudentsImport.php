<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToCollection ,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    use Importable;
    public function collection(Collection $collection)
    {
        $insert_data=array();
        $f ='';
        $d ='';
        $fos='';
    

        foreach ($collection as $row)
        {
        $user = DB::connection('mysql2')->table('users')->where('jamb_reg',$row['regno'])->first();
        if($user == null)
        {
            $password=Hash::make($row['regno']);
$insert_data[] =['matric_number'=>$row['regno'],'jamb_reg'=>$row['regno'],'surname'=>$row['lastname'],'firstname'=>$row['firstname'],
'othername'=>$row['middlename'],'programme_id'=>3,'faculty_id'=>$f,'department_id'=>$d,'fos_id'=>$fos,'password'=>$password,
'entry_year'=>'2020'];
        }
    }

 if(!empty($insert_data))
            {
                DB::connection('mysql2')->table('users')->insert($insert_data);
              return 1;
             }
             
    }
}
