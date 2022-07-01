<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('users', function(Blueprint $table){
            $table->foreign('dept_id')->references('id')->on('departments')->onDelete('cascade');
        });

        $depts = ["Software Department", "IT Department", "SCM Department", "CS Department", "DX Department", "Sales & Marketing Department", "Service Department"];

        foreach($depts as $dep){
            $dept = new Department();
            $dept->name = $dep;
            $dept->save();
        }

        $users = [
            (Object)[
                "name"=>"Shedrack Ikwabe",
                "username"=>"ikwabe",
                "password"=> Hash::make("ikwabe04"),
                "dept_id"=>1
            ],
            (Object)[
                "name"=>"Joram Robert",
                "username"=>"joram",
                "password"=> Hash::make("joram04"),
                "dept_id"=>2
            ],
            (Object)[
                "name"=>"Jackline Minja",
                "username"=>"jackline",
                "password"=> Hash::make("jack04"),
                "dept_id"=>3
            ],
            (Object)[
                "name"=>"James Maro",
                "username"=>"james",
                "password"=> Hash::make("james04"),
                "dept_id"=>4
            ],

        ];

        foreach($users as $us => $obj){
            $user = new User();
            $user->name = $obj->name;
            $user->username = $obj->username;
            $user->password = $obj->password;
            $user->dept_id = $obj->dept_id;
            $user->save();
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
