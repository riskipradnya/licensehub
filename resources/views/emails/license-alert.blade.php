<x-mail::message>
# {{ $prefix }} 🚨

Halo Tim IT & Finance,

Sistem LicenseHub mendeteksi adanya lisensi yang membutuhkan perhatian Anda. Berikut adalah rincian lisensi yang mendekati batas waktu pembayaran:

<x-mail::panel>
**Nama Lisensi:** {{ $license->name }}
**Vendor:** {{ $license->vendor->name ?? '-' }}
**Biaya:** {{ $license->formatted_cost }} / {{ $license->billing_cycle }}
</x-mail::panel>

### ⏳ Status Waktu:
* **Tanggal Kedaluwarsa:** {{ $license->expiry_date ? $license->expiry_date->format('d M Y') : '-' }}
* **Sisa Waktu:** @if($license->is_expired)
<span style="color: red; font-weight: bold;">Telah Kedaluwarsa!</span>
@else
<span style="font-weight: bold;">{{ $license->days_until_expiry }} Hari Lagi</span>
@endif

Mohon segera jadwalkan perpanjangan (renewal) untuk menghindari gangguan operasional.

<x-mail::button :url="url('/licenses/' . $license->id)">
Tinjau Lisensi
</x-mail::button>

Terima kasih,<br>
**Pusat Kendali LicenseHub**
</x-mail::message>