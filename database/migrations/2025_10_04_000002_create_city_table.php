<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('city')) {
            Schema::create('city', function (Blueprint $table) {
                $table->id('id');
                $table->integer('city_code');
                $table->string('city_name');
                $table->foreignId('department_id')->constrained()->references('id')->on('department');
                $table->string('status')->default('ACTIVE');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

               
            });
        } else {
            Schema::table('city', function (Blueprint $table) {
                if (! Schema::hasColumn('city', 'city_code')) {
                    $table->integer('city_code')->after('city_id');
                }
                if (! Schema::hasColumn('city', 'city_name')) {
                    $table->string('city_name')->nullable()->after('city_code');
                }
                if (! Schema::hasColumn('city', 'department_id')) {
                    $table->unsignedBigInteger('department_id')->nullable()->after('city_name');
                }
                if (! Schema::hasColumn('city', 'status')) {
                    $table->string('status')->default('ACTIVE')->after('department_id');
                }
                if (! Schema::hasColumn('city', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->useCurrent();
                }
                if (! Schema::hasColumn('city', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                }

                $table->foreign('department_id')->references('id')->on('department');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('city');
    }
};


