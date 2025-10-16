<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('currency')) {
            Schema::create('currency', function (Blueprint $table) {
                $table->id('id');
                $table->string('description', 400)->nullable();
                $table->string('acronym', 400)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        } else {
            Schema::table('currency', function (Blueprint $table) {
                if (! Schema::hasColumn('currency', 'description')) {
                    $table->string('description', 400)->nullable()->after('id');
                }
                if (! Schema::hasColumn('currency', 'acronym')) {
                    $table->string('acronym', 400)->nullable()->after('description');
                }
                if (! Schema::hasColumn('currency', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->useCurrent();
                }
                if (! Schema::hasColumn('currency', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('currency');
    }
};


