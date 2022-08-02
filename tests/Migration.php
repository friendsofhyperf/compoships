<?php

declare(strict_types=1);
/**
 * This file is part of friendsofhyperf/compoships.
 *
 * @link     https://github.com/friendsofhyperf/compoships
 * @document https://github.com/friendsofhyperf/compoships/blob/master/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Awobaz\Compoships\Tests;

// todo
use Hyperf\Database\Capsule\Manager as Capsule;
use Hyperf\Database\Migrations\Migration as BaseMigration;
use Hyperf\Database\Schema\Blueprint;

class Migration extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')
                ->unsigned()
                ->nullable();
            $table->integer('booking_id')
                ->unsigned()
                ->nullable();
            $table->integer('vehicle_id')
                ->unsigned()
                ->nullable();
            $table->timestamps();
        });

        // contains original single PK relations
        Capsule::schema()->create('original_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pcid')->nullable();
            $table->string('name')->nullable();
            $table->integer('allocation_id');

            $table->foreign('allocation_id')
                ->references('id')
                ->on('allocations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('pcid')
                ->references('pcid')
                ->on('product_codes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Capsule::schema()->create('spaces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id')
                ->unsigned();
            $table->timestamps();
        });

        Capsule::schema()->create('tracking_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id')
                ->unsigned()
                ->nullable();
            $table->integer('vehicle_id')
                ->unsigned()
                ->nullable();

            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('allocations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('vehicle_id')
                ->references('vehicle_id')
                ->on('allocations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Capsule::schema()->create('pickup_points', function (Blueprint $table) {
            $table->string('contract_number');
            $table->integer('pickup_index')
                ->unsigned();
            $table->timestamps();
        });

        Capsule::schema()->create('pickup_times', function (Blueprint $table) {
            $table->string('contract_number');
            $table->integer('pickup_index')
                ->unsigned();
            $table->string('days')
                ->unsigned();
            $table->time('pickup_time')
                ->unsigned();

            $table->foreign('pickup_index')
                ->references('pickup_index')
                ->on('pickup_point')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
        });

        Capsule::schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id')
                ->unsigned()
                ->nullable();
            $table->timestamps();
        });

        Capsule::schema()->create('product_codes', function (Blueprint $table) {
            $table->uuid('pcid')->unique();
            $table->string('code');
        });
    }
}
