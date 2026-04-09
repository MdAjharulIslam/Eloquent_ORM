<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;

/*
|--------------------------------------------------------------------------
| MIGRATION
|--------------------------------------------------------------------------
*/
class CreateStudentCourseTables extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('course_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_student');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('students');
    }
}

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/
class Student extends Model
{
    protected $fillable = ['name'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student');
    }
}

class Course extends Model
{
    protected $fillable = ['title'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student');
    }
}

/*
|--------------------------------------------------------------------------
| CONTROLLER
|--------------------------------------------------------------------------
*/
class StudentController extends Controller
{
    // Create Student + Attach Courses
    public function store()
    {
        $student = Student::create([
            'name' => 'Ajhar'
        ]);

        // Create courses (for demo)
        $c1 = Course::create(['title' => 'Math']);
        $c2 = Course::create(['title' => 'Physics']);
        $c3 = Course::create(['title' => 'Chemistry']);

        // Attach courses
        $student->courses()->attach([$c1->id, $c2->id]);

        return "Student created with courses";
    }

    // Show student with courses
    public function show($id)
    {
        $student = Student::with('courses')->find($id);
        return $student;
    }

    // Sync (update courses)
    public function update($id)
    {
        $student = Student::find($id);

        // Sync new courses
        $student->courses()->sync([2, 3]);

        return "Courses synced";
    }

    // Add course
    public function addCourse($id)
    {
        $student = Student::find($id);

        $student->courses()->attach(3);

        return "Course added";
    }

    // Remove course
    public function removeCourse($id)
    {
        $student = Student::find($id);

        $student->courses()->detach(1);

        return "Course removed";
    }
}

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/store', [StudentController::class, 'store']);
Route::get('/show/{id}', [StudentController::class, 'show']);
Route::get('/update/{id}', [StudentController::class, 'update']);
Route::get('/add/{id}', [StudentController::class, 'addCourse']);
Route::get('/remove/{id}', [StudentController::class, 'removeCourse']);