<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Contact;
class studentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $students=  Student::with('contact')->get();
    //   $students=  Student::with('contact')->find(20);

    // $students = Student::where('gender',"Female")
    // ->withWhereHas('contact', function($query){
    //                             $query->where('student_id','>',"10");})->get();
      return $students;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $students = Student::create([
        //  'name' => 'ajharul islam',
        //  'age' =>'22',
        //  'gender'=>'Male'
        // ]);

        // $students->contact()->create([
        //           'email'=>'ajharulislam@gmail.com',
        //           'phone'=> '01795277954',
        //           'city' => 'dhaka'
        // ]);

        

        // for insert in one to many relation

        $students = Student::find(3);

        $students->contact()->create([
                  'email'=>'ajharulislam@gmail.com',
                  'phone'=> '01795277954',
                  'city' => 'dhaka'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
