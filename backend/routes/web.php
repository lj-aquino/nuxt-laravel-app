use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-route', function () {
    return 'This is a test route!';
});
