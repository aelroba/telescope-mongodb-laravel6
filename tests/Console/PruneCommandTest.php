<?php

namespace Laravel\Telescope\Tests\Console;

use Laravel\Telescope\Storage\EntryModel;
use Laravel\Telescope\Tests\FeatureTestCase;

class PruneCommandTest extends FeatureTestCase
{
    public function test_prune_command_will_clear_old_records()
    {
        $this->loadFactoriesUsing($this->app, __DIR__.'/../../src/Storage/factories');

        $recent = factory(EntryModel::class)->create(['created_at' => now()]);

        $old = factory(EntryModel::class)->create(['created_at' => now()->subDays(2)]);

        $this->artisan('telescope:prune')->expectsOutput('1 entries pruned.');

        $this->assertDatabaseHas('telescope_entries', ['uuid' => $recent->uuid]);

        $this->assertDatabaseMissing('telescope_entries', ['uuid' => $old->uuid]);
    }

    public function test_prune_command_can_vary_hours()
    {
        $this->loadFactoriesUsing($this->app, __DIR__.'/../../src/Storage/factories');

        $recent = factory(EntryModel::class)->create(['created_at' => now()->subHours(5)]);

        $this->artisan('telescope:prune')->expectsOutput('0 entries pruned.');

        $this->artisan('telescope:prune', ['--hours' => 4])->expectsOutput('1 entries pruned.');

        $this->assertDatabaseMissing('telescope_entries', ['uuid' => $recent->uuid]);
    }
}
