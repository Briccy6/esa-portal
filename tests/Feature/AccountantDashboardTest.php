<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountantDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $accountant;
    protected $admin;
    protected $dos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountant = Staff::factory()->create([
            'role' => 'Accountant',
            'email' => 'accountant@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->admin = Staff::factory()->create([
            'role' => 'DOS',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->dos = Staff::factory()->create([
            'role' => 'DOS',
            'email' => 'dos@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function accountant_can_access_dashboard()
    {
        $response = $this->actingAs($this->accountant, 'staff')
            ->get(route('staff.accountant.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function non_accountant_cannot_access_accountant_dashboard()
    {
        $response = $this->actingAs($this->dos, 'staff')
            ->get(route('staff.accountant.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_accountant_dashboard()
    {
        $response = $this->get(route('staff.accountant.dashboard'));

        $response->assertRedirect(route('staff.login'));
    }

    /** @test */
    public function accountant_can_access_expenses_index()
    {
        $response = $this->actingAs($this->accountant, 'staff')
            ->get(route('staff.accountant.expenses.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function non_accountant_cannot_access_expenses_index()
    {
        $response = $this->actingAs($this->dos, 'staff')
            ->get(route('staff.accountant.expenses.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function accountant_can_access_receipts_index()
    {
        $response = $this->actingAs($this->accountant, 'staff')
            ->get(route('staff.accountant.receipts.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function non_accountant_cannot_access_receipts_index()
    {
        $response = $this->actingAs($this->dos, 'staff')
            ->get(route('staff.accountant.receipts.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function accountant_can_access_chart_data_endpoint()
    {
        $response = $this->actingAs($this->accountant, 'staff')
            ->get(route('staff.accountant.chart-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'fees_vs_expenses_by_month',
            'fee_status',
            'collections_by_branch',
            'expenses_by_category',
            'daily_collections'
        ]);
    }

    /** @test */
    public function accountant_can_access_kpi_data_endpoint()
    {
        $response = $this->actingAs($this->accountant, 'staff')
            ->get(route('staff.accountant.kpi-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_fees_expected',
            'fees_collected',
            'outstanding_balance',
            'collection_rate',
            'total_expenses',
            'net_balance',
            'payments_today',
            'students_with_arrears'
        ]);
    }

    /** @test */
    public function admin_can_access_accountant_routes()
    {
        // Admin role should also have access
        $admin = Staff::factory()->create([
            'role' => 'Admin',
            'email' => 'admin_access@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin, 'staff')
            ->get(route('staff.accountant.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function accountant_dashboard_links_to_staff_accounting_reports()
    {
        $response = $this->actingAs($this->accountant, 'staff')
            ->get(route('staff.accountant.dashboard'));

        $response->assertOk()
            ->assertSee(route('staff.accounting.cashbook'), false)
            ->assertSee(route('staff.accounting.balance-sheet'), false);
    }

    /** @test */
    public function accountant_can_access_cashbook_report()
    {
        $response = $this->actingAs($this->accountant, 'staff')
            ->get(route('staff.accounting.cashbook'));

        $response->assertOk();
    }
}
