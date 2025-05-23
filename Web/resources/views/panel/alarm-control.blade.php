@extends('panel.layouts.app')

@section('title', 'Alarm Kontrolü')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-shield-alt me-2"></i> Alarm Kontrol Paneli</h4>
                        <div>
                            <button class="btn btn-success me-2" onclick="activateAllAlarms()">
                                <i class="fas fa-power-off me-1"></i> Tüm Alarmları Aç
                            </button>
                            <button class="btn btn-danger" onclick="deactivateAllAlarms()">
                                <i class="fas fa-power-off me-1"></i> Tüm Alarmları Kapat
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Buzzer Alarm Kontrolü -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-volume-up me-2"></i> Sesli Uyarı</h5>
                                    <span class="badge bg-light text-danger" id="buzzerStatusBadge">Kapalı</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="alarm-control-container mb-4">
                                        <div class="alarm-toggle" id="buzzerAlarmToggle" onclick="toggleAlarm('buzzer')">
                                            <div class="alarm-toggle-inner">
                                                <i class="fas fa-power-off"></i>
                                            </div>
                                        </div>
                                        <div class="alarm-label mt-3 text-center">
                                            <span class="alarm-status" id="buzzerAlarmStatus">Kapalı</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Uyarı Işığı Alarm Kontrolü -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i> Uyarı Işığı</h5>
                                    <span class="badge bg-light text-warning" id="ledStatusBadge">Kapalı</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="alarm-control-container mb-4">
                                        <div class="alarm-toggle" id="ledAlarmToggle" onclick="toggleAlarm('led')">
                                            <div class="alarm-toggle-inner">
                                                <i class="fas fa-power-off"></i>
                                            </div>
                                        </div>
                                        <div class="alarm-label mt-3 text-center">
                                            <span class="alarm-status" id="ledAlarmStatus">Kapalı</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pencere Durumu Alarm Kontrolü -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-door-open me-2"></i> Pencere Durumu</h5>
                                    <span class="badge bg-light text-info" id="servoStatusBadge">Kapalı</span>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="alarm-control-container mb-4">
                                        <div class="alarm-toggle" id="servoAlarmToggle" onclick="toggleAlarm('servo')">
                                            <div class="alarm-toggle-inner">
                                                <i class="fas fa-power-off"></i>
                                            </div>
                                        </div>
                                        <div class="alarm-label mt-3 text-center">
                                            <span class="alarm-status" id="servoAlarmStatus">Kapalı</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sistem Kontrol Butonları -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class=" p-4 rounded-lg shadow-sm">
                                <div class="system-control-buttons d-flex justify-content-center gap-3">
                                    <button class="btn btn-system-control active" id="autoSystemBtn">
                                        <i class="fas fa-robot me-2"></i>
                                        <span>Otomatik Sistem</span>
                                    </button>
                                    <button class="btn btn-system-control" id="manualSystemBtn">
                                        <i class="fas fa-user me-2"></i>
                                        <span>Kullanıcı Kontrolünde</span>
                                    </button>
                                    <button class="btn btn-system-control" id="silentModeBtn">
                                        <i class="fas fa-volume-mute me-2"></i>
                                        <span>Sessiz Mod</span>
                                    </button>
                                    <button class="btn btn-system-control" id="phoneVibrateBtn">
                                        <i class="fas fa-mobile-alt me-2"></i>
                                        <span>Telefon Titreşimi</span>
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
.alarm-control-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.alarm-toggle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: linear-gradient(145deg, #e6e6e6, #ffffff);
    box-shadow: 8px 8px 16px #d1d1d1, -8px -8px 16px #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.alarm-toggle.active {
    background: linear-gradient(145deg, #28a745, #218838);
    box-shadow: inset 8px 8px 16px #1e7e34, inset -8px -8px 16px #34c759;
}

.alarm-toggle-inner {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: linear-gradient(145deg, #f0f0f0, #ffffff);
    box-shadow: inset 5px 5px 10px #d1d1d1, inset -5px -5px 10px #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.alarm-toggle.active .alarm-toggle-inner {
    background: linear-gradient(145deg, #34c759, #28a745);
    box-shadow: inset 5px 5px 10px #1e7e34, inset -5px -5px 10px #34c759;
}

.alarm-toggle i {
    font-size: 3rem;
    color: #666;
    transition: all 0.3s ease;
}

.alarm-toggle.active i {
    color: #fff;
}

.alarm-label {
    font-size: 1.4rem;
    font-weight: bold;
}

.alarm-status {
    padding: 0.6rem 1.2rem;
    border-radius: 50px;
    background-color: #f8f9fa;
    color: #dc3545;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.alarm-status.active {
    background-color: #28a745;
    color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

/* Hover efektleri */
.alarm-toggle:hover {
    transform: scale(1.05);
}

.alarm-toggle:active {
    transform: scale(0.95);
}

/* Kart animasyonları */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

/* Buton animasyonları */
.btn {
    transition: all 0.3s ease;
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}

.btn:active {
    transform: translateY(0);
}

/* Toast bildirimi */
.toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    min-width: 300px;
}

.toast.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.toast.bg-warning .btn-close {
    filter: brightness(0) saturate(100%);
}

/* Telefon titreşim butonu için özel stil */
#phoneVibrateBtn.active {
    background-color: #0d6efd;
    color: white;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);
    position: relative;
    overflow: hidden;
}

#phoneVibrateBtn {
    cursor: pointer !important;
    user-select: none;
}

#phoneVibrateBtn.active::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    opacity: 0;
    animation: pulse-white 2s infinite;
    pointer-events: none; /* Tıklamayı engellememesi için */
}

@keyframes vibration {
    0% { transform: translateX(0); }
    20% { transform: translateX(-2px) rotate(-1deg); }
    40% { transform: translateX(2px) rotate(1deg); }
    60% { transform: translateX(-2px) rotate(-1deg); }
    80% { transform: translateX(2px) rotate(1deg); }
    100% { transform: translateX(0); }
}

@keyframes pulse-white {
    0% { transform: scale(1); opacity: 0; }
    50% { opacity: 0.4; }
    100% { transform: scale(1.5); opacity: 0; }
}

/* Sistem Kontrol Container Stilleri */
.system-control-container {
    background: linear-gradient(145deg, #f8f9fa, #e9ecef);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 5px 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.system-control-container:hover {
    transform: translateY(-2px);
    box-shadow: 8px 8px 20px rgba(0,0,0,0.1);
}

.system-control-container h5 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.system-control-container h5 i {
    color: #007bff;
}

/* Sistem Kontrol Butonları Stilleri */
.system-control-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
}

.btn-system-control {
    display: flex;
    align-items: center;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff, #e6e6e6);
    border: none;
    color: #495057;
    box-shadow: 5px 5px 10px #d1d1d1, -5px -5px 10px #ffffff;
    min-width: 200px;
    justify-content: center;
}

.btn-system-control:hover {
    transform: translateY(-2px);
    box-shadow: 8px 8px 15px #d1d1d1, -8px -8px 15px #ffffff;
}

.btn-system-control:active {
    transform: translateY(0);
    box-shadow: inset 5px 5px 10px #d1d1d1, inset -5px -5px 10px #ffffff;
}

.btn-system-control.active {
    background: linear-gradient(145deg, #007bff, #0056b3);
    color: white;
    box-shadow: inset 5px 5px 10px #0056b3, inset -5px -5px 10px #007bff;
}

#silentModeBtn.active {
    background: linear-gradient(145deg, #6c757d, #495057);
    color: white;
    box-shadow: inset 5px 5px 10px #495057, inset -5px -5px 10px #6c757d;
}

.btn-system-control i {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}

/* Alarm Kontrol Butonları Stilleri */
.btn-success, .btn-danger {
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 5px 5px 10px rgba(0,0,0,0.1);
}

.btn-success {
    background: linear-gradient(145deg, #28a745, #218838);
    color: white;
}

.btn-danger {
    background: linear-gradient(145deg, #dc3545, #c82333);
    color: white;
}

.btn-success:hover, .btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 8px 8px 15px rgba(0,0,0,0.15);
}

.btn-success:active, .btn-danger:active {
    transform: translateY(0);
    box-shadow: inset 5px 5px 10px rgba(0,0,0,0.2);
}

/* Responsive düzenlemeler */
@media (max-width: 768px) {
    .system-control-buttons {
        flex-direction: column;
        gap: 1rem;
    }

    .btn-system-control {
        width: 100%;
    }

    .card-header > div:last-child {
        flex-direction: column;
        gap: 1rem;
    }

    .btn-success, .btn-danger {
        width: 100%;
    }
}

/* Devre dışı buton stilleri */
.btn.disabled,
.alarm-toggle.disabled {
    cursor: not-allowed !important;
    position: relative;
    opacity: 0.5 !important;
    filter: grayscale(50%) !important;
    pointer-events: all !important; /* Tıklama olaylarını yakalamak için */
}

.btn.disabled::after,
.alarm-toggle.disabled::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    border-radius: inherit;
}

/* Sessiz mod aktif olduğunda gösterilecek stil */
.silent-mode-active .alarm-toggle::before {
    content: '\f6a9';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 1.2rem;
    color: #6c757d;
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.silent-mode-active .alarm-status::after {
    content: '(Sessiz)';
    margin-left: 5px;
    font-size: 0.8rem;
    opacity: 0.7;
}

.btn.disabled:hover,
.alarm-toggle.disabled:hover {
    transform: none !important;
    box-shadow: none !important;
}

.btn-system-control.disabled {
    opacity: 0.7 !important;
    cursor: not-allowed !important;
    background: linear-gradient(145deg, #e6e6e6, #cccccc) !important;
    color: #666 !important;
    pointer-events: all !important; /* Event'ların çalışması için */
}

.btn-system-control.disabled:hover {
    transform: none !important;
    box-shadow: none !important;
}
</style>

@push('scripts')
<!-- Firebase SDK -->
<script>

// Firebase referanslarını al
const deviceRef = firebase.database().ref('device_outputs');
const alarmsLogRef = firebase.database().ref('alarms_log');
const systemControlRef = firebase.database().ref('system_control');

// Telefon titreşim butonunu sıfırla
function resetPhoneVibrateButton(isActive) {
    phoneVibrateBtn.innerHTML = `
        <i class="fas fa-mobile-alt me-2"></i>
        <span>${isActive ? 'Telefon Titreşimi Aktif' : 'Telefon Titreşimi'}</span>
    `;
    phoneVibrateBtn.classList.toggle('active', isActive);
}

// Sayfa yüklendiğinde çalıştırılacak fonksiyonlar
document.addEventListener('DOMContentLoaded', function() {
    // Global değişkenler
    window.isProcessingPhoneVibrate = false;

    // Buton referansları
    const autoSystemBtn = document.getElementById('autoSystemBtn');
    const manualSystemBtn = document.getElementById('manualSystemBtn');
    const silentModeBtn = document.getElementById('silentModeBtn');
    const phoneVibrateBtn = document.getElementById('phoneVibrateBtn');

    // Otomatik ve Kullanıcı kontrol butonları
    [autoSystemBtn, manualSystemBtn].forEach(btn => {
        btn.addEventListener('click', function(e) {
            const isAuto = btn === autoSystemBtn;
            updateSystemControlMode(isAuto, false);
        });
    });

    // Sessiz mod butonu - sadece silent_mode değerini toggle yapar
    silentModeBtn.addEventListener('click', function(e) {
        updateSystemControlMode(null, true);
    });

    // Telefon titreşimi butonu - sadece phone_vibrate değerini toggle yapar
    phoneVibrateBtn.addEventListener('click', togglePhoneVibrate);

    // Sayfa yüklendiğinde mevcut alarm durumlarını yükle
    loadAlarmStates();

    // Sayfa yüklendiğinde mevcut sistem durumunu kontrol et
    loadSystemControlState();
    setupDeviceOutputsListener();

    // Sessiz mod değişikliğini dinle
    systemControlRef.on('value', (snapshot) => {
        const systemControlData = snapshot.val() || {};
        updatePhoneVibrateButtonState(systemControlData.silent_mode);
    });
});

// Alarm durumlarını yükle
function loadAlarmStates() {
    deviceRef.once('value')
        .then((snapshot) => {
            const deviceData = snapshot.val();
            if (deviceData) {
                // Buzzer
                if (deviceData.output_type_1) {
                    const isActive = deviceData.output_type_1.status === 1;
                    updateStatusBadge('buzzerStatusBadge', isActive);
                    updateToggleState('buzzer', isActive);
                }

                // Uyarı Işığı
                if (deviceData.output_type_2) {
                    const isActive = deviceData.output_type_2.status === 1;
                    updateStatusBadge('ledStatusBadge', isActive);
                    updateToggleState('led', isActive);
                }

                // Pencere Durumu
                if (deviceData.output_type_3) {
                    const isActive = deviceData.output_type_3.status === 1;
                    updateStatusBadge('servoStatusBadge', isActive);
                    updateToggleState('servo', isActive);
                }
            }
        })
        .catch((error) => {
            console.error('Alarm durumları yüklenirken hata:', error);
            showToast('Alarm durumları yüklenirken bir hata oluştu', 'danger');
        });
}

// Sistem kontrol durumunu yükle
function loadSystemControlState() {
    systemControlRef.once('value')
        .then((snapshot) => {
            const systemControlData = snapshot.val();
            if (systemControlData) {
                updateSystemControlUI(
                    systemControlData.mechanical_intervention,
                    systemControlData.user_intervention,
                    systemControlData.silent_mode
                );

                // Telefon titreşimi durumunu da güncelle
                updatePhoneVibrateButton(systemControlData.phone_vibrate);
            }
        })
        .catch((error) => {
            console.error('Sistem kontrol durumu yüklenirken hata:', error);
            showToast('Sistem kontrol durumu yüklenirken bir hata oluştu', 'danger');
        });
}

// Sistem kontrol UI'ını güncelle
function updateSystemControlUI(mechanicalIntervention, userIntervention, silentMode = false) {
    const autoSystemBtn = document.getElementById('autoSystemBtn');
    const manualSystemBtn = document.getElementById('manualSystemBtn');
    const silentModeBtn = document.getElementById('silentModeBtn');
    const alarmControlsContainer = document.querySelector('.card-body');

    // Sessiz mod durumunu güncelle - bağımsız bir toggle olarak çalışır
    if (silentMode) {
        silentModeBtn.classList.add('active');
        // Sessiz mod aktif olduğunda görsel değişiklikleri uygula
        alarmControlsContainer.classList.add('silent-mode-active');
    } else {
        silentModeBtn.classList.remove('active');
        // Sessiz mod kapalı olduğunda görsel değişiklikleri kaldır
        alarmControlsContainer.classList.remove('silent-mode-active');
    }

    // Telefon titreşim butonunun durumunu güncelle
    updatePhoneVibrateButtonState(silentMode);

    // Otomatik veya Kullanıcı kontrol modu
    if (mechanicalIntervention) {
        autoSystemBtn.classList.add('active');
        manualSystemBtn.classList.remove('active');

        // Otomatik modda tüm alarm kontrol butonları devre dışı
        disableAllControls();
    } else if (userIntervention) {
        autoSystemBtn.classList.remove('active');
        manualSystemBtn.classList.add('active');

        // Kullanıcı modunda butonları aktif et (sessiz mod durumuna bakılmaksızın)
        enableAllControls();
    }
}

// Tüm alarm kontrollerini aktif et (Kullanıcı kontrol modu için)
function enableAllControls() {
    const alarmToggles = document.querySelectorAll('.alarm-toggle');
    const alarmButtons = document.querySelectorAll('.btn-success, .btn-danger');

    alarmToggles.forEach(toggle => {
        toggle.classList.remove('disabled');
        toggle.style.opacity = '1';
        toggle.style.filter = 'none';
        toggle.style.cursor = 'pointer';
        toggle.title = '';
        const type = toggle.id.replace('AlarmToggle', '').toLowerCase();
        toggle.onclick = () => toggleAlarm(type);
    });

    alarmButtons.forEach(button => {
        button.classList.remove('disabled');
        button.style.opacity = '1';
        button.style.filter = 'none';
        button.style.cursor = 'pointer';
        button.title = '';
        if (button.classList.contains('btn-success')) {
            button.onclick = activateAllAlarms;
        } else {
            button.onclick = deactivateAllAlarms;
        }
    });
}

// Tüm kontrolleri devre dışı bırak
function disableAllControls(message = 'Sistem kontrol modunda kontrol edilemez') {
    const alarmToggles = document.querySelectorAll('.alarm-toggle');
    const alarmButtons = document.querySelectorAll('.btn-success, .btn-danger');

    alarmToggles.forEach(toggle => {
        toggle.classList.add('disabled');
        toggle.style.opacity = '0.35';
        toggle.style.filter = 'grayscale(90%)';
        toggle.style.cursor = 'not-allowed';
        toggle.title = message;
        toggle.onclick = function(e) {
            e.preventDefault();
            showToast(message, 'warning');
        };
    });

    alarmButtons.forEach(button => {
        button.classList.add('disabled');
        button.style.opacity = '0.35';
        button.style.filter = 'grayscale(90%)';
        button.style.cursor = 'not-allowed';
        button.title = message;
        button.onclick = function(e) {
            e.preventDefault();
            showToast(message, 'warning');
        };
    });
}

// Sistem kontrol modunu güncelle
function updateSystemControlMode(isAuto, toggleSilent = false) {
    // Buton referanslarını al
    const autoSystemBtn = document.getElementById('autoSystemBtn');
    const manualSystemBtn = document.getElementById('manualSystemBtn');
    const silentModeBtn = document.getElementById('silentModeBtn');
    const alarmControlsContainer = document.querySelector('.card-body');

    // Önce mevcut durumu al
    systemControlRef.once('value')
        .then((snapshot) => {
            const systemControlData = snapshot.val() || {};

            // Sessiz mod toggle yapılırken mevcut değerleri koru
            let newMechanicalIntervention = isAuto;
            let newUserIntervention = !isAuto;

            // Eğer sadece sessiz mod toggle ediliyorsa, mevcut kontrol modunu koru
            if (toggleSilent === true && isAuto === null) {
                newMechanicalIntervention = systemControlData.mechanical_intervention || false;
                newUserIntervention = systemControlData.user_intervention || false;
            }

            // Sessiz mod durumu - mevcut değeri al veya toggle yap
            let newSilentMode = toggleSilent ?
                !(systemControlData.silent_mode || false) : // Toggle yapılıyorsa tersine çevir
                (systemControlData.silent_mode || false);   // Toggle yapılmıyorsa mevcut değeri koru

            // PERFORMANS İYİLEŞTİRMESİ: Önce UI güncellemesi yapılıyor
            // Ardından veritabanı işlemi gerçekleştiriliyor

            // UI güncellemesini hemen yap
            if (toggleSilent) {
                const silentStatus = newSilentMode ? 'aktif' : 'devre dışı';
                showToast(`Sessiz mod ${silentStatus} edildi`, 'success');

                // Sessiz mod butonunu güncelle
                silentModeBtn.classList.toggle('active', newSilentMode);
                if (newSilentMode) {
                    alarmControlsContainer.classList.add('silent-mode-active');
                } else {
                    alarmControlsContainer.classList.remove('silent-mode-active');
                }
            } else {
                const modeText = isAuto ? 'Otomatik Sistem' : 'Kullanıcı Kontrolünde';
                showToast(`Sistem ${modeText} moduna geçirildi`, 'success');

                // Butonları doğrudan güncelle
                autoSystemBtn.classList.toggle('active', newMechanicalIntervention);
                manualSystemBtn.classList.toggle('active', newUserIntervention);

                // Kontrol moduna göre UI güncelleme
                if (newMechanicalIntervention) {
                    disableAllControls();
                } else if (newUserIntervention) {
                    enableAllControls();
                }
            }

            // Veritabanını arka planda güncelle
            const updates = {
                mechanical_intervention: newMechanicalIntervention,
                user_intervention: newUserIntervention,
                silent_mode: newSilentMode,
                updated_at: firebase.database.ServerValue.TIMESTAMP
            };

            // Asenkron olarak veritabanını güncelle, ikinci okuma yok
            return systemControlRef.update(updates)
                .catch((error) => {
                    console.error('Sistem kontrol modu güncellenirken hata:', error);
                    showToast('Sistem kontrol modu güncellenirken bir hata oluştu', 'danger');

                    // Hata durumunda UI'ı eski haline getir
                    if (toggleSilent) {
                        silentModeBtn.classList.toggle('active', systemControlData.silent_mode || false);
                        if (systemControlData.silent_mode) {
                            alarmControlsContainer.classList.add('silent-mode-active');
                        } else {
                            alarmControlsContainer.classList.remove('silent-mode-active');
                        }
                    } else {
                        autoSystemBtn.classList.toggle('active', systemControlData.mechanical_intervention || false);
                        manualSystemBtn.classList.toggle('active', systemControlData.user_intervention || false);

                        if (systemControlData.mechanical_intervention) {
                            disableAllControls();
                        } else if (systemControlData.user_intervention) {
                            enableAllControls();
                        }
                    }
                });
        })
        .catch((error) => {
            console.error('Mevcut sistem durumu alınırken hata:', error);
            showToast('Sistem durumu alınırken bir hata oluştu', 'danger');
        });
}

// Toggle durumunu güncelle
function updateToggleState(type, isActive) {
    const toggle = document.getElementById(`${type}AlarmToggle`);
    const status = document.getElementById(`${type}AlarmStatus`);

    if (isActive) {
        toggle.classList.add('active');
        status.textContent = 'Açık';
        status.classList.add('active');
    } else {
        toggle.classList.remove('active');
        status.textContent = 'Kapalı';
        status.classList.remove('active');
    }
}

// Toggle'a tıklandığında
function toggleAlarm(type) {
    // Sistem kontrol modunu kontrol et
    systemControlRef.once('value')
        .then((snapshot) => {
            const systemControlData = snapshot.val();
            if (systemControlData && systemControlData.mechanical_intervention) {
                showToast('Otomatik modda manuel kontrol yapılamaz. Lütfen önce "Kullanıcı Kontrolünde" moduna geçin.', 'warning');
                return;
            }

            // First_alarm durumunu kontrol et - sadece ilgili çıkış için
            return deviceRef.once('value');
        })
        .then((snapshot) => {
            if (!snapshot) return; // Önceki if bloğunda return yapıldıysa

            const deviceData = snapshot.val();

            // Alarm tipine göre first_alarm kontrolü yap
            let outputType = '';
            if (type === 'buzzer') outputType = 'output_type_1';
            else if (type === 'led') outputType = 'output_type_2';
            else if (type === 'servo') outputType = 'output_type_3';

            if (deviceData && deviceData[outputType] && deviceData[outputType].first_alarm === true) {
                showToast('Öncelikle bu alarm çalması gerekiyor', 'warning');
                return;
            }

            const toggle = document.getElementById(`${type}AlarmToggle`);
            const isCurrentlyActive = toggle.classList.contains('active');
            const newActiveState = !isCurrentlyActive;

            // ÖNEMLİ: Toggle durumunu güncelle - UI değişikliği önce yapılmalı
            updateToggleState(type, newActiveState);

            // Veritabanına gönderilecek değerler
            const alarmData = {
                status: newActiveState ? 1 : 0,
                is_alarm_acknowledged: newActiveState ? false : true,
                last_alarm_time: firebase.database.ServerValue.TIMESTAMP,
                updated_at: firebase.database.ServerValue.TIMESTAMP
            };

            // Hangi output'u güncelleyeceğimizi belirle
            let outputPath = '';
            let badgeId = '';
            let successMessage = '';
            let sensorId = '';

            if (type === 'buzzer') {
                outputPath = 'output_type_1';
                badgeId = 'buzzerStatusBadge';
                successMessage = 'Buzzer alarm ayarları güncellendi';
                sensorId = 'sensor_type_1';
            } else if (type === 'led') {
                outputPath = 'output_type_2';
                badgeId = 'ledStatusBadge';
                successMessage = 'Uyarı Işığı alarm ayarları güncellendi';
                sensorId = 'sensor_type_2';
            } else if (type === 'servo') {
                outputPath = 'output_type_3';
                badgeId = 'servoStatusBadge';
                successMessage = 'Pencere Durumu alarm ayarları güncellendi';
                sensorId = 'sensor_type_3';
            }

            // Veritabanını güncelle
            deviceRef.child(outputPath).update(alarmData)
                .then(() => {
                    // Alarm logunu kaydet
                    const logData = {
                        alarm_description: newActiveState ? 'Kullanıcı tarafından manuel olarak alarm kontrolü açıldı' : 'Kullanıcı tarafından manuel olarak alarm kontrolü kapatıldı',
                        alarm_type: newActiveState ? 'Normal' : 'Tehlike',
                        created_at: firebase.database.ServerValue.TIMESTAMP,
                        device_id: 'device1',
                        sensor_id: sensorId,
                        threshold: 0,
                        triggered_output: outputPath,
                        value: 0
                    };

                    return alarmsLogRef.push(logData);
                })
                .then(() => {
                    // Status badge'i güncelle
                    updateStatusBadge(badgeId, newActiveState);
                    showToast(successMessage, 'success');
                })
                .catch((error) => {
                    // Hata durumunda UI'ı eski haline getir
                    console.error('Alarm güncellenirken hata:', error);
                    showToast('Alarm güncellenirken bir hata oluştu', 'danger');

                    // UI'ı eski haline getir - hata durumunda
                    updateToggleState(type, isCurrentlyActive);
                });
        });
}

// Durum rozetini güncelle
function updateStatusBadge(badgeId, isActive) {
    const badge = document.getElementById(badgeId);
    if (isActive) {
        badge.textContent = 'Açık';
        badge.classList.remove('bg-light');
        badge.classList.add('bg-success');
        badge.classList.remove('text-danger', 'text-warning', 'text-info');
        badge.classList.add('text-white');
    } else {
        badge.textContent = 'Kapalı';
        badge.classList.remove('bg-success');
        badge.classList.add('bg-light');
        badge.classList.remove('text-white');

        // Renk sınıfını badge ID'sine göre ayarla
        if (badgeId === 'buzzerStatusBadge') {
            badge.classList.add('text-danger');
        } else if (badgeId === 'ledStatusBadge') {
            badge.classList.add('text-warning');
        } else if (badgeId === 'servoStatusBadge') {
            badge.classList.add('text-info');
        }
    }
}

// Tüm alarmları aktif et
function activateAllAlarms() {
    // Sistem kontrol modunu kontrol et
    systemControlRef.once('value')
        .then((snapshot) => {
            const systemControlData = snapshot.val();
            if (systemControlData && systemControlData.mechanical_intervention) {
                showToast('Otomatik modda manuel kontrol yapılamaz. Lütfen önce "Kullanıcı Kontrolünde" moduna geçin.', 'warning');
                return;
            }

            // First_alarm durumlarını kontrol et
            return deviceRef.once('value');
        })
        .then((snapshot) => {
            if (!snapshot) return; // Önceki if bloğunda return yapıldıysa

            const deviceData = snapshot.val() || {};
            const updates = {};
            const disabledOutputs = [];

            // Her bir çıkış için first_alarm durumunu kontrol et
            // output_type_1 - Buzzer
            if (deviceData.output_type_1 && deviceData.output_type_1.first_alarm !== true) {
                updates['output_type_1/status'] = 1;
                updates['output_type_1/is_alarm_acknowledged'] = false;
                updates['output_type_1/last_alarm_time'] = firebase.database.ServerValue.TIMESTAMP;
                updates['output_type_1/updated_at'] = firebase.database.ServerValue.TIMESTAMP;
            } else if (deviceData.output_type_1) {
                disabledOutputs.push('Buzzer');
            }

            // output_type_2 - LED
            if (deviceData.output_type_2 && deviceData.output_type_2.first_alarm !== true) {
                updates['output_type_2/status'] = 1;
                updates['output_type_2/is_alarm_acknowledged'] = false;
                updates['output_type_2/last_alarm_time'] = firebase.database.ServerValue.TIMESTAMP;
                updates['output_type_2/updated_at'] = firebase.database.ServerValue.TIMESTAMP;
            } else if (deviceData.output_type_2) {
                disabledOutputs.push('Uyarı Işığı');
            }

            // output_type_3 - Servo
            if (deviceData.output_type_3 && deviceData.output_type_3.first_alarm !== true) {
                updates['output_type_3/status'] = 1;
                updates['output_type_3/is_alarm_acknowledged'] = false;
                updates['output_type_3/last_alarm_time'] = firebase.database.ServerValue.TIMESTAMP;
                updates['output_type_3/updated_at'] = firebase.database.ServerValue.TIMESTAMP;
            } else if (deviceData.output_type_3) {
                disabledOutputs.push('Pencere Durumu');
            }

            // Eğer hiçbir çıkış güncellenemiyorsa
            if (Object.keys(updates).length === 0) {
                showToast('Hiçbir alarm güncellenemedi, öncelikle alarmların çalışması gerekiyor', 'warning');
                return;
            }

            // Devre dışı olan çıkışlar varsa, kullanıcıya bildir
            if (disabledOutputs.length > 0) {
                showToast(`Bazı alarmlar güncellenemedi: ${disabledOutputs.join(', ')}`, 'warning');
            }

            return deviceRef.update(updates);
        })
        .then((result) => {
            if (!result) return; // Önceki if bloğunda return yapıldıysa

            // Veritabanından güncel durumu al
            return deviceRef.once('value');
        })
        .then((snapshot) => {
            if (!snapshot) return; // Önceki if bloğunda return yapıldıysa

            const deviceData = snapshot.val() || {};

            // UI'ı güncelle
            if (deviceData.output_type_1) {
                const isActive = deviceData.output_type_1.status === 1;
                updateToggleState('buzzer', isActive);
                updateStatusBadge('buzzerStatusBadge', isActive);
            }

            if (deviceData.output_type_2) {
                const isActive = deviceData.output_type_2.status === 1;
                updateToggleState('led', isActive);
                updateStatusBadge('ledStatusBadge', isActive);
            }

            if (deviceData.output_type_3) {
                const isActive = deviceData.output_type_3.status === 1;
                updateToggleState('servo', isActive);
                updateStatusBadge('servoStatusBadge', isActive);
            }

            // Log kayıtları
            const logPromises = [];

            if (deviceData.output_type_1 && deviceData.output_type_1.status === 1 && deviceData.output_type_1.first_alarm !== true) {
                logPromises.push(createLogEntry('output_type_1', 'Kullanıcı tarafından manuel olarak alarm kontrolü açıldı', 'Normal', 'sensor_type_1'));
            }

            if (deviceData.output_type_2 && deviceData.output_type_2.status === 1 && deviceData.output_type_2.first_alarm !== true) {
                logPromises.push(createLogEntry('output_type_2', 'Kullanıcı tarafından manuel olarak alarm kontrolü açıldı', 'Normal', 'sensor_type_2'));
            }

            if (deviceData.output_type_3 && deviceData.output_type_3.status === 1 && deviceData.output_type_3.first_alarm !== true) {
                logPromises.push(createLogEntry('output_type_3', 'Kullanıcı tarafından manuel olarak alarm kontrolü açıldı', 'Normal', 'sensor_type_3'));
            }

            return Promise.all(logPromises);
        })
        .then((result) => {
            if (!result) return; // Önceki if bloğunda return yapıldıysa
            if (result.length > 0) {
                showToast('Alarmlar güncellendi', 'success');
            }
        })
        .catch((error) => {
            console.error('Alarmlar güncellenirken hata:', error);
            showToast('Alarmlar güncellenirken bir hata oluştu', 'danger');
        });
}

// Tüm alarmları pasif et
function deactivateAllAlarms() {
    // Sistem kontrol modunu kontrol et
    systemControlRef.once('value')
        .then((snapshot) => {
            const systemControlData = snapshot.val();
            if (systemControlData && systemControlData.mechanical_intervention) {
                showToast('Otomatik modda manuel kontrol yapılamaz. Lütfen önce "Kullanıcı Kontrolünde" moduna geçin.', 'warning');
                return;
            }

            // First_alarm durumlarını kontrol et
            return deviceRef.once('value');
        })
        .then((snapshot) => {
            if (!snapshot) return; // Önceki if bloğunda return yapıldıysa

            const deviceData = snapshot.val() || {};
            const updates = {};
            const disabledOutputs = [];

            // Her bir çıkış için first_alarm durumunu kontrol et
            // output_type_1 - Buzzer
            if (deviceData.output_type_1 && deviceData.output_type_1.first_alarm !== true) {
                updates['output_type_1/status'] = 0;
                updates['output_type_1/is_alarm_acknowledged'] = true;
                updates['output_type_1/last_alarm_time'] = firebase.database.ServerValue.TIMESTAMP;
                updates['output_type_1/updated_at'] = firebase.database.ServerValue.TIMESTAMP;
            } else if (deviceData.output_type_1) {
                disabledOutputs.push('Buzzer');
            }

            // output_type_2 - LED
            if (deviceData.output_type_2 && deviceData.output_type_2.first_alarm !== true) {
                updates['output_type_2/status'] = 0;
                updates['output_type_2/is_alarm_acknowledged'] = true;
                updates['output_type_2/last_alarm_time'] = firebase.database.ServerValue.TIMESTAMP;
                updates['output_type_2/updated_at'] = firebase.database.ServerValue.TIMESTAMP;
            } else if (deviceData.output_type_2) {
                disabledOutputs.push('Uyarı Işığı');
            }

            // output_type_3 - Servo
            if (deviceData.output_type_3 && deviceData.output_type_3.first_alarm !== true) {
                updates['output_type_3/status'] = 0;
                updates['output_type_3/is_alarm_acknowledged'] = true;
                updates['output_type_3/last_alarm_time'] = firebase.database.ServerValue.TIMESTAMP;
                updates['output_type_3/updated_at'] = firebase.database.ServerValue.TIMESTAMP;
            } else if (deviceData.output_type_3) {
                disabledOutputs.push('Pencere Durumu');
            }

            // Eğer hiçbir çıkış güncellenemiyorsa
            if (Object.keys(updates).length === 0) {
                showToast('Hiçbir alarm güncellenemedi, öncelikle alarmların çalışması gerekiyor', 'warning');
                return;
            }

            // Devre dışı olan çıkışlar varsa, kullanıcıya bildir
            if (disabledOutputs.length > 0) {
                showToast(`Bazı alarmlar güncellenemedi: ${disabledOutputs.join(', ')}`, 'warning');
            }

            return deviceRef.update(updates);
        })
        .then((result) => {
            if (!result) return; // Önceki if bloğunda return yapıldıysa

            // Veritabanından güncel durumu al
            return deviceRef.once('value');
        })
        .then((snapshot) => {
            if (!snapshot) return; // Önceki if bloğunda return yapıldıysa

            const deviceData = snapshot.val() || {};

            // UI'ı güncelle
            if (deviceData.output_type_1) {
                const isActive = deviceData.output_type_1.status === 1;
                updateToggleState('buzzer', isActive);
                updateStatusBadge('buzzerStatusBadge', isActive);
            }

            if (deviceData.output_type_2) {
                const isActive = deviceData.output_type_2.status === 1;
                updateToggleState('led', isActive);
                updateStatusBadge('ledStatusBadge', isActive);
            }

            if (deviceData.output_type_3) {
                const isActive = deviceData.output_type_3.status === 1;
                updateToggleState('servo', isActive);
                updateStatusBadge('servoStatusBadge', isActive);
            }

            // Log kayıtları
            const logPromises = [];

            if (deviceData.output_type_1 && deviceData.output_type_1.status === 0 && deviceData.output_type_1.first_alarm !== true) {
                logPromises.push(createLogEntry('output_type_1', 'Kullanıcı tarafından manuel olarak alarm kontrolü kapatıldı', 'Tehlike', 'sensor_type_1'));
            }

            if (deviceData.output_type_2 && deviceData.output_type_2.status === 0 && deviceData.output_type_2.first_alarm !== true) {
                logPromises.push(createLogEntry('output_type_2', 'Kullanıcı tarafından manuel olarak alarm kontrolü kapatıldı', 'Tehlike', 'sensor_type_2'));
            }

            if (deviceData.output_type_3 && deviceData.output_type_3.status === 0 && deviceData.output_type_3.first_alarm !== true) {
                logPromises.push(createLogEntry('output_type_3', 'Kullanıcı tarafından manuel olarak alarm kontrolü kapatıldı', 'Tehlike', 'sensor_type_3'));
            }

            return Promise.all(logPromises);
        })
        .then((result) => {
            if (!result) return; // Önceki if bloğunda return yapıldıysa
            if (result.length > 0) {
                showToast('Alarmlar güncellendi', 'success');
            }
        })
        .catch((error) => {
            console.error('Alarmlar güncellenirken hata:', error);
            showToast('Alarmlar güncellenirken bir hata oluştu', 'danger');
        });
}

// Log kaydı oluştur
function createLogEntry(triggeredOutput, description, alarmType, sensorId) {
    const logData = {
        alarm_description: description,
        alarm_type: alarmType,
        created_at: firebase.database.ServerValue.TIMESTAMP,
        device_id: 'device1',
        sensor_id: sensorId,
        threshold: 0,
        triggered_output: triggeredOutput,
        value: 0
    };

    return alarmsLogRef.push(logData);
}

// Toast bildirimi göster
function showToast(message, type = 'info') {
    // Toast kuyruğunu oluştur (eğer yoksa)
    if (!window.toastQueue) {
        window.toastQueue = [];
        window.isShowingToast = false;
    }

    // Yeni toast bildirimini kuyruğa ekle
    window.toastQueue.push({ message, type });

    // Eğer başka bir toast gösterilmiyorsa, kuyruktaki toastları göstermeye başla
    if (!window.isShowingToast) {
        processToastQueue();
    }
}

// Toast kuyruğundaki bildirimleri işle
function processToastQueue() {
    if (!window.toastQueue || window.toastQueue.length === 0) {
        window.isShowingToast = false;
        return;
    }

    window.isShowingToast = true;

    // Kuyruktaki ilk toastı al
    const { message, type } = window.toastQueue.shift();

    // Toast container oluştur veya var olanı kullan
    const toastContainer = document.getElementById('toast-container') || createToastContainer();

    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'warning' ? 'fa-exclamation-triangle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.innerHTML += toastHtml;

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        delay: 2500,
        autohide: true
    });
    toast.show();

    // Toast kapandığında DOM'dan kaldır ve sonraki toastı göster
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();

        // Bu toast kapandıktan sonra kuyruktaki diğer toastları göster
        setTimeout(() => {
            processToastQueue();
        }, 300); // Toastlar arasında küçük bir gecikme
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

// Device outputs kontrolü
function checkFirstAlarmAndStatus(deviceData) {
    // Sistem kontrol butonları her zaman aktif olacak
    setSystemControlButtonsEnabled(true);

    // Mevcut sistem modunu güncelle
    systemControlRef.once('value').then(snapshot => {
        const systemControlData = snapshot.val() || {};

        // Kullanıcı modunda ise her bir alarm türünü ayrı kontrol et
        if (systemControlData.user_intervention) {
            // Tüm kontrolleri önce aktif et
            enableAllControls();

            // Buzzer - output_type_1
            if (deviceData.output_type_1 && deviceData.output_type_1.first_alarm === true) {
                disableSpecificControl('buzzer', "Öncelikle bu alarm çalması gerekiyor");
            }

            // Uyarı Işığı - output_type_2
            if (deviceData.output_type_2 && deviceData.output_type_2.first_alarm === true) {
                disableSpecificControl('led', "Öncelikle bu alarm çalması gerekiyor");
            }

            // Pencere Durumu - output_type_3
            if (deviceData.output_type_3 && deviceData.output_type_3.first_alarm === true) {
                disableSpecificControl('servo', "Öncelikle bu alarm çalması gerekiyor");
            }
        }
    });
}

// Belirli bir kontrol öğesini devre dışı bırak
function disableSpecificControl(type, message) {
    const toggle = document.getElementById(`${type}AlarmToggle`);

    if (toggle) {
        toggle.classList.add('disabled');
        toggle.style.opacity = '0.35';
        toggle.style.filter = 'grayscale(90%)';
        toggle.style.cursor = 'not-allowed';
        toggle.title = message;
        toggle.onclick = function(e) {
            e.preventDefault();
            showToast(message, 'warning');
        };
    }
}

// Sistem kontrol butonlarını devre dışı bırak/aktif et
function setSystemControlButtonsEnabled(enabled) {
    // Artık tüm butonlar her zaman aktif olacak - bu fonksiyon kullanılmayacak
    // Ancak eski kodu silmek yerine, tüm butonları aktif etmek için kullanacağız
    const autoSystemBtn = document.getElementById('autoSystemBtn');
    const manualSystemBtn = document.getElementById('manualSystemBtn');
    const silentModeBtn = document.getElementById('silentModeBtn');
    const phoneVibrateBtn = document.getElementById('phoneVibrateBtn');

    [autoSystemBtn, manualSystemBtn, silentModeBtn, phoneVibrateBtn].forEach(btn => {
            btn.classList.remove('disabled');
            btn.style.opacity = '1';
            btn.title = '';
    });
}

// Sayfa yüklendiğinde ve device_outputs değiştiğinde kontrol et
function setupDeviceOutputsListener() {
    deviceRef.on('value', function(snapshot) {
        const deviceData = snapshot.val();
        if (deviceData) {
            // Her değişiklikte alarm durumlarını güncelle
            // Buzzer
            if (deviceData.output_type_1) {
                const isActive = deviceData.output_type_1.status === 1;
                updateStatusBadge('buzzerStatusBadge', isActive);
                updateToggleState('buzzer', isActive);
            }

            // Uyarı Işığı
            if (deviceData.output_type_2) {
                const isActive = deviceData.output_type_2.status === 1;
                updateStatusBadge('ledStatusBadge', isActive);
                updateToggleState('led', isActive);
            }

            // Pencere Durumu
            if (deviceData.output_type_3) {
                const isActive = deviceData.output_type_3.status === 1;
                updateStatusBadge('servoStatusBadge', isActive);
                updateToggleState('servo', isActive);
            }

            // First alarm durumunu ve sistem modunu kontrol et
            checkFirstAlarmAndStatus(deviceData);
        } else {
            setSystemControlButtonsEnabled(false);
        }
    });
}

// Telefon titreşim butonunun durumunu güncelle (enabled/disabled)
function updatePhoneVibrateButtonState(silentModeActive) {
    const phoneVibrateBtn = document.getElementById('phoneVibrateBtn');

    if (silentModeActive) {
        // Sessiz mod aktifse, telefon titreşim butonu aktif olsun
        phoneVibrateBtn.classList.remove('disabled');
        phoneVibrateBtn.style.opacity = '1';
        phoneVibrateBtn.style.filter = 'none';
        phoneVibrateBtn.style.cursor = 'pointer';
        phoneVibrateBtn.title = '';
    } else {
        // Sessiz mod pasifse, telefon titreşim butonu pasif olsun
        phoneVibrateBtn.classList.add('disabled');
        phoneVibrateBtn.style.opacity = '0.35';
        phoneVibrateBtn.style.filter = 'grayscale(90%)';
        phoneVibrateBtn.style.cursor = 'not-allowed';
        phoneVibrateBtn.title = 'Telefon titreşimini kullanmak için önce Sessiz Modu aktif edin';

        // Eğer telefon titreşimi aktifse ve sessiz mod pasif olduysa, telefon titreşimini de kapat
        systemControlRef.once('value').then((snapshot) => {
            const data = snapshot.val() || {};
            if (data.phone_vibrate) {
                systemControlRef.update({
                    phone_vibrate: false
                }).then(() => {
                    updatePhoneVibrateButton(false);
                });
            }
        });
    }
}

// Telefon titreşimini toggle et
function togglePhoneVibrate() {
    // Önce sessiz modu kontrol et
    systemControlRef.once('value').then((snapshot) => {
        const data = snapshot.val() || {};

        // Sessiz mod pasifse işlem yapma
        if (!data.silent_mode) {
            showToast('Telefon titreşimini kullanmak için önce Sessiz Modu aktif edin', 'warning');
            return;
        }

        // Global değişken ile tıklama durumunu kontrol et
        if (window.isProcessingPhoneVibrate) {
            console.log("İşlem halen devam ediyor, lütfen bekleyin");
            return;
        }

        // İşlem başladığını işaretle
        window.isProcessingPhoneVibrate = true;

        // Bekliyor durumunu göster
        const phoneVibrateBtn = document.getElementById('phoneVibrateBtn');
        phoneVibrateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>İşleniyor...</span>';

        const newValue = !data.phone_vibrate;

        systemControlRef.update({
            phone_vibrate: newValue
        }).then(() => {
            // UI'ı güncelle
            updatePhoneVibrateButton(newValue);

            // Kullanıcıya bildirim ver
            showToast(`Telefon titreşimi ${newValue ? 'aktif' : 'pasif'} olarak ayarlandı.`, 'success');

            // İşlem bitti
            setTimeout(() => {
                window.isProcessingPhoneVibrate = false;
            }, 300);

        }).catch((error) => {
            console.error("Telefon titreşimi güncellenirken hata oluştu:", error);
            showToast('Telefon titreşimi güncellenirken bir hata oluştu.', 'danger');
            resetPhoneVibrateButton(data.phone_vibrate);
            window.isProcessingPhoneVibrate = false;
        });
    }).catch((error) => {
        console.error("Sistem kontrol verisi alınırken hata oluştu:", error);
        showToast('Sistem kontrol verisi alınırken bir hata oluştu.', 'danger');
        resetPhoneVibrateButton(false);
        window.isProcessingPhoneVibrate = false;
    });
}

// Telefon titreşim butonunu güncelle
function updatePhoneVibrateButton(isActive) {
    const phoneVibrateBtn = document.getElementById('phoneVibrateBtn');

    // Butonu aktif/pasif yap
    phoneVibrateBtn.classList.toggle('active', isActive);

    // İçeriğini güncelle
    phoneVibrateBtn.innerHTML = `
        <i class="fas fa-mobile-alt me-2"></i>
        <span>${isActive ? 'Telefon Titreşimi Aktif' : 'Telefon Titreşimi'}</span>
    `;

    // Aktifse hafif titreşim efekti ver
    if (isActive) {
        // Animasyonu kaldır
        phoneVibrateBtn.style.animation = 'none';
        // Zorla reflow
        void phoneVibrateBtn.offsetWidth;
        // Kısa bir animasyon uygula ve sonra kaldır
        phoneVibrateBtn.style.animation = 'vibration 0.5s ease';

        // Animasyonu süre sonunda temizle
        setTimeout(() => {
            phoneVibrateBtn.style.animation = '';
        }, 500);
    } else {
        // Pasif ise animasyonları temizle
        phoneVibrateBtn.style.animation = '';
    }
}
</script>
@endpush
@endsection
