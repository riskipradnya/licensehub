<?php
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.fonnte.com/send',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
    'target' => '085858276816', // Ganti dengan nomor WA Anda
    'message' => 'Ini tes murni tanpa Laravel',
    'url' => 'https://docs.fonnte.com/wp-content/uploads/2022/09/Logo-Fonnte-300x72.png'
  ),
  CURLOPT_HTTPHEADER => array(
    'Authorization: TOKEN_FONNTE_ANDA_DISINI' // Masukkan token asli Anda
  ),
));

$response = curl_exec($curl);
echo $response;
?>