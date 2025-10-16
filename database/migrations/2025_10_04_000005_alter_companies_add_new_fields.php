<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('companies')) {
            throw new \RuntimeException('Table "companies" does not exist. Run the base migration first.');
        }

        Schema::table('companies', function (Blueprint $table) {
    
            $table->foreignId('department_id')->constrained()->references('id')->on('department');
            $table->foreignId('city_id')->constrained()->references('id')->on('city');
            $table->foreignId('currency_id')->constrained()->references('id')->on('currency');
            $table->foreignId('invoice_provider_id')->constrained()->references('id')->on('invoice_providers');
        });
    }

    public function down()
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        try {
            Schema::table('companies', function (Blueprint $table) {
                try { $table->dropForeign(['department_id']); } catch (\Throwable $e) {}
                try { $table->dropForeign(['city_id']); } catch (\Throwable $e) {}
                try { $table->dropForeign(['currency_id']); } catch (\Throwable $e) {}
            });
        } catch (\Throwable $e) {
        }

        Schema::table('companies', function (Blueprint $table) {
            try { if (Schema::hasColumn('companies', 'currency_id')) { $table->dropColumn('currency_id'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'city_id')) { $table->dropColumn('city_id'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'department_id')) { $table->dropColumn('department_id'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'percentage_tip')) { $table->dropColumn('percentage_tip'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'width_ticket')) { $table->dropColumn('width_ticket'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'tables')) { $table->dropColumn('tables'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'change')) { $table->dropColumn('change'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'print')) { $table->dropColumn('print'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'barcode')) { $table->dropColumn('barcode'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'type_bill')) { $table->dropColumn('type_bill'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'email')) { $table->dropColumn('email'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'phone')) { $table->dropColumn('phone'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('companies', 'direction')) { $table->dropColumn('direction'); } } catch (\Throwable $e) {}
        });
    }
};


