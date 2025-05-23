@extends('panel.layouts.app')

@section('title', 'Ölçüm Geçmişi')

@section('content')
    <!-- Main Content -->
    <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4">
        <div class="content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Ölçümler</h1>
                <div class="time-display">
                    <i class="far fa-clock me-2"></i><span id="current-time"></span>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card summary-card temperature">
                        <div class="card-body">
                            <div class="icon text-danger">
                                <i class="fas fa-temperature-high"></i>
                            </div>
                            <div class="text">
                                <div class="title">Sıcaklık</div>
                                <div class="value" id="latest-temperature">--</div>
                                <div class="unit">°C</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card summary-card humidity">
                        <div class="card-body">
                            <div class="icon text-success">
                                <i class="fas fa-tint"></i>
                            </div>
                            <div class="text">
                                <div class="title">Nem</div>
                                <div class="value" id="latest-humidity">--</div>
                                <div class="unit">%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card summary-card co">
                        <div class="card-body">
                            <div class="icon text-warning">
                                <i class="fas fa-smog"></i>
                            </div>
                            <div class="text">
                                <div class="title">CO</div>
                                <div class="value" id="latest-co">--</div>
                                <div class="unit">ppm</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card summary-card total">
                        <div class="card-body">
                            <div class="icon text-success">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="text">
                                <div class="title">Toplam Ölçüm</div>
                                <div class="value" id="total-measurements">--</div>
                                <div class="unit">kayıt</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter"></i> Filtreler
                </div>
                <div class="card-body">
                    <form id="filter-form" class="filter-form">
                        <div class="form-group">
                            <label for="start-date">Başlangıç Tarihi</label>
                            <input type="datetime-local" class="form-control" id="start-date">
                        </div>
                        <div class="form-group">
                            <label for="end-date">Bitiş Tarihi</label>
                            <input type="datetime-local" class="form-control" id="end-date">
                        </div>
                        <div class="form-group">
                            <label for="measurement-type">Ölçüm Tipi</label>
                            <select class="form-control" id="measurement-type">
                                <option value="all">Tümü</option>
                                <option value="temperature">Sıcaklık</option>
                                <option value="humidity">Nem</option>
                                <option value="co">CO</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary w-100" id="filter-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Seçilen filtrelere göre verileri getir">
                                <i class="fas fa-search"></i> Filtrele
                            </button>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary w-100" id="clear-filters">
                                <i class="fas fa-times"></i> Filtreleri Temizle
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Charts Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i> Grafikler
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="chartTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="combined-tab" data-bs-toggle="tab" data-bs-target="#combined" type="button" role="tab">
                                <i class="fas fa-layer-group me-2"></i>Birleşik Grafik
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="temperature-tab" data-bs-toggle="tab" data-bs-target="#temperature" type="button" role="tab">
                                <i class="fas fa-temperature-high me-2"></i>Sıcaklık
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="humidity-tab" data-bs-toggle="tab" data-bs-target="#humidity" type="button" role="tab">
                                <i class="fas fa-tint me-2 text-success"></i>Nem
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="co-tab" data-bs-toggle="tab" data-bs-target="#co" type="button" role="tab">
                                <i class="fas fa-smog me-2"></i>CO
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="chartTabsContent">
                        <div class="tab-pane fade show active" id="combined" role="tabpanel">
                            <div class="chart-container">
                                <canvas id="combinedChart"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="temperature" role="tabpanel">
                            <div class="chart-container">
                                <canvas id="temperatureChart"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="humidity" role="tabpanel">
                            <div class="chart-container">
                                <canvas id="humidityChart"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="co" role="tabpanel">
                            <div class="chart-container">
                                <canvas id="coChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Panel -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table"></i> Ölçüm Verileri
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" id="export-csv">
                            <i class="fas fa-file-csv me-1"></i> CSV İndir
                        </button>
                        <button class="refresh-btn" id="refresh-data">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="measurements-table">
                            <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Tip</th>
                                <th>Değer</th>
                                <th>Birim</th>
                            </tr>
                            </thead>
                            <tbody id="measurements-table-body">
                            <tr>
                                <td colspan="4" class="text-center">Veriler yükleniyor...</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>



        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            color: var(--text-primary);
            line-height: 1.6;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--text-primary);
        }


        /* Container and Row Adjustments */
        .container-fluid {
            padding: 0;
            margin: 0;
            width: 100%;
        }

        .row {
            margin: 0;
            width: 100%;
        }

        /* Content Area */
        .content {
            padding: 1.5rem;
            background-color: #f8f9fc;
            min-height: calc(100vh - 50px);
        }

        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .card-body {
            padding: 1.25rem;
        }

        .summary-card {
            border-left: 0.25rem solid;
            height: 100%;
        }

        .summary-card.temperature {
            border-left-color: var(--danger-color);
        }

        .summary-card.humidity {
            border-left-color: var(--success-color);
        }

        .summary-card.co {
            border-left-color: var(--warning-color);
        }

        .summary-card.total {
            border-left-color: var(--success-color);
        }

        .summary-card .card-body {
            display: flex;
            align-items: center;
        }

        .summary-card .icon {
            font-size: 2rem;
            margin-right: 1rem;
            opacity: 0.4;
        }

        .summary-card .text {
            flex-grow: 1;
        }

        .summary-card .title {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .summary-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .summary-card .unit {
            font-size: 0.8rem;
            color: var(--secondary-color);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-form .form-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-form .form-control {
            height: 38px;
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .filter-form .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .filter-form .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            font-weight: 600;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.2s;
        }

        .filter-form .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .filter-form .btn-secondary {
            background-color: #858796;
            border-color: #858796;
            font-weight: 600;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.2s;
        }

        .filter-form .btn-secondary:hover {
            background-color: #717384;
            border-color: #6b6d7d;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .filter-form .btn {
            height: 38px;
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            border-radius: 0.35rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-form .btn i {
            margin-right: 0.5rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .table th {
            background-color: #f8f9fc;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 0.75rem;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            border-radius: 0.25rem;
        }

        .badge-temperature {
            background-color: var(--danger-color);
            color: white;
        }

        .badge-humidity {
            background-color: var(--success-color);
            color: white;
        }

        .badge-co {
            background-color: var(--warning-color);
            color: white;
        }

        .nav-tabs {
            border-bottom: 1px solid #e3e6f0;
            margin-bottom: 1rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            padding: 0.75rem 1rem;
            font-weight: 600;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            border-bottom: 2px solid rgba(78, 115, 223, 0.5);
        }
        .nav > li > a > i {
            padding-right:10px !important;
            color: white !important;
            text-align: center !important;
            align-items: center !important;
            align-content: center;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            background-color: transparent;
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 0.2em solid currentColor;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spinner-border .75s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }

        .time-display {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .refresh-btn {
            background-color: transparent;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .refresh-btn:hover {
            transform: rotate(180deg);
        }

        .refresh-btn i {
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .filter-form .form-group {
                width: 100%;
            }

            .filter-form .btn {
                width: 100%;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @push('scripts')
        <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
        <script>
            // Saat güncelleme
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('tr-TR');
                document.getElementById('current-time').textContent = timeString;
            }

            updateTime();
            setInterval(updateTime, 1000);

            // Toastr ayarları
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Grafik renkleri
            const chartColors = {
                temperature: {
                    backgroundColor: 'rgba(231, 74, 59, 0.2)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    pointBackgroundColor: 'rgba(231, 74, 59, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(231, 74, 59, 1)'
                },
                humidity: {
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(40, 167, 69, 1)'
                },
                co: {
                    backgroundColor: 'rgba(246, 194, 62, 0.2)',
                    borderColor: 'rgba(246, 194, 62, 1)',
                    pointBackgroundColor: 'rgba(246, 194, 62, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(246, 194, 62, 1)'
                }
            };

            // Grafikler
            let combinedChart = null;
            let temperatureChart = null;
            let humidityChart = null;
            let coChart = null;

            // Grafikleri oluştur
            function createCharts() {
                // Birleşik grafik
                const combinedCtx = document.getElementById('combinedChart').getContext('2d');
                combinedChart = new Chart(combinedCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: 'Sıcaklık (°C)',
                                data: [],
                                ...chartColors.temperature,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Nem (%)',
                                data: [],
                                ...chartColors.humidity,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'CO (ppm)',
                                data: [],
                                ...chartColors.co,
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1);
                                        }
                                        return label;
                                    }
                                }
                            },
                            zoom: {
                                pan: {
                                    enabled: true
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy'
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Tarih'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    callback: function(value, index, values) {
                                        return this.getLabelForValue(value);
                                    }
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Değer'
                                }
                            }
                        }
                    }
                });

                // Sıcaklık grafiği
                const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
                temperatureChart = new Chart(temperatureCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Sıcaklık (°C)',
                            data: [],
                            ...chartColors.temperature,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + ' °C';
                                        }
                                        return label;
                                    }
                                }
                            },
                            zoom: {
                                pan: {
                                    enabled: true
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy'
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Tarih'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Sıcaklık (°C)'
                                }
                            }
                        }
                    }
                });

                // Nem grafiği
                const humidityCtx = document.getElementById('humidityChart').getContext('2d');
                humidityChart = new Chart(humidityCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Nem (%)',
                            data: [],
                            ...chartColors.humidity,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + ' %';
                                        }
                                        return label;
                                    }
                                }
                            },
                            zoom: {
                                pan: {
                                    enabled: true
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy'
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Tarih'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Nem (%)'
                                }
                            }
                        }
                    }
                });

                // CO grafiği
                const coCtx = document.getElementById('coChart').getContext('2d');
                coChart = new Chart(coCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'CO (ppm)',
                            data: [],
                            ...chartColors.co,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + ' ppm';
                                        }
                                        return label;
                                    }
                                }
                            },
                            zoom: {
                                pan: {
                                    enabled: true
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy'
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Tarih'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'CO (ppm)'
                                }
                            }
                        }
                    }
                });
            }

            // Ölçümleri yükle
            function loadMeasurements() {
                // Yükleniyor göstergesi
                document.getElementById('measurements-table-body').innerHTML = '<tr><td colspan="4" class="text-center"><span class="loading-spinner"></span>Veriler yükleniyor...</td></tr>';

                // Filtre değerlerini al
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;
                const type = document.getElementById('measurement-type').value;

                console.log('Filtre değerleri:', {
                    startDate: startDate,
                    endDate: endDate,
                    type: type
                });

                // Tarih formatını düzelt (YYYY-MM-DDThh:mm -> YYYY-MM-DD hh:mm:ss)
                let formattedStartDate = '';
                let formattedEndDate = '';

                if (startDate) {
                    // datetime-local input'tan gelen değeri düzgün formata çevir
                    // Türkiye saat dilimine göre ayarla (UTC+3)
                    const startDateObj = new Date(startDate);
                    // Saat dilimi farkını ekle (3 saat)
                    startDateObj.setHours(startDateObj.getHours() + 3);
                    formattedStartDate = startDateObj.toISOString().slice(0, 19).replace('T', ' ');
                }

                if (endDate) {
                    // datetime-local input'tan gelen değeri düzgün formata çevir
                    // Türkiye saat dilimine göre ayarla (UTC+3)
                    const endDateObj = new Date(endDate);
                    // Saat dilimi farkını ekle (3 saat)
                    endDateObj.setHours(endDateObj.getHours() + 3);
                    formattedEndDate = endDateObj.toISOString().slice(0, 19).replace('T', ' ');
                }

                console.log('Formatlanmış tarihler:', {
                    formattedStartDate: formattedStartDate,
                    formattedEndDate: formattedEndDate
                });

                // API isteği
                fetch('/panel/measurements-history/data?' + new URLSearchParams({
                    start_date: formattedStartDate,
                    end_date: formattedEndDate,
                    type: type
                }))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('API yanıtı:', data);

                        if (data.success) {
                            if (data.data.length === 0) {
                                console.log('Veri bulunamadı - Filtre değerleri:', {
                                    startDate: formattedStartDate,
                                    endDate: formattedEndDate,
                                    type: type
                                });

                                let warningMessage = 'Seçilen filtreleme için veri bulunamadı.';
                                if (formattedStartDate) {
                                    warningMessage += `\nBaşlangıç tarihi: ${formattedStartDate}`;
                                }
                                if (formattedEndDate) {
                                    warningMessage += `\nBitiş tarihi: ${formattedEndDate}`;
                                }
                                if (type && type !== 'all') {
                                    warningMessage += `\nÖlçüm tipi: ${type}`;
                                }

                                toastr.warning(warningMessage);

                                // Tabloyu güncelle
                                document.getElementById('measurements-table-body').innerHTML = '<tr><td colspan="4" class="text-center">Veri bulunamadı</td></tr>';

                                // Grafikleri temizle
                                clearCharts();
                            } else {
                                console.log(`${data.data.length} adet veri bulundu`);

                                // Verileri tarihe göre sırala (en yeniden en eskiye)
                                data.data.sort((a, b) => b.timestamp - a.timestamp);

                                // En son ölçümleri bul
                                const latestTemperature = data.data.find(m => m.type === 'temperature');
                                const latestHumidity = data.data.find(m => m.type === 'humidity');
                                const latestCo = data.data.find(m => m.type === 'co');

                                console.log('En son ölçümler:', {
                                    latestTemperature: latestTemperature ? latestTemperature.value : '--',
                                    latestHumidity: latestHumidity ? latestHumidity.value : '--',
                                    latestCo: latestCo ? latestCo.value : '--'
                                });

                                // Özet bilgileri güncelle
                                document.getElementById('latest-temperature').textContent = latestTemperature ? latestTemperature.value.toFixed(1) : '--';
                                document.getElementById('latest-humidity').textContent = latestHumidity ? latestHumidity.value.toFixed(1) : '--';
                                document.getElementById('latest-co').textContent = latestCo ? latestCo.value.toFixed(1) : '--';
                                document.getElementById('total-measurements').textContent = data.data.length;

                                // Grafikleri güncelle
                                updateCharts(data.data);

                                // Tabloyu güncelle
                                updateTable(data.data);

                                // Başarı mesajı
                                toastr.success(`${data.data.length} ölçüm verisi başarıyla yüklendi.`);
                            }
                        } else {
                            // Hata mesajı
                            console.error('API hatası:', data.error);
                            toastr.error(data.error || 'Veriler yüklenirken bir hata oluştu.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Veriler yüklenirken bir hata oluştu: ' + error.message);

                        // Tabloyu güncelle
                        document.getElementById('measurements-table-body').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Veriler yüklenirken bir hata oluştu</td></tr>';

                        // Grafikleri temizle
                        clearCharts();
                    });
            }

            // Grafikleri güncelle
            function updateCharts(measurements) {
                // Verileri tarihe göre sırala
                measurements.sort((a, b) => a.timestamp - b.timestamp);

                // Her bir ölçüm tipi için ayrı veri setleri oluştur
                const temperatureData = [];
                const humidityData = [];
                const coData = [];
                const allDates = [];

                // Verileri ilgili dizilere yerleştir
                measurements.forEach(m => {
                    allDates.push(m.formatted_date);

                    if (m.type === 'temperature') {
                        temperatureData.push(m.value);
                    } else {
                        temperatureData.push(null);
                    }

                    if (m.type === 'humidity') {
                        humidityData.push(m.value);
                    } else {
                        humidityData.push(null);
                    }

                    if (m.type === 'co') {
                        coData.push(m.value);
                    } else {
                        coData.push(null);
                    }
                });

                // Birleşik grafik güncelleme
                combinedChart.data.labels = allDates;
                combinedChart.data.datasets[0].data = temperatureData;
                combinedChart.data.datasets[1].data = humidityData;
                combinedChart.data.datasets[2].data = coData;
                combinedChart.update();

                // Sıcaklık grafiği güncelleme
                temperatureChart.data.labels = allDates;
                temperatureChart.data.datasets[0].data = temperatureData;
                temperatureChart.update();

                // Nem grafiği güncelleme
                humidityChart.data.labels = allDates;
                humidityChart.data.datasets[0].data = humidityData;
                humidityChart.update();

                // CO grafiği güncelleme
                coChart.data.labels = allDates;
                coChart.data.datasets[0].data = coData;
                coChart.update();
            }

            // Tabloyu güncelle
            function updateTable(measurements) {
                const tableBody = document.getElementById('measurements-table-body');
                tableBody.innerHTML = '';

                if (measurements.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Veri bulunamadı</td></tr>';
                    return;
                }

                measurements.forEach(measurement => {
                    const row = document.createElement('tr');

                    // Tarih hücresi
                    const dateCell = document.createElement('td');
                    dateCell.textContent = measurement.formatted_date;
                    row.appendChild(dateCell);

                    // Tip hücresi
                    const typeCell = document.createElement('td');
                    let badgeClass = '';
                    let typeText = '';

                    switch (measurement.type) {
                        case 'temperature':
                            badgeClass = 'badge-temperature';
                            typeText = 'Sıcaklık';
                            break;
                        case 'humidity':
                            badgeClass = 'badge-humidity';
                            typeText = 'Nem';
                            break;
                        case 'co':
                            badgeClass = 'badge-co';
                            typeText = 'CO';
                            break;
                        default:
                            badgeClass = 'badge-secondary';
                            typeText = measurement.type || 'Bilinmeyen';
                    }

                    typeCell.innerHTML = `<span class="badge ${badgeClass}">${typeText}</span>`;
                    row.appendChild(typeCell);

                    // Değer hücresi
                    const valueCell = document.createElement('td');
                    valueCell.textContent = measurement.value.toFixed(1);
                    row.appendChild(valueCell);

                    // Birim hücresi
                    const unitCell = document.createElement('td');
                    unitCell.textContent = measurement.unit;
                    row.appendChild(unitCell);

                    tableBody.appendChild(row);
                });
            }

            // CSV olarak dışa aktar
            function exportToCSV() {
                // Yükleniyor göstergesi
                toastr.info('CSV dosyası hazırlanıyor...');

                // Filtre değerlerini al
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;
                const type = document.getElementById('measurement-type').value;

                // Tarih formatını düzelt (YYYY-MM-DDThh:mm -> YYYY-MM-DD hh:mm:ss)
                let formattedStartDate = '';
                let formattedEndDate = '';

                if (startDate) {
                    // datetime-local input'tan gelen değeri düzgün formata çevir
                    // Türkiye saat dilimine göre ayarla (UTC+3)
                    const startDateObj = new Date(startDate);
                    // Saat dilimi farkını ekle (3 saat)
                    startDateObj.setHours(startDateObj.getHours() + 3);
                    formattedStartDate = startDateObj.toISOString().slice(0, 19).replace('T', ' ');
                }

                if (endDate) {
                    // datetime-local input'tan gelen değeri düzgün formata çevir
                    // Türkiye saat dilimine göre ayarla (UTC+3)
                    const endDateObj = new Date(endDate);
                    // Saat dilimi farkını ekle (3 saat)
                    endDateObj.setHours(endDateObj.getHours() + 3);
                    formattedEndDate = endDateObj.toISOString().slice(0, 19).replace('T', ' ');
                }

                console.log('CSV için filtre değerleri:', {
                    startDate: formattedStartDate,
                    endDate: formattedEndDate,
                    type: type
                });

                // API isteği
                fetch('/panel/measurements-history/data?' + new URLSearchParams({
                    start_date: formattedStartDate,
                    end_date: formattedEndDate,
                    type: type
                }))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('CSV için API yanıtı:', data);

                        if (data.success) {
                            if (data.data.length === 0) {
                                toastr.warning('Dışa aktarılacak veri bulunamadı.');
                                return;
                            }

                            // CSV başlıkları
                            let csv = 'Tarih,Tip,Değer,Birim\n';

                            // Verileri ekle
                            data.data.forEach(measurement => {
                                csv += `${measurement.formatted_date},${measurement.type},${measurement.value.toFixed(1)},${measurement.unit}\n`;
                            });

                            // CSV dosyasını oluştur ve indir
                            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                            const url = URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.setAttribute('href', url);
                            link.setAttribute('download', `olcumler_${new Date().toISOString().slice(0, 10)}.csv`);
                            link.style.visibility = 'hidden';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            URL.revokeObjectURL(url);

                            toastr.success('Veriler CSV olarak indirildi.');
                        } else {
                            toastr.error(data.error || 'Veriler dışa aktarılırken bir hata oluştu.');
                        }
                    })
                    .catch(error => {
                        console.error('CSV Export Error:', error);
                        toastr.error('Veriler dışa aktarılırken bir hata oluştu: ' + error.message);
                    });
            }

            // Grafikleri temizle
            function clearCharts() {
                // Birleşik grafik temizleme
                combinedChart.data.labels = [];
                combinedChart.data.datasets[0].data = [];
                combinedChart.data.datasets[1].data = [];
                combinedChart.data.datasets[2].data = [];
                combinedChart.update();

                // Sıcaklık grafiği temizleme
                temperatureChart.data.labels = [];
                temperatureChart.data.datasets[0].data = [];
                temperatureChart.update();

                // Nem grafiği temizleme
                humidityChart.data.labels = [];
                humidityChart.data.datasets[0].data = [];
                humidityChart.update();

                // CO grafiği temizleme
                coChart.data.labels = [];
                coChart.data.datasets[0].data = [];
                coChart.update();

                // Özet bilgileri temizle
                document.getElementById('latest-temperature').textContent = '--';
                document.getElementById('latest-humidity').textContent = '--';
                document.getElementById('latest-co').textContent = '--';
                document.getElementById('total-measurements').textContent = '0';
            }

            // Sayfa yüklendiğinde
            document.addEventListener('DOMContentLoaded', function() {
                // Tooltip'leri etkinleştir
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Grafikleri oluştur
                createCharts();

                // Filtreleri temizle ve verileri yükle
                clearFilters();

                // Filtre butonuna tıklama olayı
                document.getElementById('filter-btn').addEventListener('click', function() {
                    console.log('Filtre butonuna tıklandı');
                    loadMeasurements();
                });

                // Filtreleri temizle butonuna tıklama olayı
                document.getElementById('clear-filters').addEventListener('click', function() {
                    console.log('Filtreleri temizle butonuna tıklandı');
                    clearFilters();
                });

                // Yenile butonuna tıklama olayı
                document.getElementById('refresh-data').addEventListener('click', function() {
                    console.log('Yenile butonuna tıklandı');
                    clearFilters();
                });

                // CSV dışa aktarma butonuna tıklama olayı
                document.getElementById('export-csv').addEventListener('click', function() {
                    console.log('CSV dışa aktarma butonuna tıklandı');
                    exportToCSV();
                });

                // Filtreleri temizleme fonksiyonu
                function clearFilters() {
                    // Tarih alanlarını temizle
                    document.getElementById('start-date').value = '';
                    document.getElementById('end-date').value = '';

                    // Ölçüm tipini varsayılana ayarla
                    document.getElementById('measurement-type').value = 'all';

                    console.log('Filtreler temizlendi');

                    // Tüm verileri yükle
                    loadMeasurements();
                }

                // Sidebar toggle for mobile
                const sidebarToggle = document.querySelector('.navbar-toggler');
                const sidebar = document.querySelector('.sidebar');

                if (sidebarToggle && sidebar) {
                    sidebarToggle.addEventListener('click', function() {
                        sidebar.classList.toggle('show');
                    });

                    // Ekran boyutu değiştiğinde sidebar'ı kontrol et
                    window.addEventListener('resize', function() {
                        if (window.innerWidth >= 768) {
                            sidebar.classList.remove('show');
                        }
                    });

                    // Sayfa dışına tıklandığında sidebar'ı kapat
                    document.addEventListener('click', function(event) {
                        if (window.innerWidth < 768 &&
                            !sidebar.contains(event.target) &&
                            !sidebarToggle.contains(event.target) &&
                            sidebar.classList.contains('show')) {
                            sidebar.classList.remove('show');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection

