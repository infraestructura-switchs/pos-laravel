<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        // If the table doesn't exist, create it according to the provided SQL
        if (! Schema::hasTable('stock_movements_detail')) {
            Schema::create('stock_movements_detail', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('stock_movements_id');
                $table->unsignedBigInteger('product_id');
                $table->enum('movement_type', ['IN', 'OUT'])->default('IN');
                $table->decimal('quantity', 10, 2);
                $table->decimal('unit_cost', 10, 2)->nullable();
                $table->decimal('total_cost', 10, 2)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index('stock_movements_id', 'stock_movements_detail_stock_movements_id_index');
                $table->index('product_id', 'stock_movements_detail_product_id_index');

                // Add foreign keys only if referenced tables exist
                if (Schema::hasTable('products')) {
                    $table->foreign('product_id', 'stock_movements_detail_product_id_foreign')
                        ->references('id')->on('products')->onDelete('cascade');
                }
                if (Schema::hasTable('stock_movements')) {
                    $table->foreign('stock_movements_id', 'stock_movements_detail_stock_movements_id_foreign')
                        ->references('id')->on('stock_movements')->onDelete('cascade');
                }
            });
            return;
        }

        // Otherwise alter the existing table: add missing columns, indexes and FKs
        Schema::table('stock_movements_detail', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_movements_detail', 'id')) {
                $table->bigIncrements('id')->first();
            }
            if (! Schema::hasColumn('stock_movements_detail', 'stock_movements_id')) {
                $table->unsignedBigInteger('stock_movements_id')->after('id');
            }
            if (! Schema::hasColumn('stock_movements_detail', 'product_id')) {
                $table->unsignedBigInteger('product_id')->after('stock_movements_id');
            }
            if (! Schema::hasColumn('stock_movements_detail', 'movement_type')) {
                $table->enum('movement_type', ['IN', 'OUT'])->default('IN')->after('product_id');
            }
            if (! Schema::hasColumn('stock_movements_detail', 'quantity')) {
                $table->decimal('quantity', 10, 2)->after('movement_type');
            }
            if (! Schema::hasColumn('stock_movements_detail', 'unit_cost')) {
                $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity');
            }
            if (! Schema::hasColumn('stock_movements_detail', 'total_cost')) {
                $table->decimal('total_cost', 10, 2)->nullable()->after('unit_cost');
            }
            if (! Schema::hasColumn('stock_movements_detail', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('total_cost');
            }
            if (! Schema::hasColumn('stock_movements_detail', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        // Add indexes if missing
        try {
            if (Schema::hasColumn('stock_movements_detail', 'stock_movements_id')) {
                Schema::table('stock_movements_detail', function (Blueprint $table) {
                    $table->index('stock_movements_id', 'stock_movements_detail_stock_movements_id_index');
                });
            }
        } catch (\Throwable $e) {}
        try {
            if (Schema::hasColumn('stock_movements_detail', 'product_id')) {
                Schema::table('stock_movements_detail', function (Blueprint $table) {
                    $table->index('product_id', 'stock_movements_detail_product_id_index');
                });
            }
        } catch (\Throwable $e) {}

        // Add foreign keys if target tables exist and FK doesn't already exist
        if (Schema::hasTable('products') && Schema::hasColumn('stock_movements_detail', 'product_id') && ! $this->foreignKeyExists('stock_movements_detail', 'stock_movements_detail_product_id_foreign')) {
            Schema::table('stock_movements_detail', function (Blueprint $table) {
                $table->foreign('product_id', 'stock_movements_detail_product_id_foreign')
                    ->references('id')->on('products')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('stock_movements') && Schema::hasColumn('stock_movements_detail', 'stock_movements_id') && ! $this->foreignKeyExists('stock_movements_detail', 'stock_movements_detail_stock_movements_id_foreign')) {
            Schema::table('stock_movements_detail', function (Blueprint $table) {
                $table->foreign('stock_movements_id', 'stock_movements_detail_stock_movements_id_foreign')
                    ->references('id')->on('stock_movements')->onDelete('cascade');
            });
        }
    }

    protected function foreignKeyExists(string $table, string $indexName): bool
    {
        try {
            $connection = Schema::getConnection();
            $database = $connection->getDatabaseName();
            $row = $connection->selectOne(
                'SELECT COUNT(*) as cnt FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
                [$database, $table, $indexName]
            );
            if ($row && (isset($row->cnt) ? $row->cnt : (isset($row['cnt']) ? $row['cnt'] : 0)) > 0) {
                return true;
            }
        } catch (\Throwable $e) {
        }

        try {
            $connection = Schema::getConnection();
            $sm = $connection->getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys($table);
            foreach ($foreignKeys as $fk) {
                if ($fk->getName() === $indexName) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
        }

        return false;
    }

    public function down()
    {
        if (! Schema::hasTable('stock_movements_detail')) {
            return;
        }

        // drop foreign keys if exist
        try {
            Schema::table('stock_movements_detail', function (Blueprint $table) {
                try { $table->dropForeign('stock_movements_detail_product_id_foreign'); } catch (\Throwable $e) {}
                try { $table->dropForeign('stock_movements_detail_stock_movements_id_foreign'); } catch (\Throwable $e) {}
            });
        } catch (\Throwable $e) {
        }

        // drop indexes and columns that may have been added by this migration
        Schema::table('stock_movements_detail', function (Blueprint $table) {
            try { $table->dropIndex('stock_movements_detail_stock_movements_id_index'); } catch (\Throwable $e) {}
            try { $table->dropIndex('stock_movements_detail_product_id_index'); } catch (\Throwable $e) {}

            try { if (Schema::hasColumn('stock_movements_detail', 'total_cost')) { $table->dropColumn('total_cost'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('stock_movements_detail', 'unit_cost')) { $table->dropColumn('unit_cost'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('stock_movements_detail', 'quantity')) { $table->dropColumn('quantity'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('stock_movements_detail', 'movement_type')) { $table->dropColumn('movement_type'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('stock_movements_detail', 'product_id')) { $table->dropColumn('product_id'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('stock_movements_detail', 'stock_movements_id')) { $table->dropColumn('stock_movements_id'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('stock_movements_detail', 'updated_at')) { $table->dropColumn('updated_at'); } } catch (\Throwable $e) {}
            try { if (Schema::hasColumn('stock_movements_detail', 'created_at')) { $table->dropColumn('created_at'); } } catch (\Throwable $e) {}
            // do not drop id primary if it existed before this migration
        });
    }
};