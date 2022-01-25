<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class PreferenseController extends BaseController
{
    //

    public function getDepartiments()
    {
        $depts =  Department::all();

        return $this->returnResponse("Departiments list",  $depts);
    }
}
