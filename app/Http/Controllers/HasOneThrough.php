<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MIGRATIONS
|--------------------------------------------------------------------------
*/

Schema::create('countries', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('country_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});

Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->text('bio');
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});


/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/

class Country extends Model
{
    protected $fillable = ['name'];

    // hasOneThrough relationship
    public function profile()
    {
        return $this->hasOneThrough( Profile::class,  User::class  );
    }
}

class User extends Model
{
    protected $fillable = ['name','country_id'];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

class Profile extends Model
{
    protected $fillable = ['bio','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


/*
|--------------------------------------------------------------------------
| ROUTE / CONTROLLER DEMO
|--------------------------------------------------------------------------
*/

Route::get('/demo', function () {

    // create country
    $country = Country::create([
        'name' => 'Bangladesh'
    ]);

    // create user
    $user = User::create([
        'name' => 'Ajhar',
        'country_id' => $country->id
    ]);

    // create profile
    Profile::create([
        'bio' => 'Laravel Developer',
        'user_id' => $user->id
    ]);

    // access profile through country
    $profile = Country::find($country->id)->profile;

    return $profile;

});


<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/

class Country extends Model
{
    protected $fillable = ['name'];

    public function profile()
    {
        return $this->hasOneThrough(
            Profile::class,
            User::class,
            'country_id',
            'user_id',
            'id',
            'id'
        );
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

class User extends Model
{
    protected $fillable = ['name','country_id'];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}

class Profile extends Model
{
    protected $fillable = ['bio','user_id'];
}

/*
|--------------------------------------------------------------------------
| ROUTES = CRUD
|--------------------------------------------------------------------------
*/

/// ✅ CREATE
Route::post('/create', function (Request $request) {

    $country = Country::create([
        'name' => $request->country_name
    ]);

    $user = User::create([
        'name' => $request->user_name,
        'country_id' => $country->id
    ]);

    $profile = Profile::create([
        'bio' => $request->bio,
        'user_id' => $user->id
    ]);

    return response()->json([
        'country' => $country,
        'user' => $user,
        'profile' => $profile
    ]);
});


/// ✅ READ (Country → Profile using hasOneThrough)
Route::get('/read/{id}', function ($id) {

    $country = Country::find($id);

    return [
        'country' => $country,
        'profile_via_hasOneThrough' => $country->profile
    ];
});


/// ✅ UPDATE
Route::put('/update/{id}', function (Request $request, $id) {

    $country = Country::find($id);
    $country->update([
        'name' => $request->country_name
    ]);

    // update related user
    $user = $country->users()->first();
    $user->update([
        'name' => $request->user_name
    ]);

    // update profile
    $user->profile->update([
        'bio' => $request->bio
    ]);

    return response()->json([
        'message' => 'Updated Successfully',
        'data' => $country->load('profile')
    ]);
});


/// ✅ DELETE
Route::delete('/delete/{id}', function ($id) {

    $country = Country::find($id);

    // cascade delete (users + profiles)
    $country->delete();

    return response()->json([
        'message' => 'Deleted Successfully'
    ]);
});