<?php

namespace App\Providers;

use App\Entities\Book;
use App\Entities\Page;
use App\Entities\Chapter;
use App\Settings\Setting;
use App\Entities\Bookshelf;
use App\Settings\SettingService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Entities\BreadcrumbsViewComposer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService($app->make(Setting::class), $app->make('Illuminate\Contracts\Cache\Repository'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set root URL
        $appUrl = config('app.url');
        if ($appUrl) {
            $isHttps = (strpos($appUrl, 'https://') === 0);
            URL::forceRootUrl($appUrl);
            URL::forceScheme($isHttps ? 'https' : 'http');
        }

        // Custom validation methods
        Validator::extend('image_extension', function ($attribute, $value, $parameters, $validator) {
            $validImageExtensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'tiff', 'webp'];
            return in_array(strtolower($value->getClientOriginalExtension()), $validImageExtensions);
        });

        Validator::extend('no_double_extension', function ($attribute, $value, $parameters, $validator) {
            $uploadName = $value->getClientOriginalName();
            return substr_count($uploadName, '.') < 2;
        });

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        Blade::directive('exposeTranslations', function ($expression) {
            return "<?php \$__env->startPush('translations'); ?>" .
                "<?php foreach({$expression} as \$key): ?>" .
                '<meta name="translation" key="<?php echo e($key); ?>" value="<?php echo e(trans($key)); ?>">' . "\n" .
                "<?php endforeach; ?>" .
                '<?php $__env->stopPush(); ?>';
        });

        // Allow longer string lengths after upgrade to utf8mb4
        Schema::defaultStringLength(191);

        // Set morph-map due to namespace changes
        Relation::morphMap([
            'App\\App' => Bookshelf::class,
            'App\\Book' => Book::class,
            'App\\Chapter' => Chapter::class,
            'App\\Page' => Page::class,
        ]);

        // View Composers
        View::composer('partials.breadcrumbs', BreadcrumbsViewComposer::class);
    }
}
