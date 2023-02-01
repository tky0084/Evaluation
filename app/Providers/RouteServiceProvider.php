<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * ログインした後にリダイレクトするぺージを指定する
     * public constで定数を指定する（public:クラス内、クラス外のどこからでもアクセス可能）
     * オーナーとｱﾄﾞﾐﾝそれぞれを指定する
     */
    public const HOME = '/dashboard';
    public const OWNER_HOME = 'owner/dashboard';
    public const ADMIN_HOME = 'admin/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     * root情報を設定する
     * 
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api') 
                ->middleware('api') // フロント側を全てJavaScriptで作る場合に指定する
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::prefix('owner')
                ->as('owner.')
                ->middleware('web') // root、クラス、モデルを読み込む形で作る場合に指定する
                ->namespace($this->namespace)
                ->group(base_path('routes/owner.php'));

            Route::prefix('admin')
                ->as('admin.')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));

            Route::prefix('/')
                ->as('user.')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
