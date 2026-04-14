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

class CreatePolymorphicManyToMany extends Migration
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

        // Tags
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Pivot (Polymorphic)
        Schema::create('taggables', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tag_id');

            // polymorphic fields
            $table->unsignedBigInteger('taggable_id');
            $table->string('taggable_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
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

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}

class Video extends Model
{
    protected $fillable = ['name'];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}

class Tag extends Model
{
    protected $fillable = ['name'];

    // inverse relations
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }

    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}

/*
|--------------------------------------------------------------------------
| CONTROLLER (CRUD)
|--------------------------------------------------------------------------
*/

class PolymorphicController extends Controller
{
    // CREATE Post + Attach Tags
    public function createPost()
    {
        $post = Post::create([
            'title' => 'Laravel Post'
        ]);

        $tag1 = Tag::create(['name' => 'PHP']);
        $tag2 = Tag::create(['name' => 'Laravel']);

        $post->tags()->attach([$tag1->id, $tag2->id]);

        return "Post with tags created";
    }

    // CREATE Video + Attach Tag
    public function createVideo()
    {
        $video = Video::create([
            'name' => 'Laravel Video'
        ]);

        $tag = Tag::create(['name' => 'Tutorial']);

        $video->tags()->attach($tag->id);

        return "Video with tag created";
    }

    // READ: Get Tags of Post
    public function getPostTags($id)
    {
        $post = Post::findOrFail($id);
        return $post->tags;
    }

    // READ: Get all Posts of a Tag
    public function getTagPosts($id)
    {
        $tag = Tag::findOrFail($id);
        return $tag->posts;
    }

    // UPDATE: Sync Tags
    public function updatePostTags($id)
    {
        $post = Post::findOrFail($id);

        $newTag = Tag::create(['name' => 'Updated']);

        $post->tags()->sync([$newTag->id]);

        return "Tags updated";
    }

    // DELETE: Detach Tag
    public function deleteTagFromPost($postId, $tagId)
    {
        $post = Post::findOrFail($postId);
        $post->tags()->detach($tagId);

        return "Tag removed from post";
    }
}

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/create-post', [PolymorphicController::class, 'createPost']);
Route::get('/create-video', [PolymorphicController::class, 'createVideo']);

Route::get('/post-tags/{id}', [PolymorphicController::class, 'getPostTags']);
Route::get('/tag-posts/{id}', [PolymorphicController::class, 'getTagPosts']);

Route::get('/update-post-tags/{id}', [PolymorphicController::class, 'updatePostTags']);
Route::get('/delete-tag/{postId}/{tagId}', [PolymorphicController::class, 'deleteTagFromPost']);