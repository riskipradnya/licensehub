<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(Request $request): View
    {
        $query = Vendor::active()->ordered()->withCount('licenses');

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $vendors = $query->get();

        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create(): View
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'address'        => ['nullable', 'string', 'max:1000'],
            'website'        => ['nullable', 'url', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'sla_response'   => ['nullable', 'in:24h,48h,72h'],
            'sla_hours'      => ['nullable', 'in:24/7,business'],
            'bank_name'           => ['nullable', Rule::in(array_keys(XenditController::BANK_CODES))],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'logo'                => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'msa_file'            => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'sla_file'            => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'name.required' => 'Nama vendor wajib diisi.',
            'email.required' => 'Email support wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'website.url'    => 'Format URL website tidak valid.',
            'logo.image'     => 'Logo harus berupa gambar.',
            'logo.max'       => 'Ukuran logo maksimal 2MB.',
            'msa_file.mimes' => 'MSA File harus berupa PDF.',
            'msa_file.max'   => 'Ukuran MSA File maksimal 10MB.',
            'sla_file.mimes' => 'SLA File harus berupa PDF.',
            'sla_file.max'   => 'Ukuran SLA File maksimal 10MB.',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('vendors/logos', 'public');
        }
        if ($request->hasFile('msa_file')) {
            $validated['msa_file'] = $request->file('msa_file')->store('vendors/docs', 'public');
        }
        if ($request->hasFile('sla_file')) {
            $validated['sla_file'] = $request->file('sla_file')->store('vendors/docs', 'public');
        }

        $vendor = Vendor::create($validated);



        return redirect()
            ->route('vendors.show', $vendor)
            ->with('success', "Vendor \"{$vendor->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor): View
    {
        $vendor->loadCount('licenses');
        $vendor->load(['licenses' => function ($query) {
            $query->with('category')
                  ->orderByRaw("FIELD(status, 'expiring', 'active', 'expired', 'inactive')")
                  ->limit(20);
        }]);

        // Stats for sidebar
        $stats = [
            'total'    => $vendor->licenses_count,
            'active'   => $vendor->licenses()->where('status', 'active')->count(),
            'expiring' => $vendor->licenses()->where('status', 'expiring')->count(),
            'expired'  => $vendor->licenses()->where('status', 'expired')->count(),
        ];

        return view('vendors.show', compact('vendor', 'stats'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor): View
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'address'        => ['nullable', 'string', 'max:1000'],
            'website'        => ['nullable', 'url', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'sla_response'   => ['nullable', 'in:24h,48h,72h'],
            'sla_hours'      => ['nullable', 'in:24/7,business'],
            'bank_name'           => ['nullable', Rule::in(array_keys(XenditController::BANK_CODES))],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'logo'                => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'msa_file'            => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'sla_file'            => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'name.required' => 'Nama vendor wajib diisi.',
            'email.required' => 'Email support wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'website.url'    => 'Format URL website tidak valid.',
            'logo.image'     => 'Logo harus berupa gambar.',
            'logo.max'       => 'Ukuran logo maksimal 2MB.',
            'msa_file.mimes' => 'MSA File harus berupa PDF.',
            'msa_file.max'   => 'Ukuran MSA File maksimal 10MB.',
            'sla_file.mimes' => 'SLA File harus berupa PDF.',
            'sla_file.max'   => 'Ukuran SLA File maksimal 10MB.',
        ]);

        $oldValues = $vendor->only(array_keys($validated));

        if ($request->hasFile('logo')) {
            if ($vendor->logo) Storage::disk('public')->delete($vendor->logo);
            $validated['logo'] = $request->file('logo')->store('vendors/logos', 'public');
        }
        if ($request->hasFile('msa_file')) {
            if ($vendor->msa_file) Storage::disk('public')->delete($vendor->msa_file);
            $validated['msa_file'] = $request->file('msa_file')->store('vendors/docs', 'public');
        }
        if ($request->hasFile('sla_file')) {
            if ($vendor->sla_file) Storage::disk('public')->delete($vendor->sla_file);
            $validated['sla_file'] = $request->file('sla_file')->store('vendors/docs', 'public');
        }

        $vendor->update($validated);



        return redirect()
            ->route('vendors.show', $vendor)
            ->with('success', "Vendor \"{$vendor->name}\" berhasil diperbarui.");
    }

    /**
     * Soft-delete (deactivate) the specified vendor.
     */
    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendorName = $vendor->name;

        // Soft-delete via flag and SoftDeletes trait
        $vendor->update(['is_active' => false]);
        $vendor->delete();



        return redirect()
            ->route('vendors.index')
            ->with('success', "Vendor \"{$vendorName}\" berhasil dihapus.");
    }
}
