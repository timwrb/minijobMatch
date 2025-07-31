<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Statamic\Facades\Path;
use Statamic\Facades\Stache;
use Statamic\Support\Str;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk as BasePreventsSavingStacheItemsToDisk;

trait PreventsSavingStacheItemsToDisk
{
    use BasePreventsSavingStacheItemsToDisk;

    protected string $fakeStacheDirectory = __DIR__.'/../fixtures/stache';

    protected array $overwrites = [
        'collections' => 'content/collections',
    ];

    protected function preventSavingStacheItemsToDisk(): void
    {
        $this->fakeStacheDirectory = Path::tidy($this->fakeStacheDirectory);
        Stache::stores()->each(function ($store, $key) {
            if (isset($this->overwrites[$key])) {
                $store->directory(__DIR__.'/../../'.$this->overwrites[$key]);

                return;
            }
            $fixturesPath = Str::before($this->fakeStacheDirectory, '/dev-null');
            $storeDirectory = '/'.Path::makeRelative($store->directory());

            $relative = Str::after(Str::after($storeDirectory, $fixturesPath), '/');

            $store->directory($this->fakeStacheDirectory.'/'.$relative);
        });
    }
}