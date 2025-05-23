@extends('panel.layouts.app')
@section('title', 'Sensör Ayarları')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-cogs me-2"></i> Sensör Ayarları</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Sıcaklık Sensörü Ayarları -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-thermometer-half me-2"></i> Sıcaklık Sensörü</h5>
                                    <span class="badge bg-light text-danger" id="tempIntervalBadge">1 dk</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="sensor-icon mb-4">
                                        <i class="fas fa-thermometer-full fa-4x text-danger"></i>
                                    </div>
                                    <div class="sensor-info text-center mb-4">
                                        <h6 class="text-muted">Mevcut Ölçüm Aralığı</h6>
                                        <h3 class="fw-bold" id="tempIntervalValue">1 dakika</h3>
                                    </div>
                                    <div class="sensor-control w-100">
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control form-control-lg" id="tempIntervalInput" placeholder="Değer" min="1" step="1">
                                            <select class="form-select form-select-lg" id="tempIntervalUnit">
                                                <option value="minute">Dakika</option>
                                                <option value="hour">Saat</option>
                                                <option value="day">Gün</option>
                                                <option value="month">Ay</option>
                                            </select>
                                        </div>
                                        <div class="converted-value text-center mb-3">
                                            <small class="text-muted">Toplam: <span id="tempConvertedValue">1</span> dakika</small>
                                        </div>
                                        <button class="btn btn-primary btn-lg w-100" onclick="updateTempInterval()">
                                            <i class="fas fa-save me-2"></i> Güncelle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nem Sensörü Ayarları -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-tint me-2"></i> Nem Sensörü</h5>
                                    <span class="badge bg-light text-info" id="humidityIntervalBadge">1 dk</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="sensor-icon mb-4">
                                        <i class="fas fa-tint fa-4x text-info"></i>
                                    </div>
                                    <div class="sensor-info text-center mb-4">
                                        <h6 class="text-muted">Mevcut Ölçüm Aralığı</h6>
                                        <h3 class="fw-bold" id="humidityIntervalValue">1 dakika</h3>
                                    </div>
                                    <div class="sensor-control w-100">
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control form-control-lg" id="humidityIntervalInput" placeholder="Değer" min="1" step="1">
                                            <select class="form-select form-select-lg" id="humidityIntervalUnit">
                                                <option value="minute">Dakika</option>
                                                <option value="hour">Saat</option>
                                                <option value="day">Gün</option>
                                                <option value="month">Ay</option>
                                            </select>
                                        </div>
                                        <div class="converted-value text-center mb-3">
                                            <small class="text-muted">Toplam: <span id="humidityConvertedValue">1</span> dakika</small>
                                        </div>
                                        <button class="btn btn-primary btn-lg w-100" onclick="updateHumidityInterval()">
                                            <i class="fas fa-save me-2"></i> Güncelle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Karbonmonoksit Sensörü Ayarları -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-smog me-2"></i> Karbonmonoksit Sensörü</h5>
                                    <span class="badge bg-light text-warning" id="coIntervalBadge">1 dk</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="sensor-icon mb-4">
                                        <i class="fas fa-smog fa-4x text-warning"></i>
                                    </div>
                                    <div class="sensor-info text-center mb-4">
                                        <h6 class="text-muted">Mevcut Ölçüm Aralığı</h6>
                                        <h3 class="fw-bold" id="coIntervalValue">1 dakika</h3>
                                    </div>
                                    <div class="sensor-control w-100">
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control form-control-lg" id="coIntervalInput" placeholder="Değer" min="1" step="1">
                                            <select class="form-select form-select-lg" id="coIntervalUnit">
                                                <option value="minute">Dakika</option>
                                                <option value="hour">Saat</option>
                                                <option value="day">Gün</option>
                                                <option value="month">Ay</option>
                                            </select>
                                        </div>
                                        <div class="converted-value text-center mb-3">
                                            <small class="text-muted">Toplam: <span id="coConvertedValue">1</span> dakika</small>
                                        </div>
                                        <button class="btn btn-primary btn-lg w-100" onclick="updateCOInterval()">
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

.sensor-control .form-control,
.sensor-control .form-select {
    border: none;
    padding: 15px;
    font-size: 1.1rem;
}

.sensor-control .form-select {
    background-color: #f8f9fa;
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

.converted-value {
    background-color: #f8f9fa;
    padding: 8px;
    border-radius: 8px;
    font-size: 0.9rem;
}

/* Toast bildirimi */
.toast-container {
    z-index: 9999;
}

.toast {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}
</style>

@push('scripts')
<!-- Firebase SDK -->
<script>

// Firebase referanslarını al
const settingsRef = firebase.database().ref('user_sensors_settings');

// Zaman birimlerini dakikaya çevirme faktörleri
const timeFactors = {
    minute: 1,
    hour: 60,
    day: 1440, // 24 * 60
    month: 43200 // 30 * 24 * 60 (yaklaşık)
};

// Zaman birimi kısaltmaları
const timeUnitLabels = {
    minute: 'dk',
    hour: 'sa',
    day: 'gün',
    month: 'ay'
};

// Zaman birimi tam isimleri
const timeUnitFullLabels = {
    minute: 'dakika',
    hour: 'saat',
    day: 'gün',
    month: 'ay'
};

// Sayfa yüklendiğinde mevcut ayarları yükle
document.addEventListener('DOMContentLoaded', function() {
    loadSensorSettings();
    
    // Veritabanındaki değişiklikleri dinle
    settingsRef.on('value', function(snapshot) {
        const settingsData = snapshot.val();
        if (settingsData) {
            // Sıcaklık Sensörü
            if (settingsData.sensor_settings_1) {
                const interval = settingsData.sensor_settings_1.interval_min;
                updateIntervalDisplay('temp', interval);
            }
            
            // Nem Sensörü
            if (settingsData.sensor_settings_2) {
                const interval = settingsData.sensor_settings_2.interval_min;
                updateIntervalDisplay('humidity', interval);
            }
            
            // Karbonmonoksit Sensörü
            if (settingsData.sensor_settings_3) {
                const interval = settingsData.sensor_settings_3.interval_min;
                updateIntervalDisplay('co', interval);
            }
        }
    });
    
    // Input değişikliklerini dinle
    setupIntervalInputListeners();
});

// Input değişikliklerini dinle
function setupIntervalInputListeners() {
    // Sıcaklık Sensörü
    document.getElementById('tempIntervalInput').addEventListener('input', function() {
        updateConvertedValue('temp');
    });
    document.getElementById('tempIntervalUnit').addEventListener('change', function() {
        updateConvertedValue('temp');
    });
    
    // Nem Sensörü
    document.getElementById('humidityIntervalInput').addEventListener('input', function() {
        updateConvertedValue('humidity');
    });
    document.getElementById('humidityIntervalUnit').addEventListener('change', function() {
        updateConvertedValue('humidity');
    });
    
    // Karbonmonoksit Sensörü
    document.getElementById('coIntervalInput').addEventListener('input', function() {
        updateConvertedValue('co');
    });
    document.getElementById('coIntervalUnit').addEventListener('change', function() {
        updateConvertedValue('co');
    });
}

// Dönüştürülmüş değeri güncelle
function updateConvertedValue(type) {
    const input = document.getElementById(`${type}IntervalInput`);
    const unit = document.getElementById(`${type}IntervalUnit`).value;
    const convertedValue = document.getElementById(`${type}ConvertedValue`);
    
    const value = parseInt(input.value) || 1;
    const totalMinutes = value * timeFactors[unit];
    
    convertedValue.textContent = totalMinutes;
}

// Sensör ayarlarını yükle
function loadSensorSettings() {
    settingsRef.once('value')
        .then((snapshot) => {
            const settingsData = snapshot.val();
            if (settingsData) {
                // Sıcaklık Sensörü
                if (settingsData.sensor_settings_1) {
                    const interval = settingsData.sensor_settings_1.interval_min;
                    updateIntervalDisplay('temp', interval);
                }
                
                // Nem Sensörü
                if (settingsData.sensor_settings_2) {
                    const interval = settingsData.sensor_settings_2.interval_min;
                    updateIntervalDisplay('humidity', interval);
                }
                
                // Karbonmonoksit Sensörü
                if (settingsData.sensor_settings_3) {
                    const interval = settingsData.sensor_settings_3.interval_min;
                    updateIntervalDisplay('co', interval);
                }
            }
        })
        .catch((error) => {
            console.error('Sensör ayarları yüklenirken hata:', error);
            showToast('Sensör ayarları yüklenirken bir hata oluştu', 'danger');
        });
}

// Ölçüm aralığı görüntüsünü güncelle
function updateIntervalDisplay(type, interval) {
    const intervalValue = document.getElementById(`${type}IntervalValue`);
    const intervalBadge = document.getElementById(`${type}IntervalBadge`);
    const intervalInput = document.getElementById(`${type}IntervalInput`);
    const intervalUnit = document.getElementById(`${type}IntervalUnit`);
    const convertedValue = document.getElementById(`${type}ConvertedValue`);
    
    // En uygun birimi bul
    let bestUnit = 'minute';
    let bestValue = interval;
    
    if (interval >= timeFactors.month) {
        bestUnit = 'month';
        bestValue = Math.round(interval / timeFactors.month);
    } else if (interval >= timeFactors.day) {
        bestUnit = 'day';
        bestValue = Math.round(interval / timeFactors.day);
    } else if (interval >= timeFactors.hour) {
        bestUnit = 'hour';
        bestValue = Math.round(interval / timeFactors.hour);
    }
    
    // Görüntüyü güncelle
    if (intervalValue) intervalValue.textContent = `${bestValue} ${timeUnitFullLabels[bestUnit]}`;
    if (intervalBadge) intervalBadge.textContent = `${bestValue} ${timeUnitLabels[bestUnit]}`;
    if (intervalInput) intervalInput.value = bestValue;
    if (intervalUnit) intervalUnit.value = bestUnit;
    if (convertedValue) convertedValue.textContent = interval;
}

// Sıcaklık sensörü ölçüm aralığını güncelle
function updateTempInterval() {
    const intervalInput = document.getElementById('tempIntervalInput');
    const intervalUnit = document.getElementById('tempIntervalUnit').value;
    const value = parseInt(intervalInput.value) || 1;
    
    if (value < 1) {
        showToast('Lütfen geçerli bir değer girin', 'warning');
        return;
    }
    
    const totalMinutes = value * timeFactors[intervalUnit];
    
    const now = new Date();
    const updatedTime = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + ' ' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0') + ':' + 
                       String(now.getSeconds()).padStart(2, '0');
    
    settingsRef.child('sensor_settings_1').update({
        interval_min: totalMinutes,
        updated_time: updatedTime
    })
    .then(() => {
        updateIntervalDisplay('temp', totalMinutes);
        showToast('Sıcaklık sensörü ölçüm aralığı güncellendi', 'success');
    })
    .catch((error) => {
        console.error('Ölçüm aralığı güncellenirken hata:', error);
        showToast('Ölçüm aralığı güncellenirken bir hata oluştu', 'danger');
    });
}

// Nem sensörü ölçüm aralığını güncelle
function updateHumidityInterval() {
    const intervalInput = document.getElementById('humidityIntervalInput');
    const intervalUnit = document.getElementById('humidityIntervalUnit').value;
    const value = parseInt(intervalInput.value) || 1;
    
    if (value < 1) {
        showToast('Lütfen geçerli bir değer girin', 'warning');
        return;
    }
    
    const totalMinutes = value * timeFactors[intervalUnit];
    
    const now = new Date();
    const updatedTime = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + ' ' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0') + ':' + 
                       String(now.getSeconds()).padStart(2, '0');
    
    settingsRef.child('sensor_settings_2').update({
        interval_min: totalMinutes,
        updated_time: updatedTime
    })
    .then(() => {
        updateIntervalDisplay('humidity', totalMinutes);
        showToast('Nem sensörü ölçüm aralığı güncellendi', 'success');
    })
    .catch((error) => {
        console.error('Ölçüm aralığı güncellenirken hata:', error);
        showToast('Ölçüm aralığı güncellenirken bir hata oluştu', 'danger');
    });
}

// Karbonmonoksit sensörü ölçüm aralığını güncelle
function updateCOInterval() {
    const intervalInput = document.getElementById('coIntervalInput');
    const intervalUnit = document.getElementById('coIntervalUnit').value;
    const value = parseInt(intervalInput.value) || 1;
    
    if (value < 1) {
        showToast('Lütfen geçerli bir değer girin', 'warning');
        return;
    }
    
    const totalMinutes = value * timeFactors[intervalUnit];
    
    const now = new Date();
    const updatedTime = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + ' ' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0') + ':' + 
                       String(now.getSeconds()).padStart(2, '0');
    
    settingsRef.child('sensor_settings_3').update({
        interval_min: totalMinutes,
        updated_time: updatedTime
    })
    .then(() => {
        updateIntervalDisplay('co', totalMinutes);
        showToast('Karbonmonoksit sensörü ölçüm aralığı güncellendi', 'success');
    })
    .catch((error) => {
        console.error('Ölçüm aralığı güncellenirken hata:', error);
        showToast('Ölçüm aralığı güncellenirken bir hata oluştu', 'danger');
    });
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
</script>
@endpush
@endsection 