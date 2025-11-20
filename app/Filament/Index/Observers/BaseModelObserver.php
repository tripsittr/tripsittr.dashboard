<?php
namespace App\Filament\Index\Observers;

use App\Services\LogActivity;
use Illuminate\Database\Eloquent\Model;

class BaseModelObserver
{
    protected function entity(Model $model): string
    {
        return class_basename($model);
    }

    protected function teamId(Model $model): ?int
    {
        return $model->team_id ?? null;
    }

    public function created(Model $model): void
    {
        LogActivity::record(strtolower($this->entity($model)).'.created', $this->entity($model), $model->getKey(), $this->extractFillable($model), $this->teamId($model));
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);
        LogActivity::record(strtolower($this->entity($model)).'.updated', $this->entity($model), $model->getKey(), $changes, $this->teamId($model));
    }

    public function deleted(Model $model): void
    {
        LogActivity::record(strtolower($this->entity($model)).'.deleted', $this->entity($model), $model->getKey(), [], $this->teamId($model));
    }

    protected function extractFillable(Model $model): array
    {
        if (method_exists($model, 'getFillable')) {
            return collect($model->getFillable())
                ->mapWithKeys(fn($f) => [$f => $model->{$f} ?? null])
                ->filter(fn($v) => !is_null($v))
                ->toArray();
        }
        return [];
    }
}
