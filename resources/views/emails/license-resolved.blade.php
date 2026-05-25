<x-mail::message>
# Pembaharuan Lisensi Berhasil ✅

Halo Tim IT & Finance,

Kabar baik! Pembayaran untuk lisensi **{{ $license->name }}** telah berhasil diproses oleh sistem. Operasional infrastruktur dipastikan aman tanpa hambatan.

<x-mail::panel>
**Rincian Lisensi:**
* **Nama:** {{ $license->name }}
* **Vendor:** {{ $license->vendor->name ?? '-' }}
* **Status Baru:** <span style="color: green; font-weight: bold;">✅ Active (Terpelihara)</span>
* **Masa Berlaku Diperpanjang Hingga:** {{ $license->expiry_date ? $license->expiry_date->format('d F Y') : '-' }}
</x-mail::panel>

Data kelancaran pembayaran ini telah tercatat secara otomatis di dalam sistem LicenseHub. 

<x-mail::button :url="url('/licenses/' . $license->id)" color="success">
Buka Dashboard Lisensi
</x-mail::button>

Terima kasih,<br>
**Pusat Kendali LicenseHub**
</x-mail::message>