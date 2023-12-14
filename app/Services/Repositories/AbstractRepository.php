<?php

namespace App\Services\Repositories;

use Closure;
use Illuminate\Support\Collection;

abstract class AbstractRepository
{
    protected Collection $data;

    public static function make(): static
    {
        return resolve(static::class);
    }

    /**
     * @return \Illuminate\Support\Collection<array-key, static>
     */
    protected function getDataCollection(): Collection
    {
        if (! isset($this->data)) {
            $this->data = $this->loadDataFromJsonFile();
        }

        return $this->data;
    }

    protected function loadDataFromJsonFile(): Collection
    {
        $fileContent = file_get_contents(
            getcwd().'/storage/data/'.$this->getDataSourceFile()
        );

        return collect(json_decode($fileContent, true))->map($this->resolveDataMappingCallback());
    }

    abstract protected function getDataSourceFile(): string;

    abstract protected function resolveDataMappingCallback(): Closure;
}
