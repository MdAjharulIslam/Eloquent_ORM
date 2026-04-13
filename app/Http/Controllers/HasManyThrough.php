<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| MIGRATION
|--------------------------------------------------------------------------
*/
class CreateTables extends Migration
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

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
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

    // 🔥 hasManyThrough
    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            Enrollment::class,
            'student_id', // FK on enrollments
            'id',         // PK on courses
            'id',         // PK on students
            'course_id'   // FK on enrollments
        );
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}

class Course extends Model
{
    protected $fillable = ['title'];
}

class Enrollment extends Model
{
    protected $fillable = ['student_id', 'course_id'];
}

/*
|--------------------------------------------------------------------------
| CONTROLLER
|--------------------------------------------------------------------------
*/

class StudentController extends Controller
{
    // ✅ CREATE (Student + Courses)
    public function store()
    {
        $student = Student::create(['name' => 'Ajhar']);

        $c1 = Course::create(['title' => 'Math']);
        $c2 = Course::create(['title' => 'Physics']);

        Enrollment::create(['student_id' => $student->id, 'course_id' => $c1->id]);
        Enrollment::create(['student_id' => $student->id, 'course_id' => $c2->id]);

        return "Created successfully";
    }

    // ✅ READ (Single)
    public function show($id)
    {
        $student = Student::find($id);

        return [
            'student' => $student,
            'courses' => $student->courses
        ];
    }

    // ✅ READ ALL
    public function index()
    {
        return Student::with('courses')->get();
    }

    // ✅ UPDATE (Student + Courses)
    public function update($id)
    {
        $student = Student::find($id);

        // update student
        $student->update(['name' => 'Updated Name']);

        // remove old enrollments
        Enrollment::where('student_id', $id)->delete();

        // add new courses
        Enrollment::create(['student_id' => $id, 'course_id' => 1]);
        Enrollment::create(['student_id' => $id, 'course_id' => 2]);

        return "Updated successfully";
    }

    // ✅ ADD COURSE
    public function addCourse($id)
    {
        Enrollment::create([
            'student_id' => $id,
            'course_id' => 3
        ]);

        return "Course added";
    }

    // ✅ REMOVE COURSE
    public function removeCourse($id)
    {
        Enrollment::where('student_id', $id)
            ->where('course_id', 1)
            ->delete();

        return "Course removed";
    }

    // ✅ DELETE
    public function destroy($id)
    {
        Student::find($id)->delete(); // cascade delete

        return "Deleted successfully";
    }
}

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/create', [StudentController::class, 'store']);
Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/{id}', [StudentController::class, 'show']);
Route::get('/update/{id}', [StudentController::class, 'update']);
Route::get('/add-course/{id}', [StudentController::class, 'addCourse']);
Route::get('/remove-course/{id}', [StudentController::class, 'removeCourse']);
Route::get('/delete/{id}', [StudentController::class, 'destroy']);