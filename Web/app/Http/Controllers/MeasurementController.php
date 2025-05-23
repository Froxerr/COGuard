<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class MeasurementController extends Controller
{
    protected $database;

    public function __construct()
    {
        try {
            Log::info('Firebase bağlantısı başlatılıyor', [
                'credentials_path' => storage_path('app/' . env('FIREBASE_CREDENTIALS')),
                'database_url' => env('FIREBASE_DATABASE_URL')
            ]);

            $factory = (new Factory)
                ->withServiceAccount(storage_path('app/' . env('FIREBASE_CREDENTIALS')))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

            $this->database = $factory->createDatabase();

            Log::info('Firebase bağlantısı başarıyla kuruldu');
        } catch (\Exception $e) {
            Log::error('Firebase bağlantısı kurulurken hata oluştu', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function index()
    {
        if (!Session::has('user')) {
            Log::warning('Oturum açmamış kullanıcı ölçüm sayfasına erişmeye çalıştı');
            return redirect("/login");
        }

        Log::info('Ölçüm sayfası görüntülendi', [
            'user' => Session::get('user')['kulad'] ?? 'Bilinmeyen kullanıcı'
        ]);

        return view('panel.measurements-history');
    }

    public function getMeasurements(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $type = $request->input('type', 'all');

            Log::info('Ölçüm verileri isteği alındı', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => $type
            ]);

            $measurements = [];

            // Firebase'den verileri al
            $snapshot = $this->database->getReference('measurements')->getSnapshot();

            if ($snapshot->exists()) {
                $data = $snapshot->getValue();
                Log::info('Firebase\'den veri alındı', ['data_count' => count($data)]);

                foreach ($data as $key => $value) {
                    Log::info("Firebase'den veri çekiliyor: {$key}", ['value' => $value]);

                    // Her bir ölçüm tipini kontrol et
                    $measurementTypes = [
                        'sicaklik_measurements' => ['type' => 'temperature', 'unit' => '°C'],
                        'nem_measurements' => ['type' => 'humidity', 'unit' => '%'],
                        'karbonmonoksit_measurements' => ['type' => 'co', 'unit' => 'ppm']
                    ];

                    foreach ($measurementTypes as $measurementKey => $measurementInfo) {
                        if (isset($value[$measurementKey])) {
                            $measurementData = $value[$measurementKey];

                            // Eğer tip filtresi varsa ve uyuşmuyorsa atla
                            if ($type !== 'all' && $measurementInfo['type'] !== $type) {
                                Log::info("Tip filtresi uyuşmadı, atlanıyor", [
                                    'filtered_type' => $type,
                                    'measurement_type' => $measurementInfo['type']
                                ]);
                                continue;
                            }

                            // Timestamp kontrolü ve dönüşümü
                            $timestamp = isset($measurementData['timestamp']) ? intval($measurementData['timestamp']) : time();

                            // Eğer timestamp saniye cinsindense milisaniyeye çevir
                            if ($timestamp < 1000000000000) {
                                $timestamp = $timestamp * 1000;
                            }

                            // Eğer geçersiz timestamp ise (1970-01-01 gibi), şu anki timestamp'i kullan
                            if ($timestamp < 946684800000) { // 2000-01-01 00:00:00 UTC
                                $timestamp = time() * 1000;
                            }

                            // Tarihi formatla (Türkiye saat dilimine göre)
                            $date = new \DateTime();
                            $date->setTimestamp($timestamp / 1000);
                            $date->setTimezone(new \DateTimeZone('Europe/Istanbul'));
                            $formattedDate = $date->format('d.m.Y H:i:s');

                            Log::info("Ölçüm verisi işleniyor", [
                                'original_timestamp' => $measurementData['timestamp'] ?? 'yok',
                                'converted_timestamp' => $timestamp,
                                'formatted_date' => $formattedDate,
                                'type' => $measurementInfo['type']
                            ]);

                            // Tarih filtreleme
                            $includeMeasurement = true;
                            $filterReason = '';

                            if ($startDate) {
                                $startDateTime = new \DateTime($startDate);
                                $startDateTime->setTimezone(new \DateTimeZone('Europe/Istanbul'));
                                // UTC timestamp'e çevir
                                $startTimestamp = ($startDateTime->getTimestamp() - (3 * 3600)) * 1000;

                                Log::info("Başlangıç tarihi kontrolü", [
                                    'measurement_date' => $formattedDate,
                                    'measurement_timestamp' => $timestamp,
                                    'start_date' => $startDate,
                                    'start_timestamp' => $startTimestamp,
                                    'comparison' => $timestamp >= $startTimestamp ? 'geçerli' : 'filtrelendi'
                                ]);

                                // Başlangıç tarihinden önceki ölçümleri filtrele
                                if ($timestamp < $startTimestamp) {
                                    $includeMeasurement = false;
                                    $filterReason = 'Başlangıç tarihinden önce';
                                }
                            }

                            if ($endDate && $includeMeasurement) {
                                $endDateTime = new \DateTime($endDate);
                                $endDateTime->setTimezone(new \DateTimeZone('Europe/Istanbul'));
                                // UTC timestamp'e çevir
                                $endTimestamp = ($endDateTime->getTimestamp() - (3 * 3600)) * 1000;

                                Log::info("Bitiş tarihi kontrolü", [
                                    'measurement_date' => $formattedDate,
                                    'measurement_timestamp' => $timestamp,
                                    'end_date' => $endDate,
                                    'end_timestamp' => $endTimestamp,
                                    'comparison' => $timestamp <= $endTimestamp ? 'geçerli' : 'filtrelendi'
                                ]);

                                // Bitiş tarihinden sonraki ölçümleri filtrele
                                if ($timestamp > $endTimestamp) {
                                    $includeMeasurement = false;
                                    $filterReason = 'Bitiş tarihinden sonra';
                                }
                            }

                            if (!$includeMeasurement) {
                                Log::info("Ölçüm filtrelendi", [
                                    'measurement_date' => $formattedDate,
                                    'filter_reason' => $filterReason
                                ]);
                                continue;
                            }

                            // Ölçüm verisini ekle
                            $measurements[] = [
                                'id' => $key,
                                'timestamp' => $timestamp,
                                'formatted_date' => $formattedDate,
                                'type' => $measurementInfo['type'],
                                'value' => floatval($measurementData['value']),
                                'unit' => $measurementInfo['unit'],
                                'device_id' => $measurementData['device_id'] ?? '',
                                'sensor_id' => $measurementData['sensor_id'] ?? ''
                            ];
                        }
                    }
                }

                // Tarihe göre sırala (en yeniden en eskiye)
                usort($measurements, function($a, $b) {
                    return $b['timestamp'] - $a['timestamp'];
                });

                Log::info('Ölçüm verileri başarıyla alındı', [
                    'count' => count($measurements),
                    'first_measurement' => $measurements[0] ?? 'veri yok',
                    'last_measurement' => end($measurements) ?? 'veri yok'
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $measurements
                ]);
            } else {
                Log::warning('Firebase\'de ölçüm verisi bulunamadı');
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Ölçüm verileri alınırken hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ölçüm verileri alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
