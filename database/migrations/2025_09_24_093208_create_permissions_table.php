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
            ['id' => 1, 'name' => 'View Dashboard', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['id' => 2, 'name' => 'View Cash Sales', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 3, 'name' => 'View Credit Sales', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 4, 'name' => 'View Credit Tracking', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 5, 'name' => 'View Credit Payment', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 6, 'name' => 'Add Credit Payment', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 7, 'name' => 'View Sales Orders', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 8, 'name' => 'View Order List', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:12:08', 'updated_at' => '2024-11-15 19:12:08', 'category' => 'SALES'],
            ['id' => 9, 'name' => 'Print Sales Orders', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:12:08', 'updated_at' => '2024-11-15 19:12:08', 'category' => 'SALES'],
            ['id' => 10, 'name' => 'Edit Sales Orders', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 11, 'name' => 'Convert Sales Orders', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 12, 'name' => 'View Sales History', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 13, 'name' => 'Print Sales History', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 14, 'name' => 'View Sales Return', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 15, 'name' => 'View Sales Return Approval', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 16, 'name' => 'Approve Sales Return', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 17, 'name' => 'View Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 18, 'name' => 'Add Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 19, 'name' => 'Edit Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 20, 'name' => 'Delete Customers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SALES'],
            ['id' => 21, 'name' => 'View Current Stock', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 22, 'name' => 'View Current Stock Value', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 23, 'name' => 'View Products List', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 24, 'name' => 'Add Products', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 25, 'name' => 'Edit Products', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 26, 'name' => 'Delete Products', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 27, 'name' => 'View Price List', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 28, 'name' => 'Edit Price List', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 29, 'name' => 'Products Import', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 30, 'name' => 'Download Import Templates', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 31, 'name' => 'View Stock Transfer', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 32, 'name' => 'Create Stock Transfer', 'guard_name' => 'web', 'created_at' => '2025-07-06 19:12:58', 'updated_at' => '2025-07-06 19:12:58', 'category' => 'INVENTORY'],
            ['id' => 33, 'name' => 'Edit Stock Transfer', 'guard_name' => 'web', 'created_at' => '2025-07-06 19:12:58', 'updated_at' => '2025-07-06 19:12:58', 'category' => 'INVENTORY'],
            ['id' => 34, 'name' => 'Approve Stock Transfer', 'guard_name' => 'web', 'created_at' => '2025-07-06 19:12:58', 'updated_at' => '2025-07-06 19:12:58', 'category' => 'INVENTORY'],
            ['id' => 35, 'name' => 'Acknowledge Stock Transfer', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 36, 'name' => 'View Stock Adjustment', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 37, 'name' => 'Create Stock Adjustment', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 38, 'name' => 'View Stock Count', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 39, 'name' => 'View Outgoing Stock', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 40, 'name' => 'Download Inv. Count Sheet', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 41, 'name' => 'View Masters', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 42, 'name' => 'View Products Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 43, 'name' => 'Add Products Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 44, 'name' => 'View Requisition', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 45, 'name' => 'View Price Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 46, 'name' => 'Manage Price Categories', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 47, 'name' => 'View Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 48, 'name' => 'Create Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 49, 'name' => 'View Requisition Issue', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 50, 'name' => 'View Suppliers', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['id' => 51, 'name' => 'View Stores', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 52, 'name' => 'Manage Stores', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 53, 'name' => 'View Inventory', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['id' => 54, 'name' => 'Manage Settings', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 55, 'name' => 'View Accounting', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'MODULES'],
            ['id' => 56, 'name' => 'Manage Suppliers', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'PURCHASING'],
            ['id' => 57, 'name' => 'Manage All Branches', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'DASHBOARD'],
            ['id' => 58, 'name' => 'View Requisitions Details', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 59, 'name' => 'Create Requisitions', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 60, 'name' => 'Delete Requisitions', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 61, 'name' => 'Approve Requisitions', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 62, 'name' => 'Print Requisitions', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 63, 'name' => 'View Requisitions Issue', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 64, 'name' => 'Edit Products Categories', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'SETTINGS'],
            ['id' => 65, 'name' => 'View Alerts', 'guard_name' => 'web', 'created_at' => '2024-11-11 19:35:44', 'updated_at' => '2024-11-11 19:35:44', 'category' => 'SETTINGS'],
            ['id' => 66, 'name' => 'View Security', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:28', 'updated_at' => '2024-11-11 20:00:28', 'category' => 'SETTINGS'],
            ['id' => 67, 'name' => 'View Tools', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:00:48', 'updated_at' => '2024-11-11 20:00:48', 'category' => 'SETTINGS'],
            ['id' => 68, 'name' => 'View General', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:05:31', 'updated_at' => '2024-11-11 20:05:31', 'category' => 'SETTINGS'],
            ['id' => 69, 'name' => 'View Reports', 'guard_name' => 'web', 'created_at' => '2024-11-11 20:17:51', 'updated_at' => '2024-11-11 20:17:51', 'category' => 'MODULES'],
            ['id' => 70, 'name' => 'View Sales Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:49:39', 'updated_at' => '2024-11-15 16:49:39', 'category' => 'DASHBOARD'],
            ['id' => 71, 'name' => 'View Purchasing Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:49:56', 'updated_at' => '2024-11-15 16:49:56', 'category' => 'DASHBOARD'],
            ['id' => 72, 'name' => 'View Inventory Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:50:12', 'updated_at' => '2024-11-15 16:50:12', 'category' => 'DASHBOARD'],
            ['id' => 73, 'name' => 'View Accounting Summary', 'guard_name' => 'web', 'created_at' => '2024-11-15 16:50:28', 'updated_at' => '2024-11-15 16:50:28', 'category' => 'DASHBOARD'],
            ['id' => 74, 'name' => 'View Order Receiving', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:11:38', 'updated_at' => '2024-11-15 19:11:38', 'category' => 'PURCHASING'],
            ['id' => 75, 'name' => 'View Material Received', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:11:53', 'updated_at' => '2024-11-15 19:11:53', 'category' => 'PURCHASING'],
            ['id' => 76, 'name' => 'View Requisition List', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:12:24', 'updated_at' => '2024-11-15 19:12:24', 'category' => 'INVENTORY'],
            ['id' => 77, 'name' => 'View Invoice Receiving', 'guard_name' => 'web', 'created_at' => '2024-11-15 19:14:30', 'updated_at' => '2024-11-15 19:14:30', 'category' => 'PURCHASING'],
            ['id' => 78, 'name' => 'View Expenses', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:24:55', 'updated_at' => '2024-11-23 08:24:55', 'category' => 'ACCOUNTING'],
            ['id' => 79, 'name' => 'Manage Expenses', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'ACCOUNTING'],
            ['id' => 80, 'name' => 'View Invoices', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:25:20', 'updated_at' => '2024-11-23 08:25:20', 'category' => 'ACCOUNTING'],
            ['id' => 81, 'name' => 'Manage Invoices', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:25:36', 'updated_at' => '2024-11-23 08:25:36', 'category' => 'ACCOUNTING'],
            ['id' => 82, 'name' => 'View Assets', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:27:22', 'updated_at' => '2024-11-23 08:27:22', 'category' => 'ACCOUNTING'],
            ['id' => 83, 'name' => 'Manage Assets', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:27:38', 'updated_at' => '2024-11-23 08:27:38', 'category' => 'ACCOUNTING'],
            ['id' => 84, 'name' => 'View Cash Flow', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:27:54', 'updated_at' => '2024-11-23 08:27:54', 'category' => 'ACCOUNTING'],
            ['id' => 85, 'name' => 'Manage Cash Flow', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:29:21', 'updated_at' => '2024-11-23 08:29:21', 'category' => 'ACCOUNTING'],
            ['id' => 86, 'name' => 'View Expense Categories', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:29:29', 'updated_at' => '2024-11-23 08:29:29', 'category' => 'SETTINGS'],
            ['id' => 87, 'name' => 'Manage Expense Categories', 'guard_name' => 'web', 'created_at' => '2024-11-23 08:29:43', 'updated_at' => '2024-11-23 08:29:43', 'category' => 'ACCOUNTING'],
            ['id' => 88, 'name' => 'Manage All Branches', 'guard_name' => 'web', 'created_at' => '2024-12-02 16:15:52', 'updated_at' => '2024-12-02 16:15:52', 'category' => 'SETTINGS'],
            ['id' => 89, 'name' => 'View Settings', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'MODULES'],
            ['id' => 90, 'name' => 'Delete Products Categories', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'SETTINGS'],
            ['id' => 91, 'name' => 'Edit Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 92, 'name' => 'Delete Adjustment Reasons', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 93, 'name' => 'View Product Ledger', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 94, 'name' => 'View Stock Issue', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 95, 'name' => 'Issue Return', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 96, 'name' => 'Manage Stock Issue', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'INVENTORY'],
            ['id' => 97, 'name' => 'View Purchasing', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['id' => 98, 'name' => 'View Goods Receiving', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['id' => 99, 'name' => 'View Purchase Order', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'PURCHASING'],
            ['id' => 100, 'name' => 'View Requisition', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'INVENTORY'],
            ['id' => 101, 'name' => 'View Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 102, 'name' => 'View Sales Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 103, 'name' => 'View Inventory Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 104, 'name' => 'View Expense Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 105, 'name' => 'View Purchase Reports', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 106, 'name' => 'View Accounting Reports', 'guard_name' => 'web', 'created_at' => null, 'updated_at' => null, 'category' => 'REPORTS'],
            ['id' => 107, 'name' => 'View Users', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 108, 'name' => 'Manage Users', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 109, 'name' => 'View Roles', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 110, 'name' => 'Manage Roles', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'SETTINGS'],
            ['id' => 111, 'name' => 'View Sales', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'MODULES'],
            ['id' => 112, 'name' => 'Sales Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 113, 'name' => 'Sales Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 114, 'name' => 'Sales Total Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 115, 'name' => 'Cash Sales Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 116, 'name' => 'Cash Sales Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 117, 'name' => 'Cash Sales Total Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 118, 'name' => 'Credit Sales Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 119, 'name' => 'Credit Sales Summary Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 120, 'name' => 'Credit Sales Total Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 121, 'name' => 'Credit Payments Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 122, 'name' => 'Customer Payment Statement', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 123, 'name' => 'Price List Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 124, 'name' => 'Sales Return Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 125, 'name' => 'Sales Comparison Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 126, 'name' => 'Current Stock Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 127, 'name' => 'Product Details Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 128, 'name' => 'Product Ledger Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 129, 'name' => 'Expired Products Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 130, 'name' => 'Out Of Stock Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 131, 'name' => 'Outgoing Tracking Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 132, 'name' => 'Stock Adjustment Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 133, 'name' => 'Stock Issue Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 134, 'name' => 'Stock Transfer Report', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 135, 'name' => 'Stock Above Max. Level', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
            ['id' => 136, 'name' => 'Stock Below Min. Level', 'guard_name' => 'web', 'created_at' => '2019-10-17 20:25:55', 'updated_at' => '2019-10-17 20:25:55', 'category' => 'REPORTS'],
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
