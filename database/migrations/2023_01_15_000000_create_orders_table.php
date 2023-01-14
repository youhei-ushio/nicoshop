<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('customer_user_id')->comment('注文者ID');
            $table->date('order_date')->comment('注文日');
            $table->boolean('accepted')->comment('受付済みかどうか');
            $table->dateTime('accepted_at')->nullable()->comment('受付日');
            $table->boolean('finished')->comment('完了済みかどうか');
            $table->dateTime('finished_at')->nullable()->comment('完了日');
            $table->index('order_date');
            $table->index('accepted');
            $table->index('finished');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('注文ID');
            $table->unsignedBigInteger('product_id')->comment('商品ID');
            $table->unsignedInteger('quantity')->comment('数量');
            $table->unique(['order_id', 'product_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
