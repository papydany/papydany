<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WithdralExport implements FromView ,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $reg;
    private $w;
    private $p;
        
    public function __construct(array $withral, array $wc, array $probation, int $d, string $s, int $f,int $old)
    {
       $this->reg = $withral;
       $this->w =$wc;
       $this->p =$probation;
       $this->d =$d;
       $this->s =$s;
       $this->f=$f;
       $this->old =$old;
       
     
    }
    public function view(): View
    {
    
            return view('admin.course_with_no_result.withdral', [
                'reg' => $this->reg,'w'=>$this->w,'p'=>$this->p,'d'=>$this->d,'s'=>$this->s,'f'=>$this->f,'old'=>$this->old
            ]);
        }
        

       }
    

