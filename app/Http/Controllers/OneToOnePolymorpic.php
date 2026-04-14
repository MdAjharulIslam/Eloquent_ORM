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

class CreatePolymorphicTables extends Migration
{
    public function up()
    {
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Posts table
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        // Images table (Polymorphic)
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('url');

            // polymorphic columns
            $table->unsignedBigInteger('imageable_id');
            $table->string('imageable_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');
    }
}

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/

class User extends Model
{
    protected $fillable = ['name'];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

class Post extends Model
{
    protected $fillable = ['title'];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

class Image extends Model
{
    protected $fillable = ['url'];

    public function imageable()
    {
        return $this->morphTo();
    }
}

/*
|--------------------------------------------------------------------------
| CONTROLLER
|--------------------------------------------------------------------------
*/

class PolymorphicController extends Controller
{
    // Create User + Image
    public function createUser()
    {
        $user = User::create([
            'name' => 'Ajharul'
        ]);

        $user->image()->create([
            'url' => 'user.jpg'
        ]);

        return "User with image created";
    }

    // Create Post + Image
    public function createPost()
    {
        $post = Post::create([
            'title' => 'Laravel Polymorphic'
        ]);

        $post->image()->create([
            'url' => 'post.jpg'
        ]);

        return "Post with image created";
    }

    // Read Image from User
    public function getUserImage($id)
    {
        $user = User::findOrFail($id);
        return $user->image;
    }

    // Read Parent from Image
    public function getImageOwner($id)
    {
        $image = Image::findOrFail($id);
        return $image->imageable;
    }

    // Update Image
    public function updateImage($id)
    {
        $image = Image::findOrFail($id);
        $image->update([
            'url' => 'updated.jpg'
        ]);

        return "Image updated";
    }

    // Delete Image
    public function deleteImage($id)
    {
        $image = Image::findOrFail($id);
        $image->delete();

        return "Image deleted";
    }
}

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/create-user', [PolymorphicController::class, 'createUser']);
Route::get('/create-post', [PolymorphicController::class, 'createPost']);

Route::get('/user-image/{id}', [PolymorphicController::class, 'getUserImage']);
Route::get('/image-owner/{id}', [PolymorphicController::class, 'getImageOwner']);

Route::get('/update-image/{id}', [PolymorphicController::class, 'updateImage']);
Route::get('/delete-image/{id}', [PolymorphicController::class, 'deleteImage']);