<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // توسيع قائمة بوابات الدفع لتشمل paytabs
        DB::statement("ALTER TABLE `payment_transactions` MODIFY `gateway` ENUM('mada','tabby','tamara','paytabs') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `payment_transactions` MODIFY `gateway` ENUM('mada','tabby','tamara') NOT NULL");
    }
};
