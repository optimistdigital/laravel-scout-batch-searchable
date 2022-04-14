<?php

namespace OptimistDigital\ScoutBatchSearchable;

use Illuminate\Support\Facades\Cache;
use OptimistDigital\ScoutBatchSearchable\Commands\CheckBatchIndexStatus;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/scout.php', 'scout');
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckBatchIndexStatus::class,
            ]);
        }
    }

    public static function getBatchedModelsClasses()
    {
        return Cache::get('BATCH_SEARCHABLE_QUEUED_MODELS') ?? [];
    }

    public static function addBatchedModelClass($className)
    {
        $queuedModels = static::getBatchedModelsClasses();
        $queuedModels[] = $className;
        $queuedModels = array_unique($queuedModels);
        Cache::put('BATCH_SEARCHABLE_QUEUED_MODELS', $queuedModels);

        return $queuedModels;
    }

    public static function removeBatchedModelClass($className)
    {
        $queuedModels = static::getBatchedModelsClasses();
        $queuedModels = array_filter($queuedModels, function ($cls) use ($className) {
            return $cls !== $className;
        });
        Cache::put('BATCH_SEARCHABLE_QUEUED_MODELS', $queuedModels);

        return $queuedModels;
    }
}
