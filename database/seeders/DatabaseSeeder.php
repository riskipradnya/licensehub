<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\CostProjection;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\License;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ═══════════════════════════════════════════════════════
        //  USERS
        // ═══════════════════════════════════════════════════════

        $admin = User::create([
            'name'       => 'Super Admin',
            'email'      => 'admin@licensehub.com',
            'password'   => Hash::make('password'),
            'role'       => 'super_admin',
            'department' => 'IT',
            'is_active'  => true,
        ]);

        $itStaff = User::create([
            'name'       => 'Budi Santoso',
            'email'      => 'budi@licensehub.com',
            'password'   => Hash::make('password'),
            'role'       => 'it_staff',
            'department' => 'IT',
            'is_active'  => true,
        ]);

        $financeManager = User::create([
            'name'       => 'Siti Rahayu',
            'email'      => 'siti@licensehub.com',
            'password'   => Hash::make('password'),
            'role'       => 'finance_manager',
            'department' => 'Finance',
            'is_active'  => true,
        ]);

        $financeStaff = User::create([
            'name'       => 'Andi Wijaya',
            'email'      => 'andi@licensehub.com',
            'password'   => Hash::make('password'),
            'role'       => 'finance_staff',
            'department' => 'Finance',
            'is_active'  => true,
        ]);

        // ═══════════════════════════════════════════════════════
        //  CATEGORIES
        // ═══════════════════════════════════════════════════════

        $categories = [];
        $categoryData = [
            ['name' => 'Operating System', 'slug' => 'os',        'color' => '#22c55e', 'description' => 'Lisensi sistem operasi'],
            ['name' => 'Software',         'slug' => 'software',  'color' => '#3b82f6', 'description' => 'Lisensi perangkat lunak'],
            ['name' => 'Antivirus',        'slug' => 'antivirus', 'color' => '#ef4444', 'description' => 'Lisensi keamanan endpoint'],
            ['name' => 'Security',         'slug' => 'security',  'color' => '#f59e0b', 'description' => 'Lisensi keamanan jaringan & sistem'],
            ['name' => 'Database',         'slug' => 'database',  'color' => '#8b5cf6', 'description' => 'Lisensi database management system'],
            ['name' => 'Cloud Service',    'slug' => 'cloud',     'color' => '#06b6d4', 'description' => 'Lisensi layanan cloud & SaaS'],
        ];

        foreach ($categoryData as $data) {
            $categories[$data['slug']] = Category::create($data);
        }

        // ═══════════════════════════════════════════════════════
        //  VENDORS
        // ═══════════════════════════════════════════════════════

        $vendors = [];
        $vendorData = [
            [
                'name'           => 'Microsoft',
                'contact_person' => 'Microsoft Indonesia',
                'email'          => 'support@microsoft.com',
                'phone'          => '+62-21-5555-0001',
                'website'        => 'https://www.microsoft.com',
                'address'        => 'Jakarta, Indonesia',
                'sla_response'   => '24h',
                'sla_hours'      => '24/7',
            ],
            [
                'name'           => 'Oracle Corporation',
                'contact_person' => 'Oracle Sales Indonesia',
                'email'          => 'sales@oracle.com',
                'phone'          => '+62-21-5555-0002',
                'website'        => 'https://www.oracle.com',
                'address'        => 'Jakarta, Indonesia',
                'sla_response'   => '48h',
                'sla_hours'      => 'business',
            ],
            [
                'name'           => 'Adobe Inc.',
                'contact_person' => 'Adobe Enterprise',
                'email'          => 'enterprise@adobe.com',
                'phone'          => '+62-21-5555-0003',
                'website'        => 'https://www.adobe.com',
                'address'        => 'Jakarta, Indonesia',
                'sla_response'   => '24h',
                'sla_hours'      => 'business',
            ],
            [
                'name'           => 'Kaspersky Lab',
                'contact_person' => 'Kaspersky Partner ID',
                'email'          => 'partner@kaspersky.com',
                'phone'          => '+62-21-5555-0004',
                'website'        => 'https://www.kaspersky.com',
                'address'        => 'Jakarta, Indonesia',
                'sla_response'   => '72h',
                'sla_hours'      => 'business',
            ],
            [
                'name'           => 'VMware (Broadcom)',
                'contact_person' => 'VMware Sales',
                'email'          => 'sales@vmware.com',
                'phone'          => '+62-21-5555-0005',
                'website'        => 'https://www.vmware.com',
                'address'        => 'Jakarta, Indonesia',
                'sla_response'   => '24h',
                'sla_hours'      => '24/7',
            ],
        ];

        foreach ($vendorData as $data) {
            $vendors[$data['name']] = Vendor::create($data);
        }

        // ═══════════════════════════════════════════════════════
        //  LICENSES
        // ═══════════════════════════════════════════════════════

        $licenses = [];

        // 1. Active licenses
        $licenses[] = License::create([
            'name'          => 'Microsoft 365 Business Premium',
            'vendor_id'     => $vendors['Microsoft']->id,
            'category_id'   => $categories['software']->id,
            'type'          => 'subscription',
            'serial_key'    => 'MS365-XXXX-YYYY-ZZZZ',
            'start_date'    => '2025-07-01',
            'expiry_date'   => '2026-07-01',
            'seats'         => 150,
            'cost'          => 45000000,
            'billing_cycle' => 'yearly',
            'status'        => 'active',
            'notes'         => 'Lisensi untuk seluruh karyawan',
            'created_by'    => $itStaff->id,
        ]);

        $licenses[] = License::create([
            'name'          => 'Adobe Creative Cloud Enterprise',
            'vendor_id'     => $vendors['Adobe Inc.']->id,
            'category_id'   => $categories['software']->id,
            'type'          => 'subscription',
            'serial_key'    => 'ACC-XXXX-YYYY-ZZZZ',
            'start_date'    => '2025-09-01',
            'expiry_date'   => '2026-09-01',
            'seats'         => 25,
            'cost'          => 18000000,
            'billing_cycle' => 'yearly',
            'status'        => 'active',
            'notes'         => 'Untuk tim desain dan marketing',
            'created_by'    => $itStaff->id,
        ]);

        $licenses[] = License::create([
            'name'          => 'VMware vSphere Enterprise Plus',
            'vendor_id'     => $vendors['VMware (Broadcom)']->id,
            'category_id'   => $categories['cloud']->id,
            'type'          => 'subscription',
            'serial_key'    => 'VMW-XXXX-YYYY-ZZZZ',
            'start_date'    => '2025-06-01',
            'expiry_date'   => '2026-06-01',
            'seats'         => 10,
            'cost'          => 35000000,
            'billing_cycle' => 'yearly',
            'status'        => 'active',
            'notes'         => 'Virtualisasi server data center',
            'created_by'    => $admin->id,
        ]);

        // 2. Expiring soon licenses
        $licenses[] = License::create([
            'name'          => 'Kaspersky Endpoint Security',
            'vendor_id'     => $vendors['Kaspersky Lab']->id,
            'category_id'   => $categories['security']->id,
            'type'          => 'subscription',
            'serial_key'    => 'KES-XXXX-YYYY-ZZZZ',
            'start_date'    => '2025-05-20',
            'expiry_date'   => '2026-05-20',
            'seats'         => 200,
            'cost'          => 12000000,
            'billing_cycle' => 'yearly',
            'status'        => 'expiring',
            'notes'         => 'Endpoint protection untuk seluruh PC',
            'created_by'    => $itStaff->id,
        ]);

        $licenses[] = License::create([
            'name'          => 'Oracle Database Enterprise Edition',
            'vendor_id'     => $vendors['Oracle Corporation']->id,
            'category_id'   => $categories['database']->id,
            'type'          => 'subscription',
            'serial_key'    => 'ORA-XXXX-YYYY-ZZZZ',
            'start_date'    => '2025-06-01',
            'expiry_date'   => '2026-06-01',
            'seats'         => 2,
            'cost'          => 85000000,
            'billing_cycle' => 'yearly',
            'status'        => 'expiring',
            'notes'         => 'Database production untuk ERP',
            'created_by'    => $admin->id,
        ]);

        $licenses[] = License::create([
            'name'          => 'Windows Server 2022 Datacenter',
            'vendor_id'     => $vendors['Microsoft']->id,
            'category_id'   => $categories['os']->id,
            'type'          => 'perpetual',
            'serial_key'    => 'WS22-XXXX-YYYY-ZZZZ',
            'start_date'    => '2023-01-15',
            'expiry_date'   => null,
            'seats'         => 5,
            'cost'          => 65000000,
            'billing_cycle' => 'one_time',
            'status'        => 'active',
            'notes'         => 'Lisensi perpetual untuk server fisik',
            'created_by'    => $admin->id,
        ]);

        // 3. Expired license
        $licenses[] = License::create([
            'name'          => 'ESET NOD32 Antivirus',
            'vendor_id'     => $vendors['Kaspersky Lab']->id,
            'category_id'   => $categories['antivirus']->id,
            'type'          => 'subscription',
            'serial_key'    => 'ESET-XXXX-YYYY-ZZZZ',
            'start_date'    => '2024-04-01',
            'expiry_date'   => '2025-04-01',
            'seats'         => 50,
            'cost'          => 5000000,
            'billing_cycle' => 'yearly',
            'status'        => 'expired',
            'notes'         => 'Sudah diganti ke Kaspersky',
            'created_by'    => $itStaff->id,
        ]);

        // 4. More active licenses
        $licenses[] = License::create([
            'name'          => 'Microsoft SQL Server 2022',
            'vendor_id'     => $vendors['Microsoft']->id,
            'category_id'   => $categories['database']->id,
            'type'          => 'perpetual',
            'serial_key'    => 'SQL22-XXXX-YYYY-ZZZZ',
            'start_date'    => '2024-03-01',
            'expiry_date'   => null,
            'seats'         => 4,
            'cost'          => 42000000,
            'billing_cycle' => 'one_time',
            'status'        => 'active',
            'notes'         => 'Database untuk aplikasi internal',
            'created_by'    => $admin->id,
        ]);

        $licenses[] = License::create([
            'name'          => 'Microsoft Azure Cloud',
            'vendor_id'     => $vendors['Microsoft']->id,
            'category_id'   => $categories['cloud']->id,
            'type'          => 'subscription',
            'serial_key'    => null,
            'start_date'    => '2025-01-01',
            'expiry_date'   => '2026-01-01',
            'seats'         => null,
            'cost'          => 15000000,
            'billing_cycle' => 'monthly',
            'status'        => 'active',
            'notes'         => 'Cloud infrastructure',
            'created_by'    => $itStaff->id,
        ]);

        $licenses[] = License::create([
            'name'          => 'Windows 11 Pro',
            'vendor_id'     => $vendors['Microsoft']->id,
            'category_id'   => $categories['os']->id,
            'type'          => 'perpetual',
            'serial_key'    => 'W11P-XXXX-YYYY-ZZZZ',
            'start_date'    => '2024-06-01',
            'expiry_date'   => null,
            'seats'         => 100,
            'cost'          => 30000000,
            'billing_cycle' => 'one_time',
            'status'        => 'active',
            'notes'         => 'Lisensi volume untuk workstation',
            'created_by'    => $itStaff->id,
        ]);

        // ═══════════════════════════════════════════════════════
        //  PAYMENTS
        // ═══════════════════════════════════════════════════════

        $payment1 = Payment::create([
            'license_id'       => $licenses[0]->id,
            'amount'           => 45000000,
            'payment_date'     => '2025-06-25',
            'payment_method'   => 'transfer',
            'reference_number' => 'TRF-2025-0001',
            'status'           => 'paid',
            'approved_by'      => $financeManager->id,
            'approved_at'      => '2025-06-24',
            'notes'            => 'Pembayaran tahunan MS365',
            'created_by'       => $financeStaff->id,
        ]);

        $payment2 = Payment::create([
            'license_id'       => $licenses[1]->id,
            'amount'           => 18000000,
            'payment_date'     => '2025-08-28',
            'payment_method'   => 'transfer',
            'reference_number' => 'TRF-2025-0002',
            'status'           => 'paid',
            'approved_by'      => $financeManager->id,
            'approved_at'      => '2025-08-27',
            'notes'            => 'Renewal Adobe CC',
            'created_by'       => $financeStaff->id,
        ]);

        $payment3 = Payment::create([
            'license_id'       => $licenses[3]->id,
            'amount'           => 12000000,
            'payment_date'     => now()->format('Y-m-d'),
            'payment_method'   => null,
            'reference_number' => null,
            'status'           => 'pending',
            'notes'            => 'Renewal Kaspersky — menunggu approval',
            'created_by'       => $financeStaff->id,
        ]);

        $payment4 = Payment::create([
            'license_id'       => $licenses[4]->id,
            'amount'           => 85000000,
            'payment_date'     => now()->format('Y-m-d'),
            'payment_method'   => 'transfer',
            'reference_number' => 'TRF-2026-0003',
            'status'           => 'approved',
            'approved_by'      => $financeManager->id,
            'approved_at'      => now(),
            'notes'            => 'Renewal Oracle DB — sudah diapprove, menunggu pembayaran',
            'created_by'       => $financeStaff->id,
        ]);

        // ═══════════════════════════════════════════════════════
        //  INVOICES
        // ═══════════════════════════════════════════════════════

        Invoice::create([
            'payment_id'     => $payment1->id,
            'invoice_number' => 'INV-202506-0001',
            'vendor_id'      => $vendors['Microsoft']->id,
            'license_id'     => $licenses[0]->id,
            'amount'         => 45000000,
            'tax_amount'     => 4950000,
            'total_amount'   => 49950000,
            'invoice_date'   => '2025-06-20',
            'due_date'       => '2025-07-01',
            'status'         => 'paid',
            'notes'          => 'Invoice Microsoft 365 Business Premium',
            'created_by'     => $financeStaff->id,
        ]);

        Invoice::create([
            'payment_id'     => $payment2->id,
            'invoice_number' => 'INV-202508-0001',
            'vendor_id'      => $vendors['Adobe Inc.']->id,
            'license_id'     => $licenses[1]->id,
            'amount'         => 18000000,
            'tax_amount'     => 1980000,
            'total_amount'   => 19980000,
            'invoice_date'   => '2025-08-25',
            'due_date'       => '2025-09-01',
            'status'         => 'paid',
            'notes'          => 'Invoice Adobe Creative Cloud Enterprise',
            'created_by'     => $financeStaff->id,
        ]);

        Invoice::create([
            'payment_id'     => null,
            'invoice_number' => 'INV-202605-0001',
            'vendor_id'      => $vendors['Kaspersky Lab']->id,
            'license_id'     => $licenses[3]->id,
            'amount'         => 12000000,
            'tax_amount'     => 1320000,
            'total_amount'   => 13320000,
            'invoice_date'   => now()->format('Y-m-d'),
            'due_date'       => now()->addDays(14)->format('Y-m-d'),
            'status'         => 'sent',
            'notes'          => 'Invoice renewal Kaspersky',
            'created_by'     => $financeStaff->id,
        ]);

        // ═══════════════════════════════════════════════════════
        //  COST PROJECTIONS
        // ═══════════════════════════════════════════════════════

        $months = ['2026-01', '2026-02', '2026-03', '2026-04', '2026-05', '2026-06'];
        $projectedCosts = [18500000, 22000000, 19800000, 24500000, 21300000, 24500000];
        $actualCosts    = [17800000, 23100000, 19500000, 25200000, null, null];

        foreach ($months as $i => $month) {
            CostProjection::create([
                'license_id'     => $licenses[0]->id,
                'projected_date' => $month . '-01',
                'projected_cost' => $projectedCosts[$i],
                'actual_cost'    => $actualCosts[$i],
                'notes'          => 'Proyeksi biaya bulanan',
                'created_by'     => $financeManager->id,
            ]);
        }

        // ═══════════════════════════════════════════════════════
        //  AUDIT LOGS (sample entries)
        // ═══════════════════════════════════════════════════════

        AuditLog::create([
            'user_id'    => $admin->id,
            'action'     => 'login',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder',
            'created_at' => now()->subDays(5),
        ]);

        AuditLog::create([
            'user_id'    => $itStaff->id,
            'action'     => 'created',
            'model_type' => 'License',
            'model_id'   => $licenses[0]->id,
            'new_values'  => ['name' => $licenses[0]->name, 'cost' => $licenses[0]->cost],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder',
            'created_at' => now()->subDays(4),
        ]);

        AuditLog::create([
            'user_id'    => $financeManager->id,
            'action'     => 'approved',
            'model_type' => 'Payment',
            'model_id'   => $payment1->id,
            'new_values'  => ['status' => 'approved'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder',
            'created_at' => now()->subDays(3),
        ]);

        // ═══════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║       🎉 LicenseHub Seeded!             ║');
        $this->command->info('╠══════════════════════════════════════════╣');
        $this->command->info('║  Login Accounts:                        ║');
        $this->command->info('║  ─────────────────────────────────────   ║');
        $this->command->info('║  Super Admin:   admin@licensehub.com    ║');
        $this->command->info('║  IT Staff:      budi@licensehub.com     ║');
        $this->command->info('║  Finance Mgr:   siti@licensehub.com     ║');
        $this->command->info('║  Finance Staff: andi@licensehub.com     ║');
        $this->command->info('║  Password:      password                ║');
        $this->command->info('╠══════════════════════════════════════════╣');
        $this->command->info('║  Data: 4 users, 6 categories,           ║');
        $this->command->info('║        5 vendors, 10 licenses,          ║');
        $this->command->info('║        4 payments, 3 invoices            ║');
        $this->command->info('╚══════════════════════════════════════════╝');
        $this->command->info('');
    }
}
