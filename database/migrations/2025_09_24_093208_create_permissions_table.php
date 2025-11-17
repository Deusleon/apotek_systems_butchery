<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->string('category', 50)->nullable();
        });
        
        DB::table('permissions')->insert([
            // Modules
            ['name' => 'View Dashboard', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['name' => 'View Sales', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['name' => 'View Purchasing', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['name' => 'View Inventory', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            // ['name' => 'View Transport', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['name' => 'View Accounting', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['name' => 'View Reports', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:17:51', 'updated_at' => '2024-11-11 20:17:51', 'category' => 'MODULES'],
            ['name' => 'View Settings', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            // Dashboard Sub Modules
           ['name' => 'View Sales Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:49:39', 'updated_at' => '2024-11-15 16:49:39', 'category' => 'DASHBOARD'],
            ['name' => 'View Purchasing Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:49:56', 'updated_at' => '2024-11-15 16:49:56', 'category' => 'DASHBOARD'],
            ['name' => 'View Inventory Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:50:12', 'updated_at' => '2024-11-15 16:50:12', 'category' => 'DASHBOARD'],
            // ['name' => 'View Transport Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:50:28', 'updated_at' => '2024-11-15 16:50:28', 'category' => 'DASHBOARD'],
            ['name' => 'View Accounting Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:50:28', 'updated_at' => '2024-11-15 16:50:28', 'category' => 'DASHBOARD'],
            // Sales Sub Modules
            ['name' => 'View Cash Sales', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Credit Sales', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Credit Tracking', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Credit Payments', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'Add Credit Payment', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Sales Order', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Order List', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:12:08', 'updated_at' => '2024-11-15 19:12:08', 'category' => 'SALES'],
            ['name' => 'Print Sales Orders', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:12:08', 'updated_at' => '2024-11-15 19:12:08', 'category' => 'SALES'],
            ['name' => 'Edit Sales Orders', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'Convert Sales Orders', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Sales History', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'Print Sales History', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Sales Returns', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Sales Returns Approvals', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'Approve Sales Returns', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'View Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'Add Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'Edit Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['name' => 'Delete Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            // Inventory Sub Modules
            ['name' => 'View Product List', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Add Products', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Edit Products', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Delete Products', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Products Import', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Current Stock', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Current Stock Value', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Old Stock Value', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Stock Details', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Edit Stock Details', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Price List', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Edit Price List', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Stock Adjustment', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Create Stock Adjustment', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Stock Requisition', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Create Stock Requisition', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Edit Stock Requisition', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Print Stock Requisition', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Stock Issue', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Approve Stock Issue', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Issue Stock', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Issue History', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Print Stock Issue', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Stock Transfer', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'Create Stock Transfer', 'guard_name' => 'web', 'created_at' => '2025-07-06 19:12:58', 'updated_at' => '2025-07-06 19:12:58', 'category' => 'INVENTORY'],
            ['name' => 'Edit Stock Transfer', 'guard_name' => 'web', 'created_at' => '2025-07-06 19:12:58', 'updated_at' => '2025-07-06 19:12:58', 'category' => 'INVENTORY'],
            ['name' => 'Approve Stock Transfer', 'guard_name' => 'web', 'created_at' => '2025-07-06 19:12:58', 'updated_at' => '2025-07-06 19:12:58', 'category' => 'INVENTORY'],
            ['name' => 'Acknowledge Stock Transfer', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Stock Count', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Outgoing Stock', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Inv. Count Sheet', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['name' => 'View Stock Taking', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            // Settings Sub Modules
            ['name' => 'View General', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:05:31', 'updated_at' => '2024-11-11 20:05:31', 'category' => 'SETTINGS'],
            ['name' => 'View Configurations', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'Edit Configurations', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'View Product Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Add Product Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Edit Product Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Delete Product Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Price Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Add Price Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Edit Price Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Delete Price Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Expense Categories', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:29:29', 'updated_at' => '2024-11-23 08:29:29', 'category' => 'SETTINGS'],
            ['name' => 'Add Expense Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Edit Expense Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Delete Expense Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Add Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Edit Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Delete Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Branches', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Add Branches', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Edit Branches', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Delete Branches', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Terms and Conditions', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Security', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:28', 'updated_at' => '2024-11-11 20:00:28', 'category' => 'SETTINGS'],
            ['name' => 'View Roles', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Add Roles', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Edit Roles', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Delete Roles', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Users', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Add Users', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Edit Users', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'Permit Users', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['name' => 'View Tools', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'View Database Backup', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'Create Database Backup', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'Download Database Backup', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'Delete Database Backup', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'View Export Stock', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'View Clear Database', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'View Reset Stock', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'View Upload Stock', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['name' => 'View Upload Price', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            
            // Accounting Sub Modules
            ['name' => 'View Expenses', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:24:55', 'updated_at' => '2024-11-23 08:24:55', 'category' => 'ACCOUNTING'],
            ['name' => 'Add Expenses', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'ACCOUNTING'],
            ['name' => 'Edit Expenses', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'ACCOUNTING'],
            ['name' => 'Delete Expenses', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'ACCOUNTING'],
            ['name' => 'View Invoices', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:25:20', 'updated_at' => '2024-11-23 08:25:20', 'category' => 'ACCOUNTING'],
            ['name' => 'Add Invoices', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:25:36', 'updated_at' => '2024-11-23 08:25:36', 'category' => 'ACCOUNTING'],
            ['name' => 'Edit Invoices', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'ACCOUNTING'],
            ['name' => 'View Payments', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:25:36', 'updated_at' => '2024-11-23 08:25:36', 'category' => 'ACCOUNTING'],
            ['name' => 'View Payment History', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:25:36', 'updated_at' => '2024-11-23 08:25:36', 'category' => 'ACCOUNTING'],
            // Purchasing Sub Modules
            ['name' => 'View Goods Receiving', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'View Invoice Receiving', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:14:30', 'updated_at' => '2024-11-15 19:14:30', 'category' => 'PURCHASING'],
            ['name' => 'View Order Receiving', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:11:38', 'updated_at' => '2024-11-15 19:11:38', 'category' => 'PURCHASING'],
            ['name' => 'Order Receiving', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:11:38', 'updated_at' => '2024-11-15 19:11:38', 'category' => 'PURCHASING'],
            ['name' => 'View Material Received', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:11:53', 'updated_at' => '2024-11-15 19:11:53', 'category' => 'PURCHASING'],
            ['name' => 'Edit Material Received', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:11:53', 'updated_at' => '2024-11-15 19:11:53', 'category' => 'PURCHASING'],
            ['name' => 'Delete Material Received', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:11:53', 'updated_at' => '2024-11-15 19:11:53', 'category' => 'PURCHASING'],
            ['name' => 'View Purchase Returns', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Purchase Return', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'View Purchase Returns Approvals', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Approve Purchase Returns', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'View Purchase Order', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Create Purchase Order', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Approve Purchase Order', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Print Purchase Order', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'View Suppliers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Add Suppliers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Edit Suppliers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['name' => 'Delete Suppliers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            // Reports Sub Modules
            ['name' => 'View Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'View Sales Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Sales Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Sales Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Sales Total Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Cash Sales Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Cash Sales Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Cash Sales Total Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Credit Sales Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Credit Sales Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Credit Sales Total Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Credit Payments Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Customer Payment Statement', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Price List Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Sales Returns Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Sales Comparison Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Discount Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'View Purchasing Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Material Received Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'List of Supplier', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Purchase Order Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Purchase Return Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'View Accounting Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Current Stock Value', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Gross Profit Detail', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Gross Profit Summary', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Expense Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Invoice Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Income Statement Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Cost of Expired Products', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'View Inventory Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Current Stock Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Current Stock Detailed Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Product Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Product Ledger Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Product Ledger Detailed Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Expired Products Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Products Expiry Date Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Out Of Stock Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Outgoing Stock Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Outgoing Stock Detailed Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Fast Moving Products Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Dead Stock Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Stock Adjustment Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Stock Issue Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Stock Issue Return Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Stock Transfer Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Stock Above Max. Level', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'Stock Below Min. Level', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['name' => 'View Expense Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            // ['name' => 'View Transport Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
