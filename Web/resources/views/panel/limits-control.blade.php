@extends('panel.layouts.app')
@section('title', 'Limit Kontrolü')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-sliders-h me-2"></i> Eşik Değerleri</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Sıcaklık Sensörü -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-thermometer-half me-2"></i> Sıcaklık Sensörü</h5>
                                    <span class="badge bg-light text-danger" id="tempLimitBadge">35 °C</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="sensor-icon mb-4">
                                        <i class="fas fa-thermometer-full fa-4x text-danger"></i>
                                    </div>
                                    <div class="sensor-info text-center mb-4">
                                        <h6 class="text-muted">Mevcut Limit Değeri</h6>
                                        <h3 class="fw-bold" id="tempLimitValue">35 °C</h3>
                                    </div>
                                    <div class="sensor-control w-100">
                                        <div class="mb-4">
                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label class="form-label fw-bold mb-0">Limit Değeri</label>
                                                <div class="limit-value-display fw-bold fs-5" id="temperature_value_display">35 °C</div>
                                            </div>
                                            <div class="modern-slider-container">
                                                <div class="modern-slider-track">
                                                    <div class="modern-slider-fill" id="temperature_slider_fill" style="width: 35%;"></div>
                                                </div>
                                                <input type="range" class="modern-slider" id="limit_slider_temperature" 
                                                    min="0" max="100" step="1" value="35"
                                                    oninput="updateModernSlider('temperature', this.value)">
                                                <div class="slider-markers">
                                                    <span>0</span>
                                                    <span>25</span>
                                                    <span>50</span>
                                                    <span>75</span>
                                                    <span>100 °C</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-danger btn-lg w-100" onclick="updateTempLimit()">
                                            <i class="fas fa-save me-2"></i> Güncelle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nem Sensörü -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-tint me-2"></i> Nem Sensörü</h5>
                                    <span class="badge bg-light text-info" id="humidityLimitBadge">60 g/m³</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="sensor-icon mb-4">
                                        <i class="fas fa-tint fa-4x text-info"></i>
                                    </div>
                                    <div class="sensor-info text-center mb-4">
                                        <h6 class="text-muted">Mevcut Limit Değeri</h6>
                                        <h3 class="fw-bold" id="humidityLimitValue">60 g/m³</h3>
                                    </div>
                                    <div class="sensor-control w-100">
                                        <div class="mb-4">
                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label class="form-label fw-bold mb-0">Limit Değeri</label>
                                                <div class="limit-value-display fw-bold fs-5" id="humidity_value_display">60 g/m³</div>
                                            </div>
                                            <div class="modern-slider-container">
                                                <div class="modern-slider-track">
                                                    <div class="modern-slider-fill" id="humidity_slider_fill" style="width: 60%;"></div>
                                                </div>
                                                <input type="range" class="modern-slider" id="limit_slider_humidity" 
                                                    min="0" max="100" step="1" value="60"
                                                    oninput="updateModernSlider('humidity', this.value)">
                                                <div class="slider-markers">
                                                    <span>0</span>
                                                    <span>25</span>
                                                    <span>50</span>
                                                    <span>75</span>
                                                    <span>100 g/m³</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-info text-white btn-lg w-100" onclick="updateHumidityLimit()">
                                            <i class="fas fa-save me-2"></i> Güncelle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Karbonmonoksit Sensörü -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-smog me-2"></i> Karbonmonoksit Sensörü</h5>
                                    <span class="badge bg-light text-warning" id="coLimitBadge">174 ppm</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="sensor-icon mb-4">
                                        <i class="fas fa-smog fa-4x text-warning"></i>
                                    </div>
                                    <div class="sensor-info text-center mb-4">
                                        <h6 class="text-muted">Mevcut Limit Değeri</h6>
                                        <h3 class="fw-bold" id="coLimitValue">174 ppm</h3>
                                    </div>
                                    <div class="sensor-control w-100">
                                        <div class="mb-4">
                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <label class="form-label fw-bold mb-0">Limit Değeri</label>
                                                <div class="limit-value-display fw-bold fs-5" id="co_value_display">174 ppm</div>
                                            </div>
                                            <div class="modern-slider-container">
                                                <div class="modern-slider-track">
                                                    <div class="modern-slider-fill" id="co_slider_fill" style="width: 17.4%;"></div>
                                                </div>
                                                <input type="range" class="modern-slider" id="limit_slider_co" 
                                                    min="0" max="1000" step="1" value="174"
                                                    oninput="updateModernSlider('co', this.value)">
                                                <div class="slider-markers">
                                                    <span>0</span>
                                                    <span>250</span>
                                                    <span>500</span>
                                                    <span>750</span>
                                                    <span>1000 ppm</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-warning text-dark btn-lg w-100" onclick="updateCOLimit()">
                                            <i class="fas fa-save me-2"></i> Güncelle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sensor-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(145deg, #f0f0f0, #ffffff);
    box-shadow: 8px 8px 16px #d1d1d1, -8px -8px 16px #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.sensor-icon:hover {
    transform: scale(1.1);
}

.sensor-info h3 {
    font-size: 2.5rem;
    margin: 0;
    transition: all 0.3s ease;
}

.sensor-control .input-group {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

.sensor-control .form-control {
    border: none;
    padding: 15px;
    font-size: 1.1rem;
}

.sensor-control .input-group-text {
    background-color: #f8f9fa;
    border: none;
    padding: 0 15px;
    font-weight: bold;
}

.sensor-control .btn {
    border-radius: 10px;
    padding: 12px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.sensor-control .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.card {
    transition: all 0.3s ease;
    border-radius: 15px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.badge {
    font-size: 0.9rem;
    padding: 8px 12px;
    border-radius: 50px;
}

/* Toast bildirimi */
.toast-container {
    z-index: 9999;
}

.toast {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Slider için özel stiller */
.modern-slider-container {
    position: relative;
    padding: 15px 0;
    margin-bottom: 15px;
}

.modern-slider-track {
    position: relative;
    width: 100%;
    height: 10px;
    background-color: #e9ecef;
    border-radius: 10px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}

.modern-slider-fill {
    position: absolute;
    height: 100%;
    left: 0;
    top: 0;
    border-radius: 10px;
    transition: width 0.2s ease;
}

#temperature_slider_fill {
    background: linear-gradient(90deg, #ff7e5f, #e74c3c);
    box-shadow: 0 2px 5px rgba(231, 76, 60, 0.3);
}

#humidity_slider_fill {
    background: linear-gradient(90deg, #36d1dc, #3498db);
    box-shadow: 0 2px 5px rgba(52, 152, 219, 0.3);
}

#co_slider_fill {
    background: linear-gradient(90deg, #f7b733, #9b59b6);
    box-shadow: 0 2px 5px rgba(155, 89, 182, 0.3);
}

.modern-slider {
    -webkit-appearance: none;
    position: absolute;
    width: 100%;
    height: 10px;
    left: 0;
    top: 15px;
    margin: 0;
    background: transparent;
    z-index: 10;
}

.modern-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #fff;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
}

.modern-slider::-webkit-slider-thumb:hover {
    transform: scale(1.2);
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.modern-slider:focus {
    outline: none;
}

.slider-markers {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    color: #868e96;
    font-size: 0.8rem;
    padding: 0 5px;
}

/* Sensör tipleri için özel renkler */
#limit_slider_temperature::-webkit-slider-thumb {
    background: #fff url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="%23e74c3c"><path d="M160 64c0-17.7 14.3-32 32-32s32 14.3 32 32v144h32c17.7 0 32 14.3 32 32s-14.3 32-32 32H192c-17.7 0-32-14.3-32-32V64zM160 352c0-17.7 14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32s-32-14.3-32-32zm176-32c17.7 0 32 14.3 32 32s-14.3 32-32 32s-32-14.3-32-32s14.3-32 32-32z"/></svg>') no-repeat center center / 10px;
    border: 2px solid #e74c3c;
}

#limit_slider_humidity::-webkit-slider-thumb {
    background: #fff url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="%233498db"><path d="M192 512C86 512 0 426 0 320C0 228.8 130.2 57.7 166.6 11.7C172.6 4.2 181.5 0 191.1 0h1.8c9.6 0 18.5 4.2 24.5 11.7C253.8 57.7 384 228.8 384 320c0 106-86 192-192 192zM96 336c0-8.8-7.2-16-16-16s-16 7.2-16 16c0 61.9 50.1 112 112 112c8.8 0 16-7.2 16-16s-7.2-16-16-16c-44.2 0-80-35.8-80-80z"/></svg>') no-repeat center center / 10px;
    border: 2px solid #3498db;
}

#limit_slider_co::-webkit-slider-thumb {
    background: #fff url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="%239b59b6"><path d="M32 144c0 79.5 64.5 144 144 144H299.3c22.6 19.9 52.2 32 84.7 32s62.1-12.1 84.7-32H496c61.9 0 112-50.1 112-112s-50.1-112-112-112c-10.7 0-21 1.5-30.8 4.3C443.8 27.7 401.1 0 352 0c-32.8 0-62.6 12.1-85.4 32H176C96.5 32 32 96.5 32 176v144 32C14.3 352 0 337.7 0 320s14.3-32 32-32V144z"/></svg>') no-repeat center center / 12px;
    border: 2px solid #9b59b6;
}

.limit-value-display {
    color: #495057;
    background-color: #f8f9fa;
    padding: 0.3rem 0.75rem;
    border-radius: 50px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    border: 1px solid #dee2e6;
    min-width: 100px;
    text-align: center;
    transition: all 0.3s ease;
}
</style>

@push('scripts')
<script>

// Firebase referanslarını al
const deviceRef = firebase.database().ref('device_sensors');

// Sensör tipine göre ölçü birimini döndür
function getSensorUnit(sensorType) {
    switch(sensorType) {
        case 'temperature':
            return '°C';
        case 'humidity':
            return 'g/m³';
        case 'co':
            return 'ppm';
        default:
            return '';
    }
}

// Mevcut limitleri yükle
function loadCurrentLimits() {
    deviceRef.once('value')
        .then((snapshot) => {
            const data = snapshot.val();
            if (data) {
                // Sıcaklık sensörü
                if (data.sensor_type_1 && data.sensor_type_1.limits) {
                    const tempSlider = document.getElementById('limit_slider_temperature');
                    if (tempSlider) {
                        const limitValue = parseInt(data.sensor_type_1.limits);
                        tempSlider.value = limitValue;
                        updateModernSlider('temperature', limitValue);
                    }
                }

                // Nem sensörü
                if (data.sensor_type_2 && data.sensor_type_2.limits) {
                    const humSlider = document.getElementById('limit_slider_humidity');
                    if (humSlider) {
                        const limitValue = parseInt(data.sensor_type_2.limits);
                        humSlider.value = limitValue;
                        updateModernSlider('humidity', limitValue);
                    }
                }

                // CO sensörü
                if (data.sensor_type_3 && data.sensor_type_3.limits) {
                    const coSlider = document.getElementById('limit_slider_co');
                    if (coSlider) {
                        const limitValue = parseInt(data.sensor_type_3.limits);
                        coSlider.value = limitValue;
                        updateModernSlider('co', limitValue);
                    }
                }
            }
        })
        .catch((error) => {
            console.error("Mevcut limit değerleri yüklenirken hata:", error);
            showToast('Limit değerleri yüklenirken bir hata oluştu', 'danger');
        });
}

// Sayfa yüklendiğinde limitleri yükle ve dinleyicileri başlat
document.addEventListener('DOMContentLoaded', function() {
    loadCurrentLimits();
    
    // Firebase veritabanını dinle ve değişiklikleri anlık olarak güncelle
    startRealtimeListeners();
});

// Firebase değişikliklerini dinle
function startRealtimeListeners() {
    // Sıcaklık sensörü değişikliklerini dinle
    deviceRef.child('sensor_type_1').on('value', (snapshot) => {
        const data = snapshot.val();
        if (data && data.limits) {
            const limitValue = parseInt(data.limits);
            updateSilently('temperature', limitValue);
        }
    });

    // Nem sensörü değişikliklerini dinle
    deviceRef.child('sensor_type_2').on('value', (snapshot) => {
        const data = snapshot.val();
        if (data && data.limits) {
            const limitValue = parseInt(data.limits);
            updateSilently('humidity', limitValue);
        }
    });

    // CO sensörü değişikliklerini dinle
    deviceRef.child('sensor_type_3').on('value', (snapshot) => {
        const data = snapshot.val();
        if (data && data.limits) {
            const limitValue = parseInt(data.limits);
            updateSilently('co', limitValue);
        }
    });
    
    console.log('Firebase değişiklikleri dinleniyor...');
}

// Sessizce değeri güncelle (bildirim göstermeden)
function updateSilently(sensorType, value) {
    // Mevcut değeri kontrol et
    const slider = document.getElementById(`limit_slider_${sensorType}`);
    
    if (slider && parseInt(slider.value) !== parseInt(value)) {
        console.log(`${sensorType} sensörü limit değeri uzaktan güncellendi: ${value}`);
        
        // Slider değerini güncelle
        slider.value = value;
        
        // Diğer UI elemanlarını güncelle, bildirim göstermeden
        updateModernSlider(sensorType, value);
    }
}

// Limit değeri görüntüsünü güncelle
function updateLimitDisplay(type, limit, unit) {
    const limitValue = document.getElementById(`${type}LimitValue`);
    const limitBadge = document.getElementById(`${type}LimitBadge`);
    const limitInput = document.getElementById(`${type}LimitInput`);
    
    if (limitValue) limitValue.textContent = `${limit} ${unit}`;
    if (limitBadge) limitBadge.textContent = `${limit} ${unit}`;
    if (limitInput) limitInput.value = limit;
}

// Sıcaklık sensörü limit değerini güncelle
function updateTempLimit() {
    const limitInput = document.getElementById('limit_value_temperature');
    const newLimit = limitInput.value;
    
    if (!newLimit || isNaN(newLimit) || newLimit < 0) {
        showToast('Lütfen geçerli bir limit değeri girin', 'warning');
        return;
    }
    
    // Loading göster
    showUpdating('temperature');
    
    // Veritabanını güncelle (tam sayı değer olarak, string formatında)
    deviceRef.child('sensor_type_1').update({
        limits: String(parseInt(newLimit)),
        updated_at: firebase.database.ServerValue.TIMESTAMP
    })
    .then(() => {
        showToast('Sıcaklık limit değeri başarıyla güncellendi', 'success');
        showUpdated('temperature');
        
        // Slider ve göstergeyi güncelle
        updateModernSlider('temperature', parseInt(newLimit));
    })
    .catch((error) => {
        console.error('Limit değeri güncellenirken hata:', error);
        showToast('Limit değeri güncellenirken bir hata oluştu', 'danger');
        showUpdateError('temperature');
    });
}

// Nem sensörü limit değerini güncelle
function updateHumidityLimit() {
    const limitInput = document.getElementById('limit_value_humidity');
    const newLimit = limitInput.value;
    
    if (!newLimit || isNaN(newLimit) || newLimit < 0) {
        showToast('Lütfen geçerli bir limit değeri girin', 'warning');
        return;
    }
    
    // Loading göster
    showUpdating('humidity');
    
    // Veritabanını güncelle (tam sayı değer olarak, string formatında)
    deviceRef.child('sensor_type_2').update({
        limits: String(parseInt(newLimit)),
        updated_at: firebase.database.ServerValue.TIMESTAMP
    })
    .then(() => {
        showToast('Nem limit değeri başarıyla güncellendi', 'success');
        showUpdated('humidity');
        
        // Slider ve göstergeyi güncelle
        updateModernSlider('humidity', parseInt(newLimit));
    })
    .catch((error) => {
        console.error('Limit değeri güncellenirken hata:', error);
        showToast('Limit değeri güncellenirken bir hata oluştu', 'danger');
        showUpdateError('humidity');
    });
}

// Karbonmonoksit sensörü limit değerini güncelle
function updateCOLimit() {
    const limitInput = document.getElementById('limit_value_co');
    const newLimit = limitInput.value;
    
    if (!newLimit || isNaN(newLimit) || newLimit < 0) {
        showToast('Lütfen geçerli bir limit değeri girin', 'warning');
        return;
    }
    
    // Loading göster
    showUpdating('co');
    
    // Veritabanını güncelle (tam sayı değer olarak, string formatında)
    deviceRef.child('sensor_type_3').update({
        limits: String(parseInt(newLimit)),
        updated_at: firebase.database.ServerValue.TIMESTAMP
    })
    .then(() => {
        showToast('Karbonmonoksit limit değeri başarıyla güncellendi', 'success');
        showUpdated('co');
        
        // Slider ve göstergeyi güncelle
        updateModernSlider('co', parseInt(newLimit));
    })
    .catch((error) => {
        console.error('Limit değeri güncellenirken hata:', error);
        showToast('Limit değeri güncellenirken bir hata oluştu', 'danger');
        showUpdateError('co');
    });
}

// Slider değeri değiştiğinde input değerini güncelle
function updateLimitValue(sensorType, value) {
    const limitInput = document.getElementById(`limit_value_${sensorType}`);
    if (limitInput) {
        limitInput.value = value;
        // Limit değerini gerçek zamanlı gösterge kısmında da güncelle
        updateLimitIndicator(sensorType, value);
    }
}

// Input değeri değiştiğinde slider değerini güncelle
function updateSlider(sensorType, value) {
    const slider = document.getElementById(`limit_slider_${sensorType}`);
    if (slider) {
        slider.value = value;
        // Limit değerini gerçek zamanlı gösterge kısmında da güncelle
        updateLimitIndicator(sensorType, value);
    }
}

// Limit göstergesini güncelle (varsa)
function updateLimitIndicator(sensorType, value) {
    const indicator = document.getElementById(`limit_indicator_${sensorType}`);
    if (indicator) {
        indicator.textContent = `${value} ${getSensorUnit(sensorType)}`;
    }
}

// Toast bildirimi göster
function showToast(message, type = 'info') {
    // Bootstrap toast oluştur
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.innerHTML += toastHtml;
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    // Toast kapandığında DOM'dan kaldır
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Toast container oluştur
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1050';
    document.body.appendChild(container);
    return container;
}

// Güncelleme durumunu göster
function showUpdating(sensorType) {
    let btnId;
    switch(sensorType) {
        case 'temperature':
            btnId = document.querySelector('button[onclick="updateTempLimit()"]');
            break;
        case 'humidity':
            btnId = document.querySelector('button[onclick="updateHumidityLimit()"]');
            break;
        case 'co':
            btnId = document.querySelector('button[onclick="updateCOLimit()"]');
            break;
    }
    
    if (btnId) {
        btnId.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i>Güncelleniyor...';
        btnId.disabled = true;
    }
}

// Güncelleme başarılı mesajı
function showUpdated(sensorType) {
    let btnId;
    switch(sensorType) {
        case 'temperature':
            btnId = document.querySelector('button[onclick="updateTempLimit()"]');
            break;
        case 'humidity':
            btnId = document.querySelector('button[onclick="updateHumidityLimit()"]');
            break;
        case 'co':
            btnId = document.querySelector('button[onclick="updateCOLimit()"]');
            break;
    }
    
    if (btnId) {
        btnId.innerHTML = '<i class="fas fa-check me-2"></i>Güncellendi';
        
        // 2 saniye sonra orijinal duruma dön
        setTimeout(() => {
            btnId.innerHTML = 'Limiti Güncelle';
            btnId.disabled = false;
        }, 2000);
    }
}

// Güncelleme hatası
function showUpdateError(sensorType) {
    let btnId;
    switch(sensorType) {
        case 'temperature':
            btnId = document.querySelector('button[onclick="updateTempLimit()"]');
            break;
        case 'humidity':
            btnId = document.querySelector('button[onclick="updateHumidityLimit()"]');
            break;
        case 'co':
            btnId = document.querySelector('button[onclick="updateCOLimit()"]');
            break;
    }
    
    if (btnId) {
        btnId.innerHTML = '<i class="fas fa-times me-2"></i>Hata';
        
        // 2 saniye sonra orijinal duruma dön
        setTimeout(() => {
            btnId.innerHTML = 'Limiti Güncelle';
            btnId.disabled = false;
        }, 2000);
    }
}

// Slider değeri değiştiğinde UI güncelle ve input değerini güncelle
function updateModernSlider(sensorType, value) {
    // Integer'a çevir
    value = parseInt(value);
    
    // Gizli input değerini güncelle (form gönderimi için)
    let hiddenInput = document.getElementById(`limit_value_${sensorType}`);
    if (!hiddenInput) {
        // Gizli input yok, oluşturalım
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.id = `limit_value_${sensorType}`;
        document.body.appendChild(hiddenInput);
    }
    hiddenInput.value = value;
    
    // Değer göstergesini güncelle
    const valueDisplay = document.getElementById(`${sensorType}_value_display`);
    if (valueDisplay) {
        valueDisplay.textContent = `${value} ${getSensorUnit(sensorType)}`;
        valueDisplay.style.color = getColorForSensorType(sensorType);
        
        // Değer değiştiğinde hafif animasyon
        valueDisplay.style.transform = 'scale(1.1)';
        setTimeout(() => {
            valueDisplay.style.transform = 'scale(1)';
        }, 200);
    }
    
    // Dolgu çubuğunu güncelle
    const sliderFill = document.getElementById(`${sensorType}_slider_fill`);
    if (sliderFill) {
        // Yüzde değerini hesapla (0-100 arasında)
        let percent;
        switch(sensorType) {
            case 'temperature':
                percent = (value / 100) * 100;
                break;
            case 'humidity':
                percent = (value / 100) * 100;
                break;
            case 'co':
                percent = (value / 1000) * 100;
                break;
            default:
                percent = 0;
        }
        sliderFill.style.width = `${percent}%`;
    }
    
    // Kart başlığında değeri güncelle
    const limitBadge = document.getElementById(`${getOriginalSensorId(sensorType)}LimitBadge`);
    const limitValue = document.getElementById(`${getOriginalSensorId(sensorType)}LimitValue`);
    
    if (limitBadge) limitBadge.textContent = `${value} ${getSensorUnit(sensorType)}`;
    if (limitValue) limitValue.textContent = `${value} ${getSensorUnit(sensorType)}`;
}

// Sensör tipleri için renk kodu döndür
function getColorForSensorType(sensorType) {
    switch(sensorType) {
        case 'temperature':
            return '#e74c3c';
        case 'humidity':
            return '#3498db';
        case 'co':
            return '#9b59b6';
        default:
            return '#333';
    }
}

// Sensör tipi dönüştürme (yeni tanımlayıcılar ile eski olanları eşleştirme)
function getOriginalSensorId(sensorType) {
    switch(sensorType) {
        case 'temperature':
            return 'temp';
        case 'humidity':
            return 'humidity';
        case 'co':
            return 'co';
        default:
            return sensorType;
    }
}
</script>
@endpush
@endsection 