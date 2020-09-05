<?php

use App\Tag;
use App\Post;
use App\User;
use App\Vote;
use App\Comment;
use App\Category;
use App\Jobs\MyFirstJob;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use App\Events\MyFirstEvent;
use Illuminate\Http\Request;
use App\Events\MySecondEvent;
use App\Mail\FirstSampleMail;
use App\Jobs\MyExceptionalJob;
use App\Mail\SecondSampleMail;
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\MyQueuedNotification;
use App\Http\Middleware\AuthGatesMiddleware;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('landingpage');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/request', function (Request $request) {

    //commands
    Artisan::call('my-sample-Command');

    //jobs

    collect(range(5,20,5))
        ->chunk(5)
        ->map(function($collection,$key){
            $collection->each(function($value){
                Queue::push(new MyFirstJob($value));   
            });
        });

    //Models

    $userBaseQuery = User::query()->whereNotIn('email',['someOne@gmail.com']);
    $postBaseQuery = Post::query();
    $categoryBaseQuery = Category::query();
    $tagBaseQuery = Tag::query();
    $commentBaseQuery = Comment::query();
    $voteBaseQuery = Vote::query();

    $creationCount = 1;
    $updationCount = 1;
    $deletionCount = 1;

    factory(User::class,$creationCount)->create();

    $userBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $userBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Post::class,$creationCount)->create();

    $postBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $postBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();
            
    factory(Category::class,$creationCount)->create();

    $categoryBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $categoryBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Tag::class,$creationCount)->create();

    $tagBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $tagBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Comment::class,$creationCount)->create();

    $commentBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $commentBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Vote::class,$creationCount)->create();

    $voteBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $voteBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    //Events

    MyFirstEvent::dispatch(1);
    MySecondEvent::dispatch(1);

    //Mails

    Mail::to(['testuser@test.com','testusertwo@test.com'])
    ->cc(['ccon@gmail.com'])
    ->bcc([])
    ->send(new FirstSampleMail());

    Mail::to(['testusertwo@test.com','testusertwotwo@test.com'])
    ->cc(['ccontwo@gmail.com'])
    ->bcc([])
    ->send(new SecondSampleMail());

    //Notification

    tap(User::query()
        ->inRandomOrder()
        ->first(),function($user){
            $user->notify(new InvoicePaid());
            $user->notify((new MyQueuedNotification())->delay(now()->addSeconds(10)));
        });

        tap(User::query()
        ->inRandomOrder()
        ->take(2)->get(),function($users){
            Notification::send($users,new InvoicePaid());
            Notification::send($users,new MyQueuedNotification());
        });

    //Cache

    Cache::remember('allUsers', CarbonInterval::minutes(2), function () {
        return User::all(['id','name','email']);
    });

    return 'Request Done';
})->name('request');

Route::get('/commands', function () {

    Artisan::call('inspire');
    Artisan::call('my-sample-Command');

    return 'Command Executed';
})->name('commands');

Route::get('/jobs', function () {

    collect(range(5,300,5))
        ->chunk(10)
        ->map(function($collection,$key){
            $collection->each(function($value){
                MyFirstJob::dispatch($value);
            });
        });

    collect(range(1,20))
        ->chunk(10)
        ->map(function($collection,$key){
            $collection->each(function($value){
                MyExceptionalJob::dispatch($value);
            });
        });

    return 'Jobs Dispatched';
})->name('jobs');

Route::get('/exceptions', function () {

    collect(range(1,20))
        ->chunk(10)
        ->map(function($collection,$key){
            $collection->each(function($value){
                Queue::push(new MyExceptionalJob($value));   
            });
        });

        throw new \Exception("Here is My Exception Message");

    return 'Jobs Dispatched';
})->name('exceptions');

Route::get('/logs', function () {

    collect([
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug'
    ])
    ->each(function($logType){
        Log::{$logType}((string) Str::of('This is ')->append(ucfirst($logType))->append(' Message'));
    });

    return 'Logs Generated';
})->name('logs');

Route::get('/views', function () {
    return view('views');
})->name('views');

Route::get('/queries', function () {

    collect([
        new User,
        new Category,
        new Comment,
        new Post,
        new Tag,
        new Vote,
    ])
    ->each(function(Model $eachClass){

        $eachClass::query()
            ->take(rand(5,20))
            ->where('name','LIKE',Str::random(3))
            ->get();
    });

    return 'Query Executed';
})->name('queries');

Route::get('/models', function () {

    $userBaseQuery = User::query()->whereNotIn('email',['someOne@gmail.com']);
    $postBaseQuery = Post::query();
    $categoryBaseQuery = Category::query();
    $tagBaseQuery = Tag::query();
    $commentBaseQuery = Comment::query();
    $voteBaseQuery = Vote::query();

    $creationCount = 3;
    $updationCount = 2;
    $deletionCount = 1;

    factory(User::class,$creationCount)->create();

    $userBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $userBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Post::class,$creationCount)->create();

    $postBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $postBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();
            
    factory(Category::class,$creationCount)->create();

    $categoryBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $categoryBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Tag::class,$creationCount)->create();

    $tagBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $tagBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Comment::class,$creationCount)->create();

    $commentBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $commentBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    factory(Vote::class,$creationCount)->create();

    $voteBaseQuery
            ->inRandomOrder()
            ->take($updationCount)
            ->get()
            ->each
            ->updateName();
    $voteBaseQuery
            ->inRandomOrder()
            ->take($deletionCount)
            ->get()
            ->each
            ->delete();

    return 'Model Created, Updated, Deleted';
})->name('models');

Route::get('events', function () {

    MyFirstEvent::dispatch(4);
    MySecondEvent::dispatch(10);
    
    return 'Event Dispatched';
})->name('events');

Route::get('mail', function () {

    Mail::to(['testuser@test.com','testusertwo@test.com'])
    ->cc(['ccon@gmail.com'])
    ->bcc([])
    ->send(new FirstSampleMail());

    Mail::to(['testusertwo@test.com','testusertwotwo@test.com'])
    ->cc(['ccontwo@gmail.com'])
    ->bcc([])
    ->send(new SecondSampleMail());

})->name('emails');

Route::get('notifications', function () {

    tap(User::query()
        ->inRandomOrder()
        ->first(),function($user){
            $user->notify(new InvoicePaid());
            $user->notify((new MyQueuedNotification())->delay(now()->addSeconds(10)));
        });

        tap(User::query()
        ->inRandomOrder()
        ->take(2)->get(),function($users){
            Notification::send($users,new InvoicePaid());
            Notification::send($users,new MyQueuedNotification());
        });

        return User::query()
            ->with('notifications')
            ->whereHas('notifications')
            ->get();
return 'Notification Sent';
})->name('notifications');

Route::get('gates', function (Request $request) {    

    $cacheKey = Str::of('PaginatedPosts')
                    ->append('==||==')
                    ->append(http_build_query($request->all()))
                    ->__toString();
    $paginatedPosts = Cache::remember(md5($cacheKey),CarbonInterval::minutes(2),function () {
            return Post::query()->paginate(10);
    });

    return view('gates',compact('paginatedPosts'));
})
->middleware([AuthGatesMiddleware::class])
->name('gates');

Route::get('cache', function (Request $request) {  
    
    Cache::remember('allUsers', CarbonInterval::minutes(2), function () {
        return User::all(['id','name','email']);
    });

    Cache::rememberForever('allPosts',function () {
            return Post::all(['id','name']);
    });

    $cacheKey = Str::of('PaginatedPosts')
                    ->append('==||==')
                    ->append(http_build_query($request->all()))
                    ->__toString();

    $paginatedPosts = Cache::remember(md5($cacheKey),CarbonInterval::minutes(2),function () {
            return Post::query()->paginate(10);
    });

    return view('gates',compact('paginatedPosts'));
    
})->name('cache');