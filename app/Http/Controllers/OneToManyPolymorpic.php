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

class CreatePolymorphicOneToMany extends Migration
{
    public function up()
    {
        // Posts
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        // Videos
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Comments (Polymorphic)
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('comment');

            // polymorphic fields
            $table->unsignedBigInteger('commentable_id');
            $table->string('commentable_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('posts');
    }
}

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/

class Post extends Model
{
    protected $fillable = ['title'];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

class Video extends Model
{
    protected $fillable = ['name'];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

class Comment extends Model
{
    protected $fillable = ['comment'];

    public function commentable()
    {
        return $this->morphTo();
    }
}

/*
|--------------------------------------------------------------------------
| CONTROLLER (FULL CRUD)
|--------------------------------------------------------------------------
*/

class PolymorphicController extends Controller
{
    // CREATE Post + Comments
    public function createPost()
    {
        $post = Post::create([
            'title' => 'Laravel Post'
        ]);

        $post->comments()->createMany([
            ['comment' => 'Great post'],
            ['comment' => 'Nice work']
        ]);

        return "Post with comments created";
    }

    // CREATE Video + Comments
    public function createVideo()
    {
        $video = Video::create([
            'name' => 'Laravel Video'
        ]);

        $video->comments()->create([
            'comment' => 'Awesome video'
        ]);

        return "Video with comment created";
    }

    // READ: Get all comments of a Post
    public function getPostComments($id)
    {
        $post = Post::findOrFail($id);
        return $post->comments;
    }

    // READ: Get parent (Post/Video) from Comment
    public function getCommentOwner($id)
    {
        $comment = Comment::findOrFail($id);
        return $comment->commentable;
    }

    // UPDATE Comment
    public function updateComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update([
            'comment' => 'Updated comment'
        ]);

        return "Comment updated";
    }

    // DELETE Comment
    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return "Comment deleted";
    }
}

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/create-post', [PolymorphicController::class, 'createPost']);
Route::get('/create-video', [PolymorphicController::class, 'createVideo']);

Route::get('/post-comments/{id}', [PolymorphicController::class, 'getPostComments']);
Route::get('/comment-owner/{id}', [PolymorphicController::class, 'getCommentOwner']);

Route::get('/update-comment/{id}', [PolymorphicController::class, 'updateComment']);
Route::get('/delete-comment/{id}', [PolymorphicController::class, 'deleteComment']);