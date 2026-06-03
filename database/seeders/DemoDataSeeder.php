<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\License;
use App\Models\Vendor;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = \App\Models\User::where('role', 'super_admin')->first()->id ?? 1;

        // Categories
        $catOs = Category::firstOrCreate(['slug' => 'os'], ['name' => 'Operating System']);
        $catSoft = Category::firstOrCreate(['slug' => 'software'], ['name' => 'Software']);
        $catCloud = Category::firstOrCreate(['slug' => 'cloud'], ['name' => 'Cloud Service']);
        $catSecurity = Category::firstOrCreate(['slug' => 'security'], ['name' => 'Security']);
        $catDb = Category::firstOrCreate(['slug' => 'database'], ['name' => 'Database']);

        // Ghost Document setup
        Storage::disk('public')->makeDirectory('documents');
        $dummyPdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n4 0 obj\n<< /Length 53 >>\nstream\nBT /F1 24 Tf 100 700 Td (License Hub Demo Document) Tj ET\nendstream\nendobj\n5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000222 00000 n \n0000000326 00000 n \ntrailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n414\n%%EOF";
        $ghostPath = 'documents/demo-file.pdf';
        Storage::disk('public')->put($ghostPath, $dummyPdfContent);

        // 10 Vendors
        $vendorsData = [
            ['name' => 'Microsoft Indonesia', 'email' => 'billing@microsoft.co.id', 'phone' => '+628111111111'],
            ['name' => 'Adobe Inc', 'email' => 'sales@adobe.com', 'phone' => '+18001234567'],
            ['name' => 'Amazon Web Services', 'email' => 'aws-billing@amazon.com', 'phone' => '+18009998888'],
            ['name' => 'Cisco Systems', 'email' => 'sales@cisco.com', 'phone' => '+18005536387'],
            ['name' => 'Mikrotik', 'email' => 'sales@mikrotik.com', 'phone' => '+37167317700'],
            ['name' => 'Oracle Corporation', 'email' => 'sales@oracle.com', 'phone' => '+18006330738'],
            ['name' => 'VMware Inc', 'email' => 'sales@vmware.com', 'phone' => '+18774869273'],
            ['name' => 'Dell EMC', 'email' => 'support@dell.com', 'phone' => '+18004563355'],
            ['name' => 'Hewlett Packard Enterprise', 'email' => 'hpe-sales@hpe.com', 'phone' => '+18006333600'],
            ['name' => 'Fortinet', 'email' => 'support@fortinet.com', 'phone' => '+18005557777'],
        ];

        $vendors = [];
        foreach ($vendorsData as $v) {
            // Create vendor with Ghost PDFs for MSA and SLA files
            $vendor = Vendor::create([
                'name' => $v['name'],
                'email' => $v['email'],
                'phone' => $v['phone'],
                'msa_file' => $ghostPath,
                'sla_file' => $ghostPath,
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
            ]);
            $vendors[$vendor->name] = $vendor;
        }

        // 20 Licenses (10 Active, 4 Expiring, 3 Expired, 3 Perpetual)
        $licensesData = [
            // --- 3 Perpetual (CapEx) ---
            ['name' => 'Windows Server 2022 DC', 'vendor' => 'Microsoft Indonesia', 'cat' => $catOs->id, 'type' => 'perpetual', 'cost' => 120000000, 'cycle' => 'one_time', 'status' => 'active', 'start' => now()->subDays(100), 'expiry' => null],
            ['name' => 'Oracle Database Standard Edition 2', 'vendor' => 'Oracle Corporation', 'cat' => $catDb->id, 'type' => 'perpetual', 'cost' => 250000000, 'cycle' => 'one_time', 'status' => 'active', 'start' => now()->subDays(200), 'expiry' => null],
            ['name' => 'VMware vSphere Standard', 'vendor' => 'VMware Inc', 'cat' => $catSoft->id, 'type' => 'perpetual', 'cost' => 85000000, 'cycle' => 'one_time', 'status' => 'active', 'start' => now()->subDays(150), 'expiry' => null],

            // --- 10 Active (Subscription: Monthly, Quarterly, Yearly) ---
            ['name' => 'AWS EC2 & RDS Infrastructure', 'vendor' => 'Amazon Web Services', 'cat' => $catCloud->id, 'type' => 'subscription', 'cost' => 15000000, 'cycle' => 'monthly', 'status' => 'active', 'start' => now()->subDays(15), 'expiry' => now()->addDays(15)],
            ['name' => 'AWS S3 Storage', 'vendor' => 'Amazon Web Services', 'cat' => $catCloud->id, 'type' => 'subscription', 'cost' => 5000000, 'cycle' => 'monthly', 'status' => 'active', 'start' => now()->subDays(10), 'expiry' => now()->addDays(20)],
            ['name' => 'FortiGate Security Bundle', 'vendor' => 'Fortinet', 'cat' => $catSecurity->id, 'type' => 'subscription', 'cost' => 25000000, 'cycle' => 'quarterly', 'status' => 'active', 'start' => now()->subDays(60), 'expiry' => now()->addDays(30)],
            ['name' => 'Cisco Meraki Enterprise', 'vendor' => 'Cisco Systems', 'cat' => $catSecurity->id, 'type' => 'subscription', 'cost' => 45000000, 'cycle' => 'yearly', 'status' => 'active', 'start' => now()->subDays(100), 'expiry' => now()->addDays(265)],
            ['name' => 'Dell ProSupport Plus', 'vendor' => 'Dell EMC', 'cat' => $catSoft->id, 'type' => 'subscription', 'cost' => 35000000, 'cycle' => 'yearly', 'status' => 'active', 'start' => now()->subDays(200), 'expiry' => now()->addDays(165)],
            ['name' => 'HPE InfoSight', 'vendor' => 'Hewlett Packard Enterprise', 'cat' => $catSoft->id, 'type' => 'subscription', 'cost' => 20000000, 'cycle' => 'yearly', 'status' => 'active', 'start' => now()->subDays(50), 'expiry' => now()->addDays(315)],
            ['name' => 'Microsoft 365 Business Premium', 'vendor' => 'Microsoft Indonesia', 'cat' => $catSoft->id, 'type' => 'subscription', 'cost' => 8500000, 'cycle' => 'monthly', 'status' => 'active', 'start' => now()->subDays(5), 'expiry' => now()->addDays(25)],
            ['name' => 'Oracle Cloud Infrastructure', 'vendor' => 'Oracle Corporation', 'cat' => $catCloud->id, 'type' => 'subscription', 'cost' => 55000000, 'cycle' => 'quarterly', 'status' => 'active', 'start' => now()->subDays(10), 'expiry' => now()->addDays(80)],
            ['name' => 'Mikrotik Cloud Hosted Router', 'vendor' => 'Mikrotik', 'cat' => $catCloud->id, 'type' => 'subscription', 'cost' => 2000000, 'cycle' => 'monthly', 'status' => 'active', 'start' => now()->subDays(2), 'expiry' => now()->addDays(28)],
            ['name' => 'VMware Horizon Cloud', 'vendor' => 'VMware Inc', 'cat' => $catCloud->id, 'type' => 'subscription', 'cost' => 32000000, 'cycle' => 'quarterly', 'status' => 'active', 'start' => now()->subDays(45), 'expiry' => now()->addDays(45)],

            // --- 4 Expiring Soon (Warning, < 30 Days) ---
            ['name' => 'Adobe Creative Cloud for Enterprise', 'vendor' => 'Adobe Inc', 'cat' => $catSoft->id, 'type' => 'subscription', 'cost' => 125000000, 'cycle' => 'yearly', 'status' => 'expiring', 'start' => now()->subDays(351), 'expiry' => now()->addDays(14)],
            ['name' => 'Microsoft Azure Services', 'vendor' => 'Microsoft Indonesia', 'cat' => $catCloud->id, 'type' => 'subscription', 'cost' => 75000000, 'cycle' => 'yearly', 'status' => 'expiring', 'start' => now()->subDays(360), 'expiry' => now()->addDays(5)],
            ['name' => 'Cisco Webex Meetings', 'vendor' => 'Cisco Systems', 'cat' => $catSoft->id, 'type' => 'subscription', 'cost' => 18000000, 'cycle' => 'yearly', 'status' => 'expiring', 'start' => now()->subDays(340), 'expiry' => now()->addDays(25)],
            ['name' => 'FortiAnalyzer Subscription', 'vendor' => 'Fortinet', 'cat' => $catSecurity->id, 'type' => 'subscription', 'cost' => 12000000, 'cycle' => 'quarterly', 'status' => 'expiring', 'start' => now()->subDays(80), 'expiry' => now()->addDays(10)],

            // --- 3 Expired (Danger, < 0 Days) ---
            ['name' => 'Mikrotik RouterOS Level 6 Support', 'vendor' => 'Mikrotik', 'cat' => $catSoft->id, 'type' => 'subscription', 'cost' => 5000000, 'cycle' => 'yearly', 'status' => 'expired', 'start' => now()->subDays(370), 'expiry' => now()->subDays(5)],
            ['name' => 'Adobe Acrobat Pro Teams', 'vendor' => 'Adobe Inc', 'cat' => $catSoft->id, 'type' => 'subscription', 'cost' => 15000000, 'cycle' => 'yearly', 'status' => 'expired', 'start' => now()->subDays(380), 'expiry' => now()->subDays(15)],
            ['name' => 'Dell Endpoint Security', 'vendor' => 'Dell EMC', 'cat' => $catSecurity->id, 'type' => 'subscription', 'cost' => 22000000, 'cycle' => 'yearly', 'status' => 'expired', 'start' => now()->subDays(400), 'expiry' => now()->subDays(35)],
        ];

        foreach ($licensesData as $ld) {
            $lic = License::create([
                'name' => $ld['name'],
                'vendor_id' => $vendors[$ld['vendor']]->id,
                'category_id' => $ld['cat'],
                'type' => $ld['type'],
                'start_date' => $ld['start'],
                'expiry_date' => $ld['expiry'],
                'cost' => $ld['cost'],
                'billing_cycle' => $ld['cycle'],
                'status' => $ld['status'],
                'created_by' => $userId,
            ]);

            // Add dummy documents for License
            Document::create([
                'license_id' => $lic->id,
                'uploaded_by' => $userId,
                'document_type' => 'contract',
                'file_path' => $ghostPath,
                'file_name' => 'Kontrak_' . str_replace(' ', '_', $lic->name) . '.pdf',
                'file_size' => 414
            ]);
            
            // Create Invoice and its Document for Expiring & Expired licenses
            if (in_array($ld['status'], ['expiring', 'expired'])) {
                $invoice = \App\Models\Invoice::create([
                    'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
                    'license_id'     => $lic->id,
                    'vendor_id'      => $lic->vendor_id,
                    'amount'         => $lic->cost,
                    'tax_amount'     => 0,
                    'total_amount'   => $lic->cost,
                    'invoice_date'   => now()->subDays(2),
                    'due_date'       => $lic->expiry_date,
                    'status'         => 'unpaid',
                    'file_path'      => $ghostPath,
                    'notes'          => 'Tagihan perpanjangan otomatis.',
                    'created_by'     => $userId,
                ]);
                
                Document::create([
                    'license_id'    => $lic->id,
                    'uploaded_by'   => $userId,
                    'document_type' => 'invoice',
                    'file_path'     => $ghostPath,
                    'file_name'     => 'Tagihan_' . $invoice->invoice_number . '.pdf',
                    'file_size'     => 414,
                    'description'   => 'Auto-attached Invoice: ' . $invoice->invoice_number,
                ]);
            } else {
                // Just a normal invoice document for active licenses
                Document::create([
                    'license_id' => $lic->id,
                    'uploaded_by' => $userId,
                    'document_type' => 'invoice',
                    'file_path' => $ghostPath,
                    'file_name' => 'Invoice_Awal_' . str_replace(' ', '_', $lic->name) . '.pdf',
                    'file_size' => 414
                ]);
            }
        }
    }
}
