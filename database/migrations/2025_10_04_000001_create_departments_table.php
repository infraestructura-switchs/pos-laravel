<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('department')) {
            Schema::create('department', function (Blueprint $table) {
                $table->id('id');
                $table->integer('department_code');
                $table->string('department_name');
                $table->string('status')->default('ACTIVE');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        } else {
            Schema::table('department', function (Blueprint $table) {
                if (! Schema::hasColumn('department', 'department_code')) {
                    $table->integer('department_code')->after('id');
                }
                if (! Schema::hasColumn('department', 'department_name')) {
                    $table->string('department_name')->nullable()->after('department_code');
                }
                if (! Schema::hasColumn('department', 'status')) {
                    $table->string('status')->default('ACTIVE')->after('department_name');
                }
                if (! Schema::hasColumn('department', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->useCurrent();
                }
                if (! Schema::hasColumn('department', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department');
    }
};


