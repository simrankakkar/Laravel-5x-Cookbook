<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;



Route::group(['middleware' => ['web']], function () {

    Route::auth();

    Route::group(['middleware' => ['is_admin']], function () {
        Route::get('/admin/users', function () {
            return "You are here";
        })->name('admin.users');
    });

    Route::get('/facebook/redirect', 'FacebookAuthController@redirect');

    Route::get('/facebook/callback', 'FacebookAuthController@callback');

    Route::post('/api/v1/test', 'TestController@foo');

    Route::get('/api/v1/token', function(){
        return csrf_token();
    });
    
    Route::get('/test_cors', 'TestController@view');


    Route::post('api/v1/favorite', 'FavoriteCreate@create')->name('favorite.create');
    Route::delete('api/v1/favorite/{comic_id}', 'FavoriteRemove@remove')->name('favorite.remove');

    Route::get('profile/edit', 'ProfileEditController@getAuthenticatedUsersProfileToEdit')->name('profile.edit');

    Route::put('profile/edit', 'ProfileEditController@updateAuthenticatedUsersProfile')->name('profile.update');

    Route::get('profile', 'ProfileShowController@getAuthenticatedUsersProfile')->name('profile');

    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    Route::get('/about', ['as' => 'about', function () {
        $title = "About";
        return view('about', compact('title'));
    }]);

    Route::resource("users", "UserController"); // Add this line in routes.php

    Route::get('/api/v1/search', ['as' => 'search',
        'uses' => 'SearchComics@searchComicsByName']);

    Route::get('/show_message', function () {
       return redirect('/')->with("message", "Hello There");
    });

    Route::resource("wish_lists", "WishListController");

    /**
     * Example of Fake API
     *
     */
    if (env('MARVEL_API_FAKE') == true) {
        Route::get('/v1/public/comics', function () {
            Log::info(sprintf("Request coming in %s", env('MARVEL_API_FAKE')));
            if (Request::input('name')) {
                Log::info("This one had a name");
                $fixture = File::get(base_path('tests/fixtures/results_no_name.json'));
                $data = ['data' => json_decode($fixture, true)];
            } else {
                $fixture = File::get(base_path('tests/fixtures/results_no_name.json'));

                $data = ['data' => json_decode($fixture, true)];
            }

            return Response::json($data);
        });
    }
});
