@extends('panel.layouts.app')
@section('title', 'Alarm Geçmişi')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Alarm Geçmişi</h1>
                <div>
                    <button id="refreshButton" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Yenile
                    </button>
                    <button id="exportButton" class="btn btn-success">
                        <i class="fas fa-file-export"></i> Dışa Aktar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="startDate" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control mb-2" id="startDate">
                            <input type="time" class="form-control" id="startTime" value="00:00">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="endDate" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control mb-2" id="endDate">
                            <input type="time" class="form-control" id="endTime" value="23:59">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="alarmType" class="form-label">Alarm Tipi</label>
                            <select class="form-select" id="alarmType">
                                <option value="all">Tümü</option>
                                <option value="sıcaklık">Sıcaklık</option>
                                <option value="nem">Nem</option>
                                <option value="karbonmonoksit">Karbonmonoksit</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="alarmStatus" class="form-label">Durum</label>
                            <select class="form-select" id="alarmStatus">
                                <option value="all">Tümü</option>
                                <option value="aktif">Aktif</option>
                                <option value="pasif">Pasif</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button id="filterButton" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filtrele
                            </button>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button id="resetButton" class="btn btn-secondary w-100">
                                <i class="fas fa-undo"></i> Sıfırla
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alarm Tablosu -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tarih</th>
                                    <th>Alarm Tipi</th>
                                    <th>Açıklama</th>
                                    <th>Değer</th>
                                    <th>Eşik Değeri</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody id="alarmsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Yükleniyor...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="d-flex align-items-center">
                            <label for="itemsPerPage" class="me-2">Sayfa başına:</label>
                            <select id="itemsPerPage" class="form-select form-select-sm" style="width: 70px;">
                                <option value="10">10</option>
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0" id="pagination">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.table th {
    font-weight: 600;
}

.badge {
    padding: 0.5em 0.75em;
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

.pagination .page-link {
    border-radius: 5px;
    margin: 0 2px;
}
</style>

@push('scripts')
<!-- Firebase SDK -->
<script>

// Firebase referanslarını al
const alarmsRef = firebase.database().ref('alarms_log');

// Sayfalama için değişkenler
let currentPage = 1;
let itemsPerPage = 25;
let allAlarms = [];
let filteredAlarms = [];

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Tarih filtrelerini bugünün tarihi ile doldur
    const today = new Date();
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    document.getElementById('startDate').value = formatDateForInput(thirtyDaysAgo);
    document.getElementById('endDate').value = formatDateForInput(today);
    
    // Alarmları yükle
    loadAlarms();
    
    // Filtre butonuna tıklama olayı
    document.getElementById('filterButton').addEventListener('click', function() {
        currentPage = 1; // Filtreleme yapıldığında ilk sayfaya dön
        loadAlarms();
    });
    
    // Sıfırla butonuna tıklama olayı
    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('startDate').value = formatDateForInput(thirtyDaysAgo);
        document.getElementById('endDate').value = formatDateForInput(today);
        document.getElementById('startTime').value = '00:00';
        document.getElementById('endTime').value = '23:59';
        document.getElementById('alarmType').value = 'all';
        document.getElementById('alarmStatus').value = 'all';
        currentPage = 1; // Sıfırlama yapıldığında ilk sayfaya dön
        loadAlarms();
    });
    
    // Yenile butonuna tıklama olayı
    document.getElementById('refreshButton').addEventListener('click', function() {
        loadAlarms();
    });
    
    // Dışa aktar butonuna tıklama olayı
    document.getElementById('exportButton').addEventListener('click', function() {
        exportAlarmsToCSV();
    });
    
    // Sayfa başına öğe sayısı değiştiğinde
    document.getElementById('itemsPerPage').addEventListener('change', function() {
        itemsPerPage = parseInt(this.value);
        currentPage = 1; // Sayfa başına öğe sayısı değiştiğinde ilk sayfaya dön
        displayAlarms(filteredAlarms);
    });
});

// Tarihi input için formatla (YYYY-MM-DD)
function formatDateForInput(date) {
    return date.getFullYear() + '-' + 
           String(date.getMonth() + 1).padStart(2, '0') + '-' + 
           String(date.getDate()).padStart(2, '0');
}

// Tarihi görüntüleme için formatla (DD.MM.YYYY HH:MM:SS)
function formatDateForDisplay(timestamp) {
    if (!timestamp) return '-';
    
    const date = new Date(timestamp * 1000); // Unix timestamp'i milisaniyeye çevir
    return String(date.getDate()).padStart(2, '0') + '.' + 
           String(date.getMonth() + 1).padStart(2, '0') + '.' + 
           date.getFullYear() + ' ' + 
           String(date.getHours()).padStart(2, '0') + ':' + 
           String(date.getMinutes()).padStart(2, '0') + ':' + 
           String(date.getSeconds()).padStart(2, '0');
}

// Tarih ve saat birleştirme
function combineDateAndTime(dateStr, timeStr) {
    if (!dateStr || !timeStr) return null;
    
    const [hours, minutes] = timeStr.split(':').map(Number);
    const date = new Date(dateStr);
    date.setHours(hours, minutes, 0, 0);
    return date.getTime() / 1000; // Unix timestamp'e çevir
}

// Alarmları yükle
function loadAlarms() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const alarmType = document.getElementById('alarmType').value;
    const alarmStatus = document.getElementById('alarmStatus').value;
    
    // Tablo gövdesini temizle
    const tableBody = document.getElementById('alarmsTableBody');
    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Yükleniyor...</td></tr>';
    
    // Firebase'den alarmları al
    alarmsRef.once('value')
        .then((snapshot) => {
            const alarmsData = snapshot.val();
            if (alarmsData) {
                // Alarmları diziye dönüştür
                allAlarms = [];
                
                // Her bir alarm kaydını işle
                Object.entries(alarmsData).forEach(([key, value]) => {
                    // Her bir alarm kaydını işle
                    let alarmData = null;
                    
                    // İlk durum: doğrudan alarm verileri mevcut (manuel kontrol veya eski format)
                    if (value.alarm_description && value.alarm_type) {
                        alarmData = {
                            id: key,
                            alarm_type: value.alarm_type || 'Bilinmiyor',
                            alarm_description: value.alarm_description || '',
                            value: value.value || 0,
                            threshold: value.threshold || 0,
                            created_at: value.created_at || 0,
                            status: value.alarm_type || 'Bilinmiyor',
                            sensor_id: value.sensor_id || '',
                            is_alarm_acknowledged: value.is_alarm_acknowledged || false,
                            device_id: value.device_id || ''
                        };
                        
                        // created_at milisaniye formatındaysa saniyeye çevir (13 haneli timestamp)
                        if (alarmData.created_at > 9999999999) {
                            alarmData.created_at = Math.floor(alarmData.created_at / 1000);
                        }
                    
                    // Manuel kontrol kayıtları için özel işlem
                        if (alarmData.alarm_description.includes('manuel olarak')) {
                            alarmData.is_manual = true;
                    } else {
                            alarmData.is_manual = false;
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
                            sensor_id: sensorData.sensor_id || 'sensor_type_3',
                            is_alarm_acknowledged: sensorData.is_alarm_acknowledged || false,
                            device_id: sensorData.device_id || '',
                            is_manual: false
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
                            sensor_id: sensorData.sensor_id || 'sensor_type_1',
                            is_alarm_acknowledged: sensorData.is_alarm_acknowledged || false,
                            device_id: sensorData.device_id || '',
                            is_manual: false
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
                            sensor_id: sensorData.sensor_id || 'sensor_type_2',
                            is_alarm_acknowledged: sensorData.is_alarm_acknowledged || false,
                            device_id: sensorData.device_id || '',
                            is_manual: false
                        };
                    }
                    
                    // Eğer alarm verisi başarıyla oluşturulduysa diziye ekle
                    if (alarmData) {
                        allAlarms.push(alarmData);
                    }
                });
                
                // Tarihe göre filtrele
                const startTimestamp = combineDateAndTime(startDate, startTime);
                const endTimestamp = combineDateAndTime(endDate, endTime);
                
                filteredAlarms = allAlarms.filter(alarm => {
                    const time = alarm.created_at || 0;
                    return time >= startTimestamp && time <= endTimestamp;
                });
                
                // Alarm tipine göre filtrele
                if (alarmType !== 'all') {
                    filteredAlarms = filteredAlarms.filter(alarm => {
                        if (alarmType === 'sıcaklık') {
                            return alarm.sensor_id === 'sensor_type_1' || 
                                  (alarm.alarm_type && alarm.alarm_type.toLowerCase().includes('sıcak'));
                        } else if (alarmType === 'nem') {
                            return alarm.sensor_id === 'sensor_type_2' || 
                                  (alarm.alarm_type && alarm.alarm_type.toLowerCase().includes('nem'));
                        } else if (alarmType === 'karbonmonoksit') {
                            return alarm.sensor_id === 'sensor_type_3' || 
                                  (alarm.alarm_type && (alarm.alarm_type.toLowerCase().includes('karbon') || 
                                                     alarm.alarm_type.toLowerCase().includes('co')));
                        }
                        return true;
                    });
                }
                
                // Alarm durumuna göre filtrele
                if (alarmStatus !== 'all') {
                    filteredAlarms = filteredAlarms.filter(alarm => {
                        if (alarmStatus === 'aktif') {
                            // Manuel kontrollerde açıklama içeriğine göre durumu belirle
                            if (alarm.is_manual) {
                                return alarm.alarm_description.includes('açıldı') || 
                                       alarm.status === 'Aktif' || 
                                       alarm.status === 'Normal';
                            }
                            // Diğer alarmlarda is_alarm_acknowledged değerine bak
                            return !alarm.is_alarm_acknowledged;
                        } else if (alarmStatus === 'pasif') {
                            // Manuel kontrollerde açıklama içeriğine göre durumu belirle
                            if (alarm.is_manual) {
                                return alarm.alarm_description.includes('kapatıldı') || 
                                       alarm.status === 'Pasif';
                            }
                            // Diğer alarmlarda is_alarm_acknowledged değerine bak
                            return alarm.is_alarm_acknowledged;
                        }
                        return true;
                    });
                }
                
                // Tarihe göre sırala (en yeni en üstte)
                filteredAlarms.sort((a, b) => b.created_at - a.created_at);
                
                // Sıra numaralarını güncelle
                filteredAlarms.forEach((alarm, index) => {
                    alarm.displayId = filteredAlarms.length - index;
                });
                
                // Tabloyu doldur
                displayAlarms(filteredAlarms);
            } else {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Alarm kaydı bulunamadı</td></tr>';
                document.getElementById('pagination').innerHTML = '';
            }
        })
        .catch((error) => {
            console.error('Alarmlar yüklenirken hata:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Alarmlar yüklenirken bir hata oluştu</td></tr>';
            document.getElementById('pagination').innerHTML = '';
            showToast('Alarmlar yüklenirken bir hata oluştu', 'danger');
        });
}

// Alarmları tabloda göster
function displayAlarms(alarms) {
    const tableBody = document.getElementById('alarmsTableBody');
    
    if (alarms.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Alarm kaydı bulunamadı</td></tr>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    
    // Sayfalama hesapla
    const totalPages = Math.ceil(alarms.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, alarms.length);
    const currentPageAlarms = alarms.slice(startIndex, endIndex);
    
    // Tabloyu doldur
    let html = '';
    
    currentPageAlarms.forEach(alarm => {
        // Alarm tipini belirle
        let alarmType = alarm.alarm_type || 'Bilinmiyor';
        let alarmTypeClass = 'bg-secondary';
        
        // Sensör tipine ve alarm tipine göre badge rengi belirle
        if (alarm.sensor_id === 'sensor_type_3' || alarmType.toLowerCase().includes('karbon') || alarmType.toLowerCase().includes('co')) {
            alarmTypeClass = 'bg-warning text-dark';
            alarmType = 'Karbonmonoksit';
        } else if (alarm.sensor_id === 'sensor_type_1' || alarmType.toLowerCase().includes('sıcak')) {
            alarmTypeClass = 'bg-danger';
            alarmType = 'Sıcaklık';
        } else if (alarm.sensor_id === 'sensor_type_2' || alarmType.toLowerCase().includes('nem')) {
            alarmTypeClass = 'bg-info';
            alarmType = 'Nem';
        } else if (alarm.is_manual) {
            alarmTypeClass = 'bg-primary';
            alarmType = 'Manuel Kontrol';
        }
        
        // Alarm tipine göre farklı renkler
        if (alarmType.toLowerCase() === 'normal') {
            alarmTypeClass = 'bg-success';
        } else if (alarmType.toLowerCase() === 'tehlike') {
            alarmTypeClass = 'bg-danger';
        }
        
        // Alarm durumuna göre badge
        let statusText = alarm.status || 'Bilinmiyor';
        let statusClass = 'bg-secondary';
        
        // Manuel kontroller için durum belirleme
        if (alarm.is_manual) {
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
        if (alarm.is_alarm_acknowledged) {
                statusText = 'Çözüldü';
                statusClass = 'bg-success';
            } else {
                if (statusText.toLowerCase() === 'normal') {
                    statusClass = 'bg-success';
                } else if (statusText.toLowerCase() === 'tehlike' || 
                           statusText.toLowerCase() === 'uyarı') {
                    statusClass = 'bg-danger';
                } else if (statusText.toLowerCase() === 'aktif') {
                    statusClass = 'bg-success';
                } else if (statusText.toLowerCase() === 'pasif') {
                    statusClass = 'bg-danger';
        } else {
                    statusText = 'Aktif';
                    statusClass = 'bg-danger';
                }
            }
        }
        
        // Değer ve eşik değeri gösterimi
        let valueDisplay = '-';
        let thresholdDisplay = '-';
        
        if (!alarm.is_manual) {
            // Sensör tipine göre birim belirle
            let unit = '';
            if (alarm.sensor_id === 'sensor_type_3' || alarmType.toLowerCase() === 'karbonmonoksit') {
                unit = 'ppm';
            } else if (alarm.sensor_id === 'sensor_type_1' || alarmType.toLowerCase() === 'sıcaklık') {
                unit = '°C';
            } else if (alarm.sensor_id === 'sensor_type_2' || alarmType.toLowerCase() === 'nem') {
                unit = '%';
            }
            
            // Değerleri göster
            if (alarm.value) {
                valueDisplay = `${parseFloat(alarm.value).toFixed(1)} ${unit}`;
            }
            
            if (alarm.threshold) {
                thresholdDisplay = `${parseFloat(alarm.threshold).toFixed(1)} ${unit}`;
            }
        }
        
        // Açıklama
        const description = getAlarmDescription(alarm);
        
        html += `
            <tr>
                <td>${alarm.displayId}</td>
                <td>${formatDateForDisplay(alarm.created_at)}</td>
                <td><span class="badge ${alarmTypeClass}">${alarmType}</span></td>
                <td>${description}</td>
                <td>${valueDisplay}</td>
                <td>${thresholdDisplay}</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
    
    // Sayfalama oluştur
    generatePagination(totalPages);
}

// Sayfalama oluştur
function generatePagination(totalPages) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    if (totalPages <= 1) {
        return;
    }
    
    // Önceki sayfa butonu
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">
            <span aria-hidden="true">&laquo;</span>
        </a>
    `;
    pagination.appendChild(prevLi);
    
    // Sayfa numaraları
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // İlk sayfa
    if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.className = 'page-item';
        firstLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
        pagination.appendChild(firstLi);
        
        if (startPage > 2) {
            const ellipsisLi = document.createElement('li');
            ellipsisLi.className = 'page-item disabled';
            ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
            pagination.appendChild(ellipsisLi);
        }
    }
    
    // Sayfa numaraları
    for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement('li');
        pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
        pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
        pagination.appendChild(pageLi);
    }
    
    // Son sayfa
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const ellipsisLi = document.createElement('li');
            ellipsisLi.className = 'page-item disabled';
            ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
            pagination.appendChild(ellipsisLi);
        }
        
        const lastLi = document.createElement('li');
        lastLi.className = 'page-item';
        lastLi.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>`;
        pagination.appendChild(lastLi);
    }
    
    // Sonraki sayfa butonu
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">
            <span aria-hidden="true">&raquo;</span>
        </a>
    `;
    pagination.appendChild(nextLi);
    
    // Sayfa değiştirme olaylarını ekle
    pagination.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute('data-page'));
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                currentPage = page;
                displayAlarms(filteredAlarms);
            }
        });
    });
}

// Alarmları CSV olarak dışa aktar
function exportAlarmsToCSV() {
    // Mevcut filtrelenmiş alarmları kullan
    if (filteredAlarms.length === 0) {
        showToast('Dışa aktarılacak alarm bulunamadı', 'warning');
        return;
    }
    
    try {
        // CSV başlık satırı
        let csvContent = 'ID,Tarih,Alarm Tipi,Açıklama,Değer,Eşik Değeri,Durum,Cihaz ID\n';
        
        // Her alarm için CSV satırı oluştur
        filteredAlarms.forEach(alarm => {
            // Tarih formatla
            const formattedDate = formatDateForDisplay(alarm.created_at);
                    
                    // Alarm tipini belirle
            let alarmType = alarm.alarm_type || 'Bilinmiyor';
            if (alarm.sensor_id === 'sensor_type_3' || alarmType.toLowerCase().includes('karbon') || alarmType.toLowerCase().includes('co')) {
                alarmType = 'Karbonmonoksit';
            } else if (alarm.sensor_id === 'sensor_type_1' || alarmType.toLowerCase().includes('sıcak')) {
                alarmType = 'Sıcaklık';
            } else if (alarm.sensor_id === 'sensor_type_2' || alarmType.toLowerCase().includes('nem')) {
                alarmType = 'Nem';
            } else if (alarm.is_manual) {
                alarmType = 'Manuel Kontrol';
            }
            
            // Durum metnini belirle
            let statusText = alarm.status || 'Bilinmiyor';
            if (alarm.is_manual) {
                if (alarm.alarm_description.includes('açıldı') || alarm.alarm_description.includes('aktif')) {
                    statusText = 'Aktif';
                } else if (alarm.alarm_description.includes('kapatıldı') || alarm.alarm_description.includes('pasif')) {
                    statusText = 'Pasif';
                }
            } else if (alarm.is_alarm_acknowledged) {
                statusText = 'Çözüldü';
            } else if (statusText.toLowerCase() === 'normal') {
                statusText = 'Normal';
            } else if (statusText.toLowerCase() === 'tehlike' || statusText.toLowerCase() === 'uyarı') {
                statusText = 'Tehlike';
            }
            
            // Değerleri belirle
            let valueDisplay = '';
            let thresholdDisplay = '';
            
            if (!alarm.is_manual) {
                // Sensör tipine göre birim belirle
                let unit = '';
                if (alarm.sensor_id === 'sensor_type_3' || alarmType === 'Karbonmonoksit') {
                    unit = 'ppm';
                } else if (alarm.sensor_id === 'sensor_type_1' || alarmType === 'Sıcaklık') {
                    unit = '°C';
                } else if (alarm.sensor_id === 'sensor_type_2' || alarmType === 'Nem') {
                    unit = '%';
                }
                
                if (alarm.value) {
                    valueDisplay = `${parseFloat(alarm.value).toFixed(1)} ${unit}`;
                }
                
                if (alarm.threshold) {
                    thresholdDisplay = `${parseFloat(alarm.threshold).toFixed(1)} ${unit}`;
                }
            }
            
            // Alarm açıklaması
            const description = getAlarmDescription(alarm);
            
            // Virgüller ve tırnak işaretleri için CSV'yi kaçırma işlemi
            const escapeCsv = (text) => {
                if (!text) return '';
                // Çift tırnak içine al ve içerideki çift tırnak karakterlerini ikiye katla
                return `"${String(text).replace(/"/g, '""')}"`;
            };
                    
                    // CSV satırı
                    const row = [
                        alarm.displayId,
                escapeCsv(formattedDate),
                escapeCsv(alarmType),
                escapeCsv(description),
                escapeCsv(valueDisplay),
                escapeCsv(thresholdDisplay),
                escapeCsv(statusText),
                escapeCsv(alarm.device_id || '')
                    ];
                    
                    csvContent += row.join(',') + '\n';
                });
        
        // Bugünün tarihi için dosya adı oluştur
        const today = new Date();
        const dateStr = today.toISOString().slice(0, 10);
                
                // CSV dosyasını indir
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
        link.setAttribute('download', `alarmlar_${dateStr}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showToast('Alarmlar başarıyla dışa aktarıldı', 'success');
    } catch (error) {
            console.error('Alarmlar dışa aktarılırken hata:', error);
            showToast('Alarmlar dışa aktarılırken bir hata oluştu', 'danger');
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

// Alarm açıklamasını almak için yardımcı fonksiyon
function getAlarmDescription(alarm) {
    // İlk olarak doğrudan alarm_description alanını kontrol et
    if (alarm.alarm_description) {
        return alarm.alarm_description;
    }
    
    // Alt nesnelerde kontrol et
    if (alarm.karbonmonoksit_alarm && alarm.karbonmonoksit_alarm.alarm_description) {
        return alarm.karbonmonoksit_alarm.alarm_description;
    }
    
    if (alarm.nem_alarm && alarm.nem_alarm.alarm_description) {
        return alarm.nem_alarm.alarm_description;
    }
    
    if (alarm.sicaklik_alarm && alarm.sicaklik_alarm.alarm_description) {
        return alarm.sicaklik_alarm.alarm_description;
    }
    
    // Hiçbir açıklama bulunamadı
    return '-';
}
</script>
@endpush
@endsection 