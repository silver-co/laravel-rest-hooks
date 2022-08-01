<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use SilverCO\RestHooks\Enums\HttpMethods;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_hooks', function (Blueprint $table) {
            $class = Config::get('resthooks.auth_model');

            $table->id();
            $table->foreignIdFor($class)->constrained();
            $table->string('event');
            $table->string('target', 1000);
            $table->string('signature', 1000)->nullable();
            $table->enum('method', HttpMethods::toArray())
                ->default(HttpMethods::POST->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rest_hooks');
    }
};
