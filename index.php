<?php
//$_POST['tempature'] = 48;
//$_POST["carbonmonoxide"] = 45;
// Firebase Ayarları
$firebaseUrl = '';
$firebaseKey = '';

$currentTimestamp = time();
$userId = 'rEoUDVd1fVQfu1JCbyH8F5hRBHL2';

// Firebase'den system_control değerlerini çek
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'system_control.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: key=' . $firebaseKey
]);

$response = curl_exec($ch);
curl_close($ch);

$systemControl = json_decode($response, true);

// Silent mode değerini kontrol et, yoksa varsayılan olarak false olsun
$silentMode = isset($systemControl['silent_mode']) ? $systemControl['silent_mode'] : false;

// User intervention değerini kontrol et, yoksa varsayılan olarak false olsun
$userIntervention = isset($systemControl['user_intervention']) ? $systemControl['user_intervention'] : false;

// Firebase'den sensör limitlerini çek
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'device_sensors.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: key=' . $firebaseKey
]);

$response = curl_exec($ch);
curl_close($ch);

$deviceSensors = json_decode($response, true);

// Sensör limit değerleri
$thresholds = [
    'tempature' => isset($deviceSensors['sensor_type_1']['limits']) ? floatval($deviceSensors['sensor_type_1']['limits']) : 25,
    'humidity' => isset($deviceSensors['sensor_type_2']['limits']) ? floatval($deviceSensors['sensor_type_2']['limits']) : 50,
    'carbonmonoxide' => isset($deviceSensors['sensor_type_3']['limits']) ? floatval($deviceSensors['sensor_type_3']['limits']) : 1000
];

// Çıkış cihazlarının statüslerini güncellemek için fonksiyon
function updateOutputStatus($firebaseUrl, $firebaseKey, $outputType, $status, $lastAlarmTime = null, $isAcknowledged = null) {
    // Önce mevcut veriyi al
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'device_outputs/' . $outputType . '.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: key=' . $firebaseKey
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $currentData = json_decode($response, true);
    
    // Mevcut veriyi koru ve sadece güncellenecek alanları değiştir
    $data = $currentData;
    
    // Sadece belirtilen alanları güncelle
    if ($status !== null) {
        $data['status'] = $status;
    }
    if ($lastAlarmTime !== null) {
        $data['last_alarm_time'] = $lastAlarmTime;
    }
    if ($isAcknowledged !== null) {
        $data['is_alarm_acknowledged'] = $isAcknowledged;
    }
    
    // Güncellenmiş veriyi gönder
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'device_outputs/' . $outputType . '.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: key=' . $firebaseKey
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Çıkış cihazının mevcut durumunu kontrol etmek için fonksiyon
function getOutputStatus($firebaseUrl, $firebaseKey, $outputType) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'device_outputs/' . $outputType . '.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: key=' . $firebaseKey
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Firebase'den son ölçümleri çeken fonksiyon
function getLastMeasurements($firebaseUrl, $firebaseKey) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'measurements.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: key=' . $firebaseKey
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    if ($data) {
        // Son ölçümleri bul
        $lastMeasurements = [];
        foreach ($data as $timestamp => $measurements) {
            foreach ($measurements as $type => $measurement) {
                if (!isset($lastMeasurements[$type]) || 
                    (isset($measurement['timestamp']) && 
                     (!isset($lastMeasurements[$type]['timestamp']) || 
                      $measurement['timestamp'] > $lastMeasurements[$type]['timestamp']))) {
                    $lastMeasurements[$type] = $measurement;
                }
            }
        }
        
        return [
            'tempature' => isset($lastMeasurements['sicaklik_measurements']['value']) ? $lastMeasurements['sicaklik_measurements']['value'] : null,
            'humidity' => isset($lastMeasurements['nem_measurements']['value']) ? $lastMeasurements['nem_measurements']['value'] : null,
            'carbonmonoxide' => isset($lastMeasurements['karbonmonoksit_measurements']['value']) ? $lastMeasurements['karbonmonoksit_measurements']['value'] : null
        ];
    }
    return null;
}

// first_alarm değerini güncelleyen fonksiyon
function setFirstAlarm($firebaseUrl, $firebaseKey, $outputType, $value) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'device_outputs/' . $outputType . '/first_alarm.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($value));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: key=' . $firebaseKey
    ]);
    curl_exec($ch);
    curl_close($ch);
}

// Arduino'dan gelen sensör verilerini kontrol et
$measurements = [];
$outputsToUpdate = [];
$alarms_log = [];

// Sıcaklık ölçümü
if(isset($_POST['tempature']) && $_POST['tempature'] !== '') {
    $tempature = floatval($_POST['tempature']);
    $measurements['sicaklik_measurements'] = [
        'sensor_id' => 'sensor_type_1',
        'device_id' => 'device1',
        'value' => $tempature,
        'timestamp' => $currentTimestamp
    ];
    
    // Mevcut alarm durumunu kontrol et
    $currentStatus = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_1');
    $isAcknowledged = isset($currentStatus['is_alarm_acknowledged']) ? $currentStatus['is_alarm_acknowledged'] : false;
    $currentStatusValue = isset($currentStatus['status']) ? $currentStatus['status'] : 0;

     // Firebase'den son karbonmonoksit ölçümünü kontrol et
    $lastMeasurements = getLastMeasurements($firebaseUrl, $firebaseKey);
    $coValue = isset($lastMeasurements['carbonmonoxide']) ? $lastMeasurements['carbonmonoxide'] : null;
    
    // Eğer kullanıcı kontrolünde ve değer eşik değerinin altında ve status=0 ise
    if ($coValue < $thresholds["carbonmonoxide"] && $userIntervention && $tempature <= $thresholds['tempature'] && $currentStatusValue == 0) {
        // Kullanıcı kontrolü VE sessiz mod aktifse
        if ($silentMode) {
            // Tüm çıkışların status değerlerini kontrol et
            $status1 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_1');
            $status2 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_2');
            $status3 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_3');
            
            $status1Value = isset($status1['status']) ? $status1['status'] : 0;
            $status2Value = isset($status2['status']) ? $status2['status'] : 0;
            $status3Value = isset($status3['status']) ? $status3['status'] : 0;
            
            // Eğer tüm çıkışlar kapalıysa (0) firstAlarm true olsun
            if ($status1Value == 0 && $status2Value == 0 && $status3Value == 0) {
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', true);
            } else {
                // Herhangi bir çıkış aktifse (1) firstAlarm false olsun
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', false);
            }
        } else {
            // Sessiz mod aktif değilse, firstAlarm true olsun
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', true);
        }
        
        // first_alarm değeri güncellendikten sonra is_alarm_acknowledged değerini güncelle
        $isAcknowledged = false;
        // Durumu Firebase'e kaydet
        updateOutputStatus($firebaseUrl, $firebaseKey, 'output_type_1', null, null, false);
    }
    
    if($tempature > $thresholds['tempature']) {
        // Alarm açıklamasını sessiz mod durumuna göre ayarla
        $alarmDescription = $silentMode ? 'Eşik değeri aşıldı ancak sessiz modda' : 'Eşik değeri aşıldığı için alarm çalıştı';
        
        $alarms_log['sicaklik_alarm'] = [
            'sensor_id' => 'sensor_type_1',
            'device_id' => 'device1',
            'value' => $tempature,
            'threshold' => $thresholds['tempature'],
            'alarm_type' => 'Tehlike',
            'alarm_description' => $alarmDescription,
            'created_at' => $currentTimestamp,
            'triggered_output' => 'output_type_1',
            'is_alarm_acknowledged' => $isAcknowledged,
            'last_alarm_time' => $currentTimestamp
        ];
    
        // Eğer sessiz mod aktifse, first_alarm'ı false yap
        if ($silentMode) {
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', false);
        }
        
        // Sıcaklık alarmı tetikleme mantığı
        // 1. Sessiz mod kapalıysa
        // 2. VE (Mekanik müdahale VEYA Kullanıcı müdahalesi aktifse) alarmı çalıştır
        if (!$silentMode && ($systemControl['mechanical_intervention'] || $userIntervention) && !$isAcknowledged) {
            // Alarm tetiklenince first_alarm'ı false yap
            if ($currentStatus["first_alarm"]) {
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', false);
            }
            
            // first_alarm değeri güncellendikten sonra is_alarm_acknowledged değerini güncelle
            $outputsToUpdate['output_type_1'] = [
                'status' => 1,
                'is_alarm_acknowledged' => false,
                'last_alarm_time' => $currentTimestamp
            ];
        }
    } else {
        // Alarm açıklamasını sessiz mod durumuna göre ayarla
        $alarmDescription = $silentMode ? 'Değer normal seviyede ancak sessiz modda' : 'Değer normal seviyeye döndü.';
        
        $alarms_log['sicaklik_alarm'] = [
            'sensor_id' => 'sensor_type_1',
            'device_id' => 'device1',
            'value' => $tempature,
            'threshold' => $thresholds['tempature'],
            'alarm_type' => 'Normal',
            'alarm_description' => $alarmDescription,
            'created_at' => $currentTimestamp,
            'triggered_output' => '',
            'is_alarm_acknowledged' => false,
            'last_alarm_time' => isset($currentStatus['last_alarm_time']) ? $currentStatus['last_alarm_time'] : null
        ];
        
        // Firebase'den son karbonmonoksit ölçümünü kontrol et
        $lastMeasurements = getLastMeasurements($firebaseUrl, $firebaseKey);
        $coValue = isset($lastMeasurements['carbonmonoxide']) ? $lastMeasurements['carbonmonoxide'] : null;
        
        // Eğer karbonmonoksit değeri eşik değerinin altındaysa VE user_intervention false ise sıcaklık çıkışını kapat
        if (($coValue === null || $coValue <= $thresholds['carbonmonoxide']) && !$userIntervention) {
            // Önce first_alarm değerini güncelle
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', true);
            
            // Sonra is_alarm_acknowledged değerini güncelle
            $outputsToUpdate['output_type_1'] = [
                'status' => 0,
                'is_alarm_acknowledged' => false
            ];
        }
    }
}

// Nem ölçümü
if(isset($_POST['humidity']) && $_POST['humidity'] !== '') {
    $humidity = floatval($_POST['humidity']);
    $measurements['nem_measurements'] = [
        'sensor_id' => 'sensor_type_2',
        'device_id' => 'device1',
        'value' => $humidity,
        'timestamp' => $currentTimestamp
    ];
    
    // Mevcut alarm durumunu kontrol et
    $currentStatus = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_2');
    $isAcknowledged = isset($currentStatus['is_alarm_acknowledged']) ? $currentStatus['is_alarm_acknowledged'] : false;
    $currentStatusValue = isset($currentStatus['status']) ? $currentStatus['status'] : 0;
    
    // Eğer kullanıcı kontrolünde ve değer eşik değerinin altında ve status=0 ise
    //Eğer karbonmonoksit eşik değerinin üzerinde değilse bunlar yapsın.
    $lastMeasurements = getLastMeasurements($firebaseUrl, $firebaseKey);
    $coValue = isset($lastMeasurements['carbonmonoxide']) ? $lastMeasurements['carbonmonoxide'] : null;

    if ($coValue < $thresholds["carbonmonoxide"] && $userIntervention && $humidity <= $thresholds['humidity'] && $currentStatusValue == 0) {
        // Kullanıcı kontrolü VE sessiz mod aktifse
        if ($silentMode) {
            // Tüm çıkışların status değerlerini kontrol et
            $status1 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_1');
            $status2 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_2');
            $status3 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_3');
            
            $status1Value = isset($status1['status']) ? $status1['status'] : 0;
            $status2Value = isset($status2['status']) ? $status2['status'] : 0;
            $status3Value = isset($status3['status']) ? $status3['status'] : 0;
            
            // Eğer tüm çıkışlar kapalıysa (0) firstAlarm true olsun
            if ($status1Value == 0 && $status2Value == 0 && $status3Value == 0) {
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', true);
            } else {
                // Herhangi bir çıkış aktifse (1) firstAlarm false olsun
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', false);
            }
        } else {
            // Sessiz mod aktif değilse, firstAlarm true olsun
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', true);
        }
        
        // first_alarm değeri güncellendikten sonra is_alarm_acknowledged değerini güncelle
        $isAcknowledged = false;
        // Durumu Firebase'e kaydet
        updateOutputStatus($firebaseUrl, $firebaseKey, 'output_type_2', null, null, false);
    }

    // Alarm verilerini ayrı bir array'de topla
    if($humidity > $thresholds['humidity']) {
        // Alarm açıklamasını sessiz mod durumuna göre ayarla
        $alarmDescription = $silentMode ? 'Eşik değeri aşıldı ancak sessiz modda' : 'Eşik değeri aşıldığı için alarm çalıştı';
        
        $alarms_log['nem_alarm'] = [
            'sensor_id' => 'sensor_type_2',
            'device_id' => 'device1',
            'value' => $humidity,
            'threshold' => $thresholds['humidity'],
            'alarm_type' => 'Tehlike',
            'alarm_description' => $alarmDescription,
            'created_at' => $currentTimestamp,
            'triggered_output' => 'output_type_2',
            'is_alarm_acknowledged' => $isAcknowledged,
            'last_alarm_time' => $currentTimestamp
        ];
        
        // Eğer sessiz mod aktifse, first_alarm'ı false yap
        if ($silentMode) {
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', false);
        }
        
        // Nem alarmı tetikleme mantığı
        // 1. Sessiz mod kapalıysa
        // 2. VE (Mekanik müdahale VEYA Kullanıcı müdahalesi aktifse) alarmı çalıştır
        if (!$silentMode && ($systemControl['mechanical_intervention'] || $userIntervention) && !$isAcknowledged) {
            // Alarm tetiklenince first_alarm'ı false yap
            if ($currentStatus["first_alarm"]) {
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', false);
            }
            
            // first_alarm değeri güncellendikten sonra is_alarm_acknowledged değerini güncelle
            $outputsToUpdate['output_type_2'] = [
                'status' => 1,
                'is_alarm_acknowledged' => false,
                'last_alarm_time' => $currentTimestamp
            ];
        }
    } else {
        // Alarm açıklamasını sessiz mod durumuna göre ayarla
        $alarmDescription = $silentMode ? 'Değer normal seviyede ancak sessiz modda' : 'Değer normal seviyeye döndü.';
        
        $alarms_log['nem_alarm'] = [
            'sensor_id' => 'sensor_type_2',
            'device_id' => 'device1',
            'value' => $humidity,
            'threshold' => $thresholds['humidity'],
            'alarm_type' => 'Normal',
            'alarm_description' => $alarmDescription,
            'created_at' => $currentTimestamp,
            'triggered_output' => '',
            'is_alarm_acknowledged' => false,
            'last_alarm_time' => isset($currentStatus['last_alarm_time']) ? $currentStatus['last_alarm_time'] : null
        ];
        
        // Firebase'den son karbonmonoksit ölçümünü kontrol et
        $lastMeasurements = getLastMeasurements($firebaseUrl, $firebaseKey);
        $coValue = isset($lastMeasurements['carbonmonoxide']) ? $lastMeasurements['carbonmonoxide'] : null;
        
        // Eğer karbonmonoksit değeri eşik değerinin altındaysa VE user_intervention false ise nem çıkışını kapat
        if (($coValue === null || $coValue <= $thresholds['carbonmonoxide']) && !$userIntervention) {
            // Önce first_alarm değerini güncelle
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', true);
            
            // Sonra is_alarm_acknowledged değerini güncelle
            $outputsToUpdate['output_type_2'] = [
                'status' => 0,
                'is_alarm_acknowledged' => false
            ];
        }
    }
}

// Karbonmonoksit ölçümü
if(isset($_POST['carbonmonoxide']) && $_POST['carbonmonoxide'] !== '') {
    $carbonmonoxide = floatval($_POST['carbonmonoxide']);
    $measurements['karbonmonoksit_measurements'] = [
        'sensor_id' => 'sensor_type_3',
        'device_id' => 'device1',
        'value' => $carbonmonoxide,
        'timestamp' => $currentTimestamp
    ];
    
    // Mevcut alarm durumunu kontrol et
    $currentStatus_1 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_1');
    $currentStatus_2 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_2');
    $currentStatus_3 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_3');
    $isAcknowledged_1 = isset($currentStatus_1['is_alarm_acknowledged']) ? $currentStatus_1['is_alarm_acknowledged'] : false;
    $isAcknowledged_2 = isset($currentStatus_2['is_alarm_acknowledged']) ? $currentStatus_2['is_alarm_acknowledged'] : false;
    $isAcknowledged_3 = isset($currentStatus_3['is_alarm_acknowledged']) ? $currentStatus_3['is_alarm_acknowledged'] : false;
    $currentStatusValue_1 = isset($currentStatus_1['status']) ? $currentStatus_1['status'] : 0;
    $currentStatusValue_2 = isset($currentStatus_2['status']) ? $currentStatus_2['status'] : 0;
    $currentStatusValue_3 = isset($currentStatus_3['status']) ? $currentStatus_3['status'] : 0;
    // Eğer POST verisi yoksa, son ölçümleri Firebase'den al
    if (!isset($_POST['tempature']) || !isset($_POST['humidity'])) {
        $lastMeasurements = getLastMeasurements($firebaseUrl, $firebaseKey);
        
        if ($lastMeasurements) {
            if (!isset($_POST['tempature'])) {
                $_POST['tempature'] = $lastMeasurements['tempature'];
            }
            if (!isset($_POST['humidity'])) {
                $_POST['humidity'] = $lastMeasurements['humidity'];
            }
        } else {
            echo "Firebase'den veri alınamadı!\n";
        }
    }
    $tempature = floatval($_POST['tempature']);
    $humidity = floatval($_POST['humidity']);
    
    // Eğer kullanıcı kontrolünde ve değer eşik değerinin altında ve tüm status değerleri 0 ise
    if ($userIntervention && $carbonmonoxide <= $thresholds['carbonmonoxide'] && $currentStatusValue_1 == 0 && $currentStatusValue_2 == 0 && $currentStatusValue_3 == 0) {
        // Kullanıcı kontrolü VE sessiz mod aktifse
        if ($silentMode) {
            // Tüm çıkışların status değerlerini kontrol et
            $status1 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_1');
            $status2 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_2');
            $status3 = getOutputStatus($firebaseUrl, $firebaseKey, 'output_type_3');
            
            $status1Value = isset($status1['status']) ? $status1['status'] : 0;
            $status2Value = isset($status2['status']) ? $status2['status'] : 0;
            $status3Value = isset($status3['status']) ? $status3['status'] : 0;
            
            // Eğer tüm çıkışlar kapalıysa (0) firstAlarm true olsun
            if ($status1Value == 0 && $status2Value == 0 && $status3Value == 0) {
                if($tempature <= $thresholds['tempature']) {
                    setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', true);
                }
                if($humidity <= $thresholds['humidity']) {
                    setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', true);
                }
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_3', true);
            } else {
                
                // Herhangi bir çıkış aktifse (1) firstAlarm false olsun
                if($tempature <= $thresholds['tempature']) {
                    setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', false);
                }
                if($humidity <= $thresholds['humidity']) {
                    setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', false);
                }
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_3', false);
            }
        } else {
            // Sessiz mod aktif değilse, firstAlarm true olsun
            if($tempature <= $thresholds['tempature']) {
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', true);
            }
            if($humidity <= $thresholds['humidity']) {
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', true);
            }
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_3', true);
        }
        
        // first_alarm değerleri güncellendikten sonra is_alarm_acknowledged değerlerini güncelle
        
        if($tempature <= $thresholds['tempature']) {
            $isAcknowledged_1 = false;
            updateOutputStatus($firebaseUrl, $firebaseKey, 'output_type_1', null, null, false);
        }
        if($humidity <= $thresholds['humidity']) {
            $isAcknowledged_2 = false;
            updateOutputStatus($firebaseUrl, $firebaseKey, 'output_type_2', null, null, false);
        }
        $isAcknowledged_3 = false;
        updateOutputStatus($firebaseUrl, $firebaseKey, 'output_type_3', null, null, false);
    }
    
    // Alarm verilerini ayrı bir array'de topla
    if($carbonmonoxide > $thresholds['carbonmonoxide']) {
        // Alarm açıklamasını sessiz mod durumuna göre ayarla
        $alarmDescription = $silentMode ? 'Eşik değeri aşıldı ancak sessiz modda' : 'Eşik değeri aşıldığı için alarm çalıştı';
        
        $alarms_log['karbonmonoksit_alarm'] = [
            'sensor_id' => 'sensor_type_3',
            'device_id' => 'device1',
            'value' => $carbonmonoxide,
            'threshold' => $thresholds['carbonmonoxide'],
            'alarm_type' => 'Tehlike',
            'alarm_description' => $alarmDescription,
            'created_at' => $currentTimestamp,
            'triggered_output' => 'output_type_3',
            'is_alarm_acknowledged' => $isAcknowledged_3,
            'last_alarm_time' => $currentTimestamp
        ];
        
        // Eğer sessiz mod aktifse, first_alarm'ı false yap
        if ($silentMode) {
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', false);
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', false);
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_3', false);
        }
        
        // Karbonmonoksit alarmında tüm çıkışları aktif et
        // 1. Sessiz mod kapalıysa
        // 2. VE (Mekanik müdahale VEYA Kullanıcı müdahalesi aktifse)
        if (!$silentMode && ($systemControl['mechanical_intervention'] || $userIntervention)) {
            if(!$isAcknowledged_1) {
                // Alarm tetiklenince first_alarm'ı false yap
                if ($currentStatus_1["first_alarm"]) {
                    setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', false);
                }
                
                // first_alarm değeri güncellendikten sonra is_alarm_acknowledged değerini güncelle
                $outputsToUpdate['output_type_1'] = [
                    'status' => 1,
                    'is_alarm_acknowledged' => false,
                    'last_alarm_time' => $currentTimestamp
                ];
            }
            if(!$isAcknowledged_2) {
                // Alarm tetiklenince first_alarm'ı false yap
                if ($currentStatus_2["first_alarm"]) {
                    setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', false);
                }
                
                // first_alarm değeri güncellendikten sonra is_alarm_acknowledged değerini güncelle
                $outputsToUpdate['output_type_2'] = [
                    'status' => 1,
                    'is_alarm_acknowledged' => false,
                    'last_alarm_time' => $currentTimestamp
                ];
            }
            if(!$isAcknowledged_3) {
                // Alarm tetiklenince first_alarm'ı false yap
                if ($currentStatus_3["first_alarm"]) {
                    setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_3', false);
                }
                
                // first_alarm değeri güncellendikten sonra is_alarm_acknowledged değerini güncelle
                $outputsToUpdate['output_type_3'] = [
                    'status' => 1,
                    'is_alarm_acknowledged' => false,
                    'last_alarm_time' => $currentTimestamp
                ];
            }
        }
    } else {
        // Alarm açıklamasını sessiz mod durumuna göre ayarla
        $alarmDescription = $silentMode ? 'Değer normal seviyede ancak sessiz modda' : 'Değer normal seviyeye döndü.';
        
        $alarms_log['karbonmonoksit_alarm'] = [
            'sensor_id' => 'sensor_type_3',
            'device_id' => 'device1',
            'value' => $carbonmonoxide,
            'threshold' => $thresholds['carbonmonoxide'],
            'alarm_type' => 'Normal',
            'alarm_description' => $alarmDescription,
            'created_at' => $currentTimestamp,
            'triggered_output' => '',
            'is_alarm_acknowledged' => false,
            'last_alarm_time' => isset($currentStatus_3['last_alarm_time']) ? $currentStatus_3['last_alarm_time'] : null
        ];
    
        // Eğer POST verisi yoksa, son ölçümleri Firebase'den al
        if (!isset($_POST['tempature']) || !isset($_POST['humidity']) || !isset($_POST['carbonmonoxide'])) {
            $lastMeasurements = getLastMeasurements($firebaseUrl, $firebaseKey);
            
            if ($lastMeasurements) {
                if (!isset($_POST['tempature'])) {
                    $_POST['tempature'] = $lastMeasurements['tempature'];
                }
                if (!isset($_POST['humidity'])) {
                    $_POST['humidity'] = $lastMeasurements['humidity'];
                }
                if (!isset($_POST['carbonmonoxide'])) {
                    $_POST['carbonmonoxide'] = $lastMeasurements['carbonmonoxide'];
                }
            } else {
                echo "Firebase'den veri alınamadı!\n";
            }
        }

        // Karbonmonoksit normal olduğunda ve user_intervention false ise
        if (!$userIntervention) {
            // Önce first_alarm değerini güncelle
            setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_3', true);
            
            // Sonra is_alarm_acknowledged değerini güncelle
            $outputsToUpdate['output_type_3'] = [
                'status' => 0,
                'is_alarm_acknowledged' => false
            ];
        }
        
        // Diğer sensörlerin durumlarını kontrol et - bu bölümü tamamen değiştir
        if(isset($_POST['tempature']) && $_POST['tempature'] !== '') {
            $tempature = floatval($_POST['tempature']);
            if($tempature <= $thresholds['tempature'] && !$userIntervention) {
                // Önce first_alarm değerini güncelle
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_1', true);
                
                // Sonra is_alarm_acknowledged değerini güncelle
                $outputsToUpdate['output_type_1'] = [
                    'status' => 0,
                    'is_alarm_acknowledged' => false
                ];
            }
        }
        if(isset($_POST['humidity']) && $_POST['humidity'] !== '') {
            $humidity = floatval($_POST['humidity']);
            if($humidity <= $thresholds['humidity'] && !$userIntervention) {
                // Önce first_alarm değerini güncelle
                setFirstAlarm($firebaseUrl, $firebaseKey, 'output_type_2', true);
                
                // Sonra is_alarm_acknowledged değerini güncelle
                $outputsToUpdate['output_type_2'] = [
                    'status' => 0,
                    'is_alarm_acknowledged' => false
                ];
            }
        }
    }
}

// Eğer hiç ölçüm yoksa işlemi sonlandır
if(empty($measurements)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Hiç sensör verisi gönderilmedi'
    ]);
    exit;
}

// Threshold'u aşan değerler için çıkış cihazlarının statüslerini güncelle
$outputUpdateResults = [];
foreach($outputsToUpdate as $outputType => $data) {
    $result = updateOutputStatus($firebaseUrl, $firebaseKey, $outputType, 
        isset($data['status']) ? $data['status'] : null,
        isset($data['last_alarm_time']) ? $data['last_alarm_time'] : null,
        isset($data['is_alarm_acknowledged']) ? $data['is_alarm_acknowledged'] : null);
    $outputUpdateResults[$outputType] = json_decode($result, true);
}

// Ölçümleri Firebase'e gönder
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'measurements.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($measurements));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: key=' . $firebaseKey
]);

$measurementsResponse = curl_exec($ch);

// Eğer alarm verisi varsa, alarms_log'a gönder
$alarmsResponse = null;
if(!empty($alarms_log)) {
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl . 'alarms_log.json');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($alarms_log));
    $alarmsResponse = curl_exec($ch);
}

if ($measurementsResponse === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Firebase\'e veri gönderilirken hata: ' . curl_error($ch)
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'Sensor verileri ve alarmlar başarıyla Firebase\'e kaydedildi',
        'measurements_response' => json_decode($measurementsResponse, true),
        'alarms_response' => $alarmsResponse ? json_decode($alarmsResponse, true) : null,
        'output_updates' => $outputUpdateResults
    ]);
}

curl_close($ch);

?>
