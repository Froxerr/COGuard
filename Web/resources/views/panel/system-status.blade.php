@extends('panel.layouts.app')

@section('title', 'Sistem Durumu')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-4">Sistem Durumu</h2>
            </div>
        </div>

        <style>
            /* Gelişmiş İkon Stilleri */
            .metric-icon-wrapper {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(145deg, #f5f7fa, #e2e6ec);
                box-shadow: 5px 5px 10px rgba(0,0,0,0.05), 
                            -5px -5px 10px rgba(255,255,255,0.6);
                margin-right: 20px;
                transition: all 0.3s ease;
            }
            
            .metric-icon-wrapper:hover {
                transform: translateY(-3px);
                box-shadow: 7px 7px 15px rgba(0,0,0,0.07), 
                            -7px -7px 15px rgba(255,255,255,0.8);
            }
            
            .metric-icon {
                font-size: 32px;
                filter: drop-shadow(0 2px 2px rgba(0,0,0,0.1));
            }
            
            /* CO Seviyesi İkon Stili */
            .co-icon-wrapper {
                background: linear-gradient(145deg, #eef6ff, #d5e6fa);
            }
            
            .co-icon {
                color: #4285F4;
                background: linear-gradient(45deg, #4285F4, #34a853);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Sıcaklık İkon Stili */
            .temp-icon-wrapper {
                background: linear-gradient(145deg, #fff5e6, #ffe8cc);
            }
            
            .temp-icon {
                color: #FBBC05;
                background: linear-gradient(45deg, #FBBC05, #EA4335);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Nem İkon Stili */
            .humidity-icon-wrapper {
                background: linear-gradient(145deg, #e8f8ff, #d0eefb);
            }
            
            .humidity-icon {
                color: #34A8FF;
                background: linear-gradient(45deg, #34A8FF, #0F9AF5);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Sistem Durumu İkon Stili */
            .system-icon-wrapper {
                background: linear-gradient(145deg, #e8fff0, #d0fbe0);
            }
            
            .system-icon {
                color: #34A853;
                background: linear-gradient(45deg, #34A853, #178038);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Tablo sensör ikonları */
            .sensor-icon {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                margin-right: 15px;
                font-size: 26px;
            }
            
            .sensor-icon-co {
                background: linear-gradient(145deg, #eef6ff, #d5e6fa);
                color: #4285F4;
                background-clip: padding-box;
            }
            
            .sensor-icon-temp {
                background: linear-gradient(145deg, #fff5e6, #ffe8cc);
                color: #FBBC05;
                background-clip: padding-box;
            }
            
            .sensor-icon-humidity {
                background: linear-gradient(145deg, #e8f8ff, #d0eefb);
                color: #34A8FF;
                background-clip: padding-box;
            }
            
            /* Kart gölge efekti */
            .hover-lift {
                transition: all 0.3s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-7px);
                box-shadow: 0 15px 30px rgba(0,0,0,0.07) !important;
            }
            
            /* Diğer gerekli stiller */
            .bg-success-subtle {
                background-color: rgba(25, 135, 84, 0.1);
            }
            
            .bg-warning-subtle {
                background-color: rgba(255, 193, 7, 0.1);
            }
            
            .bg-info-subtle {
                background-color: rgba(13, 202, 240, 0.1);
            }
            
            .bg-danger-subtle {
                background-color: rgba(220, 53, 69, 0.1);
            }
            
            .bg-primary-subtle {
                background-color: rgba(13, 110, 253, 0.1);
            }
            
            .bg-secondary-subtle {
                background-color: rgba(108, 117, 125, 0.1);
                border: 1px solid rgba(108, 117, 125, 0.3);
            }
            
            .progress {
                background-color: rgba(0,0,0,0.05);
                border-radius: 10px;
            }
            
            .progress-bar {
                border-radius: 10px;
            }
            
            /* Sistem kontrol özellikleri için stiller */
            .system-control-item {
                padding: 8px 12px;
                border-radius: 8px;
                transition: all 0.3s ease;
                margin-bottom: 6px;
            }
            
            .system-control-item:hover {
                background-color: rgba(0,0,0,0.03);
            }
            
            /* Badge stilleri */
            .badge {
                font-size: 0.85rem;
                padding: 6px 10px;
                border-radius: 50px;
                transition: all 0.3s ease;
            }
            
            /* Sistem durumu kartı için özel stil */
            .system-status-card {
                min-height: 310px;
            }
        </style>

        <!-- Sistem Durumu Kartları -->
        <div class="row g-4 mb-4">
            <!-- CO Seviyesi Kartı -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm hover-lift">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="metric-icon-wrapper co-icon-wrapper">
                                <i class="fas fa-smog metric-icon co-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">CO Seviyesi</h6>
                                <h2 class="card-title mb-0 fw-bold" id="co-value">0 ppm</h2>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="co-progress"></div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted" id="co-threshold">Eşik: 167 ppm</small>
                            <span class="badge bg-success-subtle text-success" id="co-status">Normal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sıcaklık Kartı -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm hover-lift">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="metric-icon-wrapper temp-icon-wrapper">
                                <i class="fas fa-temperature-high metric-icon temp-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Sıcaklık</h6>
                                <h2 class="card-title mb-0 fw-bold" id="temp-value">0°C</h2>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" id="temp-progress"></div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted" id="temp-threshold">Eşik: 36°C</small>
                            <span class="badge bg-warning-subtle text-warning" id="temp-status">Normal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nem Kartı -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm hover-lift">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="metric-icon-wrapper humidity-icon-wrapper">
                                <i class="fas fa-tint metric-icon humidity-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Nem Oranı</h6>
                                <h2 class="card-title mb-0 fw-bold" id="hum-value">0%</h2>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" id="hum-progress"></div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted" id="hum-threshold">Eşik: 71%</small>
                            <span class="badge bg-info-subtle text-info" id="hum-status">Normal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistem Durumu Kartı -->
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm hover-lift system-status-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="metric-icon-wrapper system-icon-wrapper">
                                <i class="fas fa-microchip metric-icon system-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Sistem Durumu</h6>
                                <h2 class="card-title mb-0 fw-bold" id="system-status">Aktif</h2>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" id="system-progress"></div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">Son Güncelleme: <span id="last-update">-</span></small>
                            <span class="badge bg-success-subtle text-success" id="system-badge">Çalışıyor</span>
                        </div>
                        
                        <!-- Sistem Kontrol Modu -->
                        <div class="mt-3 d-flex justify-content-between align-items-center system-control-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-cogs me-2 text-primary"></i>
                                <small class="text-muted">Sistem Modu:</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary" id="system-mode-status">Yükleniyor...</span>
                        </div>
                        
                        <!-- Sessiz Mod Durumu -->
                        <div class="mt-2 d-flex justify-content-between align-items-center system-control-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-volume-mute me-2 text-warning"></i>
                                <small class="text-muted">Sessiz Mod:</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary" id="silent-mode-status">Devre Dışı</span>
                        </div>
                        
                        <!-- Telefon Titreşimi Durumu -->
                        <div class="mt-2 d-flex justify-content-between align-items-center system-control-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-mobile-alt me-2 text-info"></i>
                                <small class="text-muted">Telefon Titreşimi:</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary" id="phone-vibrate-status">Devre Dışı</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sensör Durumları -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pt-4 pb-3">
                        <h5 class="card-title mb-0 fw-bold">Sensör Durumları</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th class="border-0">Sensör</th>
                                        <th class="border-0">Durum</th>
                                        <th class="border-0">Son Okuma</th>
                                        <th class="border-0">Değer</th>
                                    </tr>
                                </thead>
                                <tbody id="sensor-table-body">
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="sensor-icon sensor-icon-co">
                                                    <i class="fas fa-smog"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">CO Sensörü</h6>
                                                    <small class="text-muted">ID: CO-001</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success" id="co-status">Normal</span></td>
                                        <td id="co-last-read">-</td>
                                        <td id="co-table-value">0 ppm</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="sensor-icon sensor-icon-temp">
                                                    <i class="fas fa-temperature-high"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Sıcaklık Sensörü</h6>
                                                    <small class="text-muted">ID: TEMP-001</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success" id="temp-status">Normal</span></td>
                                        <td id="temp-last-read">-</td>
                                        <td id="temp-table-value">0°C</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="sensor-icon sensor-icon-humidity">
                                                    <i class="fas fa-tint"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Nem Sensörü</h6>
                                                    <small class="text-muted">ID: HUM-001</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success" id="hum-status">Normal</span></td>
                                        <td id="hum-last-read">-</td>
                                        <td id="hum-table-value">0%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Alarmlar -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pt-4 pb-3">
                        <h5 class="card-title mb-0 fw-bold">Son Alarmlar</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th class="border-0">ID</th>
                                        <th class="border-0">Tarih</th>
                                        <th class="border-0">Alarm Tipi</th>
                                        <th class="border-0">Açıklama</th>
                                        <th class="border-0">Değer</th>
                                        <th class="border-0">Eşik</th>
                                        <th class="border-0">Durum</th>
                                    </tr>
                                </thead>
                                <tbody id="alarm-table-body">
                                    <tr>
                                        <td colspan="7" class="text-center">Yükleniyor...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Firebase SDK'larını yükleyelim -->
    <script>

    // Loglama fonksiyonu
    function log(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logTypes = {
            info: 'color: #3498db',
            success: 'color: #2ecc71',
            warning: 'color: #f39c12',
            error: 'color: #e74c3c'
        };
        console.log(`%c[${timestamp}] ${message}`, logTypes[type] || logTypes.info);
    }

    // Firebase'i başlat
    function initializeFirebase() {
        try {
            log('Firebase bağlantısı kontrol ediliyor...', 'info');
            
            // Firebase zaten başlatılmış mı kontrol et
            if (firebase.apps.length) {
                log('Firebase zaten başlatılmış, mevcut bağlantı kullanılıyor', 'success');
                // Global firebase.database() değişkenini kullan
                return true;
            } else {
                // Hiç başlatılmamışsa başlat (bu durum normalde oluşmamalı çünkü layout'ta başlatılıyor)
                log('Firebase başlatılıyor...', 'info');
                firebase.initializeApp(firebaseConfig);
                database = firebase.database();
                log('Firebase başarıyla başlatıldı', 'success');
                return true;
            }
        } catch (error) {
            log(`Firebase bağlantı hatası: ${error.message}`, 'error');
            return false;
        }
    }

    // Sensör verilerini dinle
    function listenToSensorData() {
        console.log('Sensör verilerini dinlemeye başlıyorum...');

        if (!database) {
            console.error('Firebase veritabanı bağlantısı bulunamadı');
            return;
        }

        try {
            // Sensör eşik değerlerini al
            database.ref('device_sensors').on('value', (snapshot) => {
                const sensors = snapshot.val();
                console.log('Sensör eşik değerleri alındı:', sensors);
                
                if (sensors) {
                    // Global değişkenlere eşik değerlerini ata
                    window.thresholds = {
                        temperature: sensors.sensor_type_1 && sensors.sensor_type_1.limits ? parseFloat(sensors.sensor_type_1.limits) : 36,
                        humidity: sensors.sensor_type_2 && sensors.sensor_type_2.limits ? parseFloat(sensors.sensor_type_2.limits) : 71,
                        co: sensors.sensor_type_3 && sensors.sensor_type_3.limits ? parseFloat(sensors.sensor_type_3.limits) : 167
                    };
                    console.log('Hesaplanan eşik değerleri:', window.thresholds);
                    
                    // Eşik değerleri değiştiğinde görsel olarak da güncelle
                    updateThresholdDisplays();
                }
            });

            // CO Sensörü
            database.ref('measurements').on('value', (snapshot) => {
                const data = snapshot.val();
                console.log('Ham sensör verisi:', data);

                if (!data) {
                    console.warn('Sensör verisi bulunamadı');
                    return;
                }

                // En son ölçümleri bul
                let coValue = null;
                let tempValue = null;
                let humValue = null;
                let coTimestamp = null;
                let tempTimestamp = null;
                let humTimestamp = null;

                // Tüm ölçümleri işle
                Object.entries(data).forEach(([key, measurement]) => {
                    console.log('İşlenen ölçüm:', key, measurement);

                    // Sensör tipini belirle
                    let sensorType = null;

                    // Ölçüm nesnesinin içindeki alanları kontrol et
                    if (measurement.sicaklik_measurements) {
                        sensorType = 'temperature';
                        const sensorData = measurement.sicaklik_measurements;
                        tempValue = sensorData.value;
                        tempTimestamp = sensorData.timestamp;
                        console.log('Sıcaklık değeri bulundu:', tempValue, '°C');
                    } else if (measurement.karbonmonoksit_measurements) {
                        sensorType = 'co';
                        const sensorData = measurement.karbonmonoksit_measurements;
                        coValue = sensorData.value;
                        coTimestamp = sensorData.timestamp;
                        console.log('CO değeri bulundu:', coValue, 'ppm');
                    } else if (measurement.nem_measurements) {
                        sensorType = 'humidity';
                        const sensorData = measurement.nem_measurements;
                        humValue = sensorData.value;
                        humTimestamp = sensorData.timestamp;
                        console.log('Nem değeri bulundu:', humValue, '%');
                    } else if (measurement.sensor_type) {
                        // Eski format için destek
                        sensorType = measurement.sensor_type.toLowerCase();
                        const value = measurement.value !== undefined ? measurement.value :
                                    measurement.sensor_value !== undefined ? measurement.sensor_value : null;
                        const timestamp = measurement.created_at || measurement.timestamp;

                        if (sensorType === 'temperature' || sensorType === 'sicaklik') {
                            tempValue = value;
                            tempTimestamp = timestamp;
                            console.log('Sıcaklık değeri bulundu:', tempValue, '°C');
                        } else if (sensorType === 'humidity' || sensorType === 'nem') {
                            humValue = value;
                            humTimestamp = timestamp;
                            console.log('Nem değeri bulundu:', humValue, '%');
                        } else if (sensorType === 'co' || sensorType === 'karbonmonoksit') {
                            coValue = value;
                            coTimestamp = timestamp;
                            console.log('CO değeri bulundu:', coValue, 'ppm');
                        }
                    }

                    if (!sensorType) {
                        console.warn('Sensör tipi belirlenemedi:', measurement);
                    }
                });

                // Değerleri güncelle
                if (coValue !== null) {
                    updateCOValue(coValue, coTimestamp);
                } else {
                    console.warn('CO değeri bulunamadı');
                }

                if (tempValue !== null) {
                    updateTemperatureValue(tempValue, tempTimestamp);
                } else {
                    console.warn('Sıcaklık değeri bulunamadı');
                }

                if (humValue !== null) {
                    updateHumidityValue(humValue, humTimestamp);
                } else {
                    console.warn('Nem değeri bulunamadı');
                }
            });

            // Sistem kontrol durumunu dinle
            database.ref('system_control').on('value', (snapshot) => {
                const systemControl = snapshot.val();
                if (systemControl) {
                    console.log('Sistem kontrol durumu alındı:', systemControl);
                    
                    // Tüm sistem kontrol durumlarını güncelle
                    updateSystemModeStatus(systemControl.mechanical_intervention, systemControl.user_intervention);
                    updateSilentModeStatus(systemControl.silent_mode);
                    updatePhoneVibrateStatus(systemControl.phone_vibrate);
                } else {
                    console.warn('Sistem kontrol durumu bulunamadı');
                }
            });

            // Alarm durumlarını dinle
            database.ref('device_outputs').on('value', (snapshot) => {
                const outputs = snapshot.val();
                if (outputs) {
                    console.log('Alarm durumları alındı:', outputs);
                    updateAlarmStatus(outputs);
                } else {
                    console.warn('Alarm durumu bulunamadı');
                }
            });

            // Alarm loglarını dinle
            database.ref('alarms_log').on('value', (snapshot) => {
                const alarms = snapshot.val();
                if (alarms) {
                    console.log('Alarm logları alındı:', alarms);
                    
                    // Verileri detaylı loglama
                    console.log('ALARM YAPISI DETAYLI ANALIZ:', JSON.stringify(alarms, null, 2));
                    
                    // İlk 5 alarm öğesini detaylı incele
                    let count = 0;
                    for (const key in alarms) {
                        if (count >= 5) break;
                        console.log(`ALARM DETAY #${count+1}:`, key, JSON.stringify(alarms[key], null, 2));
                        count++;
                    }
                    
                    updateAlarmLogs(alarms);
                } else {
                    console.warn('Alarm logu bulunamadı');
                }
            });

            console.log('Tüm sensör verileri dinleniyor');
        } catch (error) {
            console.error('Sensör verisi işlenirken hata:', error);
        }
    }

    // CO değerlerini güncelle
    function updateCOValue(value, timestamp) {
        try {
            console.log('CO değeri güncelleniyor:', value, timestamp);

            // Değer kontrolü
            if (typeof value !== 'number' || isNaN(value)) {
                console.warn('Geçersiz CO değeri:', value);
                return;
            }

            // Değerleri güncelle
            const coValue = document.getElementById('co-value');
            const coProgress = document.getElementById('co-progress');
            const coLastRead = document.getElementById('co-last-read');
            const coTableValue = document.getElementById('co-table-value');
            const coStatus = document.getElementById('co-status');

            if (coValue) coValue.textContent = `${value.toFixed(1)} ppm`;
            if (coProgress) {
                // CO için 0-1000 ppm aralığında progress bar
                const progressPercentage = Math.min((value / 1000) * 100, 100);
                coProgress.style.width = `${progressPercentage}%`;
                coProgress.setAttribute('aria-valuenow', value);
            }
            if (coLastRead && timestamp) {
                const date = new Date(timestamp * 1000);
                coLastRead.textContent = date.toLocaleString();
            }
            if (coTableValue) coTableValue.textContent = `${value.toFixed(1)} ppm`;
            if (coStatus) {
                // Threshold değerini kullan
                const threshold = window.thresholds?.co || 167;
                const threshold75 = threshold * 0.75; // %75 seviyesi
                
                if (value <= threshold75) {
                    coStatus.className = 'badge bg-success-subtle text-success';
                    coStatus.textContent = 'Normal';
                } else if (value <= threshold) {
                    coStatus.className = 'badge bg-warning-subtle text-warning';
                    coStatus.textContent = 'Uyarı';
                } else {
                    coStatus.className = 'badge bg-danger-subtle text-danger';
                    coStatus.textContent = 'Tehlike';
                }
                
                // Eşik değerini göster
                const coThresholdElement = document.getElementById('co-threshold');
                if (coThresholdElement) {
                    coThresholdElement.textContent = `Eşik: ${threshold} ppm`;
                }
            }

            updateLastUpdateTime();
        } catch (error) {
            console.error('CO değeri güncellenirken hata:', error);
        }
    }

    // Sıcaklık değerini güncelle
    function updateTemperatureValue(value, timestamp) {
        try {
            const tempValueElement = document.getElementById('temp-value');
            const tempTableValueElement = document.getElementById('temp-table-value');
            const tempProgressElement = document.getElementById('temp-progress');
            const tempStatusElement = document.getElementById('temp-status');
            const tempLastReadElement = document.getElementById('temp-last-read');

            if (tempValueElement) tempValueElement.textContent = `${value}°C`;
            if (tempTableValueElement) tempTableValueElement.textContent = `${value}°C`;

            if (tempProgressElement) {
                // Sıcaklık için 0-100°C aralığında progress bar
                const progressPercentage = Math.min((value / 100) * 100, 100);
                tempProgressElement.style.width = `${progressPercentage}%`;
            }

            if (tempStatusElement) {
                // Threshold değerini kullan
                const threshold = window.thresholds?.temperature || 36;
                const threshold75 = threshold * 0.75; // %75 seviyesi
                
                if (value <= threshold75) {
                    tempStatusElement.className = 'badge bg-success-subtle text-success';
                    tempStatusElement.textContent = 'Normal';
                } else if (value <= threshold) {
                    tempStatusElement.className = 'badge bg-warning-subtle text-warning';
                    tempStatusElement.textContent = 'Sıcak';
                } else {
                    tempStatusElement.className = 'badge bg-danger-subtle text-danger';
                    tempStatusElement.textContent = 'Çok Sıcak';
                }
                
                // Eşik değerini göster
                const tempThresholdElement = document.getElementById('temp-threshold');
                if (tempThresholdElement) {
                    tempThresholdElement.textContent = `Eşik: ${threshold}°C`;
                }
            }

            if (tempLastReadElement && timestamp) {
                const date = new Date(timestamp * 1000);
                tempLastReadElement.textContent = date.toLocaleString();
            }

            updateLastUpdateTime();
        } catch (error) {
            log(`Sıcaklık değeri güncelleme hatası: ${error.message}`, 'error');
        }
    }

    // Nem değerini güncelle
    function updateHumidityValue(value, timestamp) {
        try {
            const humValueElement = document.getElementById('hum-value');
            const humTableValueElement = document.getElementById('hum-table-value');
            const humProgressElement = document.getElementById('hum-progress');
            const humStatusElement = document.getElementById('hum-status');
            const humLastReadElement = document.getElementById('hum-last-read');

            if (humValueElement) humValueElement.textContent = `${value}%`;
            if (humTableValueElement) humTableValueElement.textContent = `${value}%`;

            if (humProgressElement) {
                // Nem için 0-100% aralığında progress bar
                const progressPercentage = Math.min(value, 100);
                humProgressElement.style.width = `${progressPercentage}%`;
            }

            if (humStatusElement) {
                // Threshold değerini kullan
                const threshold = window.thresholds?.humidity || 71;
                const threshold75 = threshold * 0.75; // %75 seviyesi
                
                if (value <= threshold75) {
                    humStatusElement.className = 'badge bg-success-subtle text-success';
                    humStatusElement.textContent = 'Normal';
                } else if (value <= threshold) {
                    humStatusElement.className = 'badge bg-warning-subtle text-warning';
                    humStatusElement.textContent = 'Yüksek';
                } else {
                    humStatusElement.className = 'badge bg-info-subtle text-info';
                    humStatusElement.textContent = 'Çok Yüksek';
                }
                
                // Eşik değerini göster
                const humThresholdElement = document.getElementById('hum-threshold');
                if (humThresholdElement) {
                    humThresholdElement.textContent = `Eşik: ${threshold}%`;
                }
            }

            if (humLastReadElement && timestamp) {
                const date = new Date(timestamp * 1000);
                humLastReadElement.textContent = date.toLocaleString();
            }

            updateLastUpdateTime();
        } catch (error) {
            log(`Nem değeri güncelleme hatası: ${error.message}`, 'error');
        }
    }

    // Alarm durumlarını güncelle
    function updateAlarmStatus(outputs) {
        try {
            console.log('Alarm durumları güncelleniyor:', outputs);

            // Buzzer durumu
            const buzzerStatus = outputs.buzzer !== undefined ? outputs.buzzer :
                               outputs.buzzer_status !== undefined ? outputs.buzzer_status : null;
            if (buzzerStatus !== null) {
                const buzzerElement = document.getElementById('buzzerStatus');
                if (buzzerElement) {
                    buzzerElement.className = `badge ${getStatusClass(buzzerStatus)}`;
                    buzzerElement.textContent = getStatusText(buzzerStatus);
                }
            }

            // LED durumu
            const ledStatus = outputs.led !== undefined ? outputs.led :
                            outputs.led_status !== undefined ? outputs.led_status : null;
            if (ledStatus !== null) {
                const ledElement = document.getElementById('ledStatus');
                if (ledElement) {
                    ledElement.className = `badge ${getStatusClass(ledStatus)}`;
                    ledElement.textContent = getStatusText(ledStatus);
                }
            }

            // Servo durumu
            const servoStatus = outputs.servo !== undefined ? outputs.servo :
                              outputs.servo_status !== undefined ? outputs.servo_status : null;
            if (servoStatus !== null) {
                const servoElement = document.getElementById('servoStatus');
                if (servoElement) {
                    servoElement.className = `badge ${getStatusClass(servoStatus)}`;
                    servoElement.textContent = getStatusText(servoStatus);
                }
            }

            // Sistem durumu
            const systemStatus = outputs.system !== undefined ? outputs.system :
                               outputs.system_status !== undefined ? outputs.system_status :
                               outputs.status !== undefined ? outputs.status : null;
            if (systemStatus !== null) {
                const systemElement = document.getElementById('systemStatus');
                if (systemElement) {
                    systemElement.className = `badge ${getStatusClass(systemStatus)}`;
                    systemElement.textContent = getStatusText(systemStatus);
                }
            }
        } catch (error) {
            console.error('Alarm durumları güncellenirken hata:', error);
        }
    }

    // Durum metnini al
    function getStatusText(status) {
        if (typeof status === 'boolean') {
            return status ? 'Aktif' : 'Pasif';
        }
        if (typeof status === 'number') {
            return status === 1 ? 'Aktif' : 'Pasif';
        }
        if (typeof status === 'string') {
            const lowerStatus = status.toLowerCase();
            if (lowerStatus === 'active' || lowerStatus === 'aktif' || lowerStatus === '1') {
                return 'Aktif';
            }
            if (lowerStatus === 'passive' || lowerStatus === 'pasif' || lowerStatus === '0') {
                return 'Pasif';
            }
        }
        return 'Bilinmiyor';
    }

    // Durum sınıfını al
    function getStatusClass(status) {
        if (typeof status === 'boolean') {
            return status ? 'bg-success' : 'bg-danger';
        }
        if (typeof status === 'number') {
            return status === 1 ? 'bg-success' : 'bg-danger';
        }
        if (typeof status === 'string') {
            const lowerStatus = status.toLowerCase();
            if (lowerStatus === 'active' || lowerStatus === 'aktif' || lowerStatus === '1') {
                return 'bg-success';
            }
            if (lowerStatus === 'passive' || lowerStatus === 'pasif' || lowerStatus === '0') {
                return 'bg-danger';
            }
        }
        return 'bg-secondary';
    }

    // Alarm loglarını güncelle
    function updateAlarmLogs(alarms) {
        try {
            const alarmTableBody = document.getElementById('alarm-table-body');
            if (!alarmTableBody) {
                console.warn('Alarm tablosu bulunamadı');
                return;
            }

            // Tabloyu temizle
            alarmTableBody.innerHTML = '';
            
            // Firebase'den gelen veriyi diziye dönüştür
            let alarmsArray = [];
            
            if (alarms && typeof alarms === 'object' && !Array.isArray(alarms)) {
                // Nesne olarak gelen alarmları diziye dönüştür
                Object.entries(alarms).forEach(([key, value]) => {
                    // Her bir alarm kaydını işle
                    let alarmData = null;
                    
                    // İlk durum: doğrudan alarm verileri mevcut
                    if (value.alarm_description && value.alarm_type) {
                        alarmData = {
                            id: key,
                            alarm_type: value.alarm_type || 'Bilinmiyor',
                            alarm_description: value.alarm_description || '',
                            value: value.value || 0,
                            threshold: value.threshold || 0,
                            created_at: value.created_at || 0,
                            // Eğer created_at milisaniye cinsindeyse saniyeye çevir
                            // (1970'den bugüne milisaniye 13 haneli, saniye 10 haneli olur)
                            status: value.alarm_type || 'Bilinmiyor',
                            sensor_id: value.sensor_id || ''
                        };
                        
                        // created_at milisaniye formatındaysa saniyeye çevir
                        if (alarmData.created_at > 9999999999) {
                            alarmData.created_at = Math.floor(alarmData.created_at / 1000);
                        }
                    }
                    // İkinci durum: karbonmonoksit_alarm, sicaklik_alarm veya nem_alarm yapısı
                    else if (value.karbonmonoksit_alarm) {
                        const sensorData = value.karbonmonoksit_alarm;
                        alarmData = {
                            id: key,
                            alarm_type: sensorData.alarm_type || 'Karbonmonoksit',
                            alarm_description: sensorData.alarm_description || '',
                            value: sensorData.value || 0,
                            threshold: sensorData.threshold || 0,
                            created_at: sensorData.created_at || 0,
                            status: sensorData.alarm_type || 'Bilinmiyor',
                            sensor_id: sensorData.sensor_id || 'sensor_type_3'
                        };
                    }
                    else if (value.sicaklik_alarm) {
                        const sensorData = value.sicaklik_alarm;
                        alarmData = {
                            id: key,
                            alarm_type: sensorData.alarm_type || 'Sıcaklık',
                            alarm_description: sensorData.alarm_description || '',
                            value: sensorData.value || 0,
                            threshold: sensorData.threshold || 0,
                            created_at: sensorData.created_at || 0,
                            status: sensorData.alarm_type || 'Bilinmiyor',
                            sensor_id: sensorData.sensor_id || 'sensor_type_1'
                        };
                    }
                    else if (value.nem_alarm) {
                        const sensorData = value.nem_alarm;
                        alarmData = {
                            id: key,
                            alarm_type: sensorData.alarm_type || 'Nem',
                            alarm_description: sensorData.alarm_description || '',
                            value: sensorData.value || 0,
                            threshold: sensorData.threshold || 0,
                            created_at: sensorData.created_at || 0,
                            status: sensorData.alarm_type || 'Bilinmiyor',
                            sensor_id: sensorData.sensor_id || 'sensor_type_2'
                        };
                    }
                    
                    // Eğer alarm verisi başarıyla oluşturulduysa diziye ekle
                    if (alarmData) {
                        alarmsArray.push(alarmData);
                    }
                });
            }
            
            // Eğer hiç alarm yoksa
            if (alarmsArray.length === 0) {
                alarmTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Hiç alarm kaydı bulunamadı</td></tr>';
                return;
            }
            
            // Son 10 alarmı göster - created_at'e göre sırala
            alarmsArray.sort((a, b) => b.created_at - a.created_at);
            const recentAlarms = alarmsArray.slice(0, 10);
            
            // Son alarmın ID'sini bul ve geri giderek ID'leri ata
            let lastId = 1440 + recentAlarms.length;
            
            // Her alarmı tabloya ekle
            recentAlarms.forEach((alarm, index) => {
                // Bu alarm için ID belirle
                const displayId = lastId - index;
                
                // Tarih işleme
                const timestamp = alarm.created_at || 0;
                let date = new Date();
                
                // Doğru timestamp değerini kullan
                try {
                    // Saniye/milisaniye formatı kontrolü
                    if (timestamp > 0) {
                        date = new Date(timestamp * 1000);
                    }
                } catch (e) {
                    console.error('Tarih dönüştürme hatası:', e);
                }
                
                // Tarih formatla
                const formattedDate = `${String(date.getDate()).padStart(2, '0')}.${String(date.getMonth() + 1).padStart(2, '0')}.${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')}`;
                
                // Alarm tipi ve badge renkleri
                let alarmType = alarm.alarm_type || 'Bilinmiyor';
                let alarmTypeClass = 'bg-secondary';
                
                // Sensör tipine ve alarm tipine göre badge rengi belirle
                if (alarm.sensor_id === 'sensor_type_3' || alarmType.toLowerCase().includes('karbon') || alarmType.toLowerCase().includes('co')) {
                    alarmTypeClass = 'bg-warning text-dark';
                } else if (alarm.sensor_id === 'sensor_type_1' || alarmType.toLowerCase().includes('sıcak')) {
                    alarmTypeClass = 'bg-danger';
                } else if (alarm.sensor_id === 'sensor_type_2' || alarmType.toLowerCase().includes('nem')) {
                    alarmTypeClass = 'bg-info';
                }
                
                // Alarm tipine göre farklı renkler
                if (alarmType.toLowerCase() === 'normal') {
                    alarmTypeClass = 'bg-success';
                } else if (alarmType.toLowerCase() === 'tehlike') {
                    alarmTypeClass = 'bg-danger';
                } else if (alarmType.toLowerCase().includes('manuel')) {
                    alarmTypeClass = 'bg-primary';
                }
                
                // Açıklama
                const description = alarm.alarm_description || 'Açıklama yok';
                
                // Değer ve birim
                let value = '-';
                if (alarm.value !== undefined && alarm.value !== null && alarm.value !== 0) {
                    value = parseFloat(alarm.value).toFixed(1);
                }
                
                // Sensör tipine göre birim belirle
                let unit = '';
                if (alarm.sensor_id === 'sensor_type_3' || alarmType.toLowerCase().includes('karbon') || alarmType.toLowerCase().includes('co')) {
                    unit = 'ppm';
                } else if (alarm.sensor_id === 'sensor_type_1' || alarmType.toLowerCase().includes('sıcak')) {
                    unit = '°C';
                } else if (alarm.sensor_id === 'sensor_type_2' || alarmType.toLowerCase().includes('nem')) {
                    unit = '%';
                }
                
                // Eşik değeri
                let threshold = '-';
                if (alarm.threshold !== undefined && alarm.threshold !== null && alarm.threshold !== 0) {
                    threshold = parseFloat(alarm.threshold).toFixed(1);
                }
                
                // Durum
                let statusText = alarm.status || 'Bilinmiyor';
                let statusClass = 'bg-secondary';
                
                // Manuel kontroller için durum belirleme
                if (alarm.alarm_description && (
                    alarm.alarm_description.includes('manuel olarak') || 
                    alarm.alarm_description.includes('manuel alarm')
                )) {
                    if (alarm.alarm_description.includes('açıldı') || 
                        alarm.alarm_description.includes('aktif')) {
                        statusText = 'Aktif';
                        statusClass = 'bg-success';
                    } else if (alarm.alarm_description.includes('kapatıldı') || 
                               alarm.alarm_description.includes('pasif')) {
                        statusText = 'Pasif';
                        statusClass = 'bg-danger';
                    }
                } 
                // Sensör alarmları için durum belirleme
                else {
                    if (statusText.toLowerCase() === 'normal') {
                        statusClass = 'bg-success';
                    } else if (statusText.toLowerCase() === 'tehlike' || 
                               statusText.toLowerCase() === 'uyarı') {
                        statusClass = 'bg-danger';
                    } else if (statusText.toLowerCase() === 'aktif') {
                        statusClass = 'bg-success';
                    } else if (statusText.toLowerCase() === 'pasif') {
                        statusClass = 'bg-danger';
                    }
                }
                
                // Yeni satır oluştur
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${displayId}</td>
                    <td>${formattedDate}</td>
                    <td><span class="badge ${alarmTypeClass}">${alarmType}</span></td>
                    <td>${description}</td>
                    <td>${value} ${unit}</td>
                    <td>${threshold} ${unit}</td>
                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                `;
                
                alarmTableBody.appendChild(row);
            });
            
        } catch (error) {
            console.error('Alarm logları güncellenirken hata:', error);
            const alarmTableBody = document.getElementById('alarm-table-body');
            if (alarmTableBody) {
                alarmTableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Alarmlar yüklenirken bir hata oluştu</td></tr>';
            }
        }
    }

    // Birim getir
    function getUnit(type) {
        const lowerType = type.toLowerCase();
        if (lowerType.includes('co') || lowerType.includes('karbon')) {
            return 'ppm';
        }
        if (lowerType.includes('temp') || lowerType.includes('sıcaklık')) {
            return '°C';
        }
        if (lowerType.includes('hum') || lowerType.includes('nem')) {
            return '%';
        }
        return '';
    }

    // Son güncelleme zamanını güncelle
    function updateLastUpdateTime() {
        const lastUpdateElement = document.getElementById('last-update');
        if (lastUpdateElement) {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            lastUpdateElement.textContent = timeString;
        }
    }

    // Eşik değerlerini görsel olarak güncelle
    function updateThresholdDisplays() {
        try {
            // CO eşik değerini göster
            const coThresholdElement = document.getElementById('co-threshold');
            if (coThresholdElement && window.thresholds) {
                coThresholdElement.textContent = `Eşik: ${window.thresholds.co} ppm`;
            }
            
            // Sıcaklık eşik değerini göster
            const tempThresholdElement = document.getElementById('temp-threshold');
            if (tempThresholdElement && window.thresholds) {
                tempThresholdElement.textContent = `Eşik: ${window.thresholds.temperature}°C`;
            }
            
            // Nem eşik değerini göster
            const humThresholdElement = document.getElementById('hum-threshold');
            if (humThresholdElement && window.thresholds) {
                humThresholdElement.textContent = `Eşik: ${window.thresholds.humidity}%`;
            }
            
            // Eğer sensör verileri zaten yüklenmişse, durumları da güncelle
            const coValue = document.getElementById('co-value');
            const tempValue = document.getElementById('temp-value');
            const humValue = document.getElementById('hum-value');
            
            if (coValue) {
                const value = parseFloat(coValue.textContent);
                if (!isNaN(value)) {
                    updateCOValue(value, null);
                }
            }
            
            if (tempValue) {
                const value = parseFloat(tempValue.textContent);
                if (!isNaN(value)) {
                    updateTemperatureValue(value, null);
                }
            }
            
            if (humValue) {
                const value = parseFloat(humValue.textContent);
                if (!isNaN(value)) {
                    updateHumidityValue(value, null);
                }
            }
            
            console.log('Eşik değerleri görsel olarak güncellendi');
        } catch (error) {
            console.error('Eşik değerleri güncellenirken hata:', error);
        }
    }

    // Telefon titreşimi durumunu güncelle
    function updatePhoneVibrateStatus(isActive) {
        try {
            const phoneVibrateStatusElement = document.getElementById('phone-vibrate-status');
            if (phoneVibrateStatusElement) {
                if (isActive) {
                    phoneVibrateStatusElement.textContent = 'Aktif';
                    phoneVibrateStatusElement.classList.remove('bg-secondary-subtle', 'text-secondary');
                    phoneVibrateStatusElement.classList.add('bg-info-subtle', 'text-info');
                    
                    // Aktif olduğunu belirtmek için animasyon ekle
                    phoneVibrateStatusElement.style.animation = 'pulse 1.5s infinite';
                } else {
                    phoneVibrateStatusElement.textContent = 'Devre Dışı';
                    phoneVibrateStatusElement.classList.remove('bg-info-subtle', 'text-info');
                    phoneVibrateStatusElement.classList.add('bg-secondary-subtle', 'text-secondary');
                    
                    // Animasyonu kaldır
                    phoneVibrateStatusElement.style.animation = '';
                }
                
                // Son güncelleme zamanını da güncelle
                updateLastUpdateTime();
            }
        } catch (error) {
            console.error('Telefon titreşim durumu güncellenirken hata:', error);
        }
    }

    // Sistem kontrol durumunu güncelle
    function updateSystemModeStatus(mechanicalIntervention, userIntervention) {
        try {
            const systemModeStatusElement = document.getElementById('system-mode-status');
            if (systemModeStatusElement) {
                if (mechanicalIntervention) {
                    systemModeStatusElement.textContent = 'Otomatik';
                    systemModeStatusElement.classList.remove('bg-secondary-subtle', 'text-secondary');
                    systemModeStatusElement.classList.add('bg-primary-subtle', 'text-primary');
                } else if (userIntervention) {
                    systemModeStatusElement.textContent = 'Kullanıcı Kontrolü';
                    systemModeStatusElement.classList.remove('bg-primary-subtle', 'text-primary', 'bg-secondary-subtle', 'text-secondary');
                    systemModeStatusElement.classList.add('bg-warning-subtle', 'text-warning');
                } else {
                    systemModeStatusElement.textContent = 'Tanımsız';
                    systemModeStatusElement.classList.remove('bg-primary-subtle', 'text-primary', 'bg-warning-subtle', 'text-warning');
                    systemModeStatusElement.classList.add('bg-secondary-subtle', 'text-secondary');
                }
                // Son güncelleme zamanını da güncelle
                updateLastUpdateTime();
            }
        } catch (error) {
            console.error('Sistem kontrol durumu güncellenirken hata:', error);
        }
    }

    // Sessiz mod durumunu güncelle
    function updateSilentModeStatus(silentMode) {
        try {
            const silentModeStatusElement = document.getElementById('silent-mode-status');
            if (silentModeStatusElement) {
                if (silentMode) {
                    silentModeStatusElement.textContent = 'Aktif';
                    silentModeStatusElement.classList.remove('bg-secondary-subtle', 'text-secondary');
                    silentModeStatusElement.classList.add('bg-primary-subtle', 'text-primary');
                } else {
                    silentModeStatusElement.textContent = 'Devre Dışı';
                    silentModeStatusElement.classList.remove('bg-primary-subtle', 'text-primary');
                    silentModeStatusElement.classList.add('bg-secondary-subtle', 'text-secondary');
                }
                // Son güncelleme zamanını da güncelle
                updateLastUpdateTime();
            }
        } catch (error) {
            console.error('Sessiz mod durumu güncellenirken hata:', error);
        }
    }

    // Sayfa yüklendiğinde
    document.addEventListener('DOMContentLoaded', () => {
        try {
            log('Sayfa yüklendi, Firebase başlatılıyor...', 'info');
            if (initializeFirebase()) {
                listenToSensorData();
            } else {
                log('Firebase başlatılamadığı için sensör verileri dinlenemiyor', 'error');
            }
        } catch (error) {
            console.error('Sayfa yüklenirken hata:', error);
        }
    });
    </script>
    @endpush
@endsection

