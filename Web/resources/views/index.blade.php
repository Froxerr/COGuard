@section('main')
    @extends('layouts.layout')



    <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="hero-content" data-aos="fade-up" data-aos-delay="200">

                            <h1 class="mb-4">
                                Akıllı CO Güvenlik <br>
                                Sistemi ile <br>
                                <span class="accent-text">Güvende Kalın</span>
                            </h1>

                            <p class="mb-4 mb-md-5">
                                Evinizde sobalı ısınma sisteminden kaynaklanan karbonmonoksit tehlikesine karşı
                                7/24 koruma sağlayan akıllı güvenlik sistemi. Sıcaklık, nem ve CO seviyelerini
                                anlık takip edin, tehlike anında anında haberdar olun.
                            </p>

                            <div class="hero-buttons">
                                @if($isLoggedIn)
                                    <a href="/panel" class="btn btn-primary me-0 me-sm-2 mx-1">Panele Gidin</a>
                                    <a href="/logout" class="btn btn-secondary me-0 me-sm-2 mx-1">Çıkış Yap</a>
                                @else
                                    <a href="/register" class="btn btn-primary me-0 me-sm-2 mx-1">Hemen Başla</a>
                                    <a href="/login" class="btn btn-secondary me-0 me-sm-2 mx-1">Giriş Yap</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
                            <img src="{{asset("assets/img/illustration-1.png")}}" alt="Hero Image" class="img-fluid">

                            <div class="customers-badge">
                                <div class="customer-avatars">
                                    <img src="{{asset("assets/img/avatar-1.webp")}}" alt="Customer 1" class="avatar">
                                    <img src="{{asset("assets/img/avatar-2.webp")}}" alt="Customer 2" class="avatar">
                                    <img src="{{asset("assets/img/avatar-3.webp")}}" alt="Customer 3" class="avatar">
                                    <img src="{{asset("assets/img/avatar-4.webp")}}" alt="Customer 4" class="avatar">
                                    <img src="{{asset("assets/img/avatar-5.webp")}}" alt="Customer 5" class="avatar">
                                    <span class="avatar more">1K+</span>
                                </div>
                                <p class="mb-0 mt-2">1000+ kullanıcı CO Guard ile güvende</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row stats-row gy-4 mt-5" data-aos="fade-up" data-aos-delay="500">

                    <div class="col-lg-6 col-md-6">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="stat-content">
                                <h4>%100 Güvenilirlik</h4>
                                <p class="mb-0">Hassas Sensör Teknolojisi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stat-content">
                                <h4>1000+ Kullanıcı</h4>
                                <p class="mb-0">Mutlu Müşteri</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section><!-- /Hero Section -->

        <!-- About Section -->
        <section id="about" class="about section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4 align-items-center justify-content-between">

                    <div class="col-xl-5" data-aos="fade-up" data-aos-delay="200">
                        <span class="about-meta">HAKKIMIZDA</span>
                        <h2 class="about-title">Evinizin Güvenliği İçin Akıllı Çözüm</h2>
                        <p class="about-description">CO Guard, sobalı evlerde yaşayan ailelerin güvenliği için geliştirilmiş akıllı bir karbonmonoksit güvenlik sistemidir. Gelişmiş sensör teknolojisi ve 7/24 izleme sistemi ile sizin ve sevdiklerinizin güvenliğini sağlar.</p>

                        <div class="row feature-list-wrapper">
                            <div class="col-md-6">
                                <ul class="feature-list">
                                    <li><i class="bi bi-check-circle-fill"></i> Gerçek zamanlı CO takibi</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Sıcaklık kontrolü</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Nem seviyesi ölçümü</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="feature-list">
                                    <li><i class="bi bi-check-circle-fill"></i> Mobil uygulama desteği</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Anlık bildirimler</li>
                                    <li><i class="bi bi-check-circle-fill"></i> 7/24 teknik destek</li>
                                </ul>
                            </div>
                        </div>

                        <div class="info-wrapper">
                            <div class="row gy-4">
                                <div class="col-lg-7">
                                    <div class="contact-info d-flex align-items-center gap-2">
                                        <i class="bi bi-telephone-fill"></i>
                                        <div>
                                            <p class="contact-label">7/24 Destek Hattı</p>
                                            <p class="contact-number">0850 123 45 67</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="image-wrapper">
                            <div class="images position-relative" data-aos="zoom-out" data-aos-delay="400">
                                <img src="{{asset("assets/img/aboutt.png")}}" alt="Business Meeting" class="img-fluid main-image rounded-4">
                                
                            </div>
                            <div class="experience-badge floating">
                                <h3>Güvenli <span>Yıllar</span></h3>
                                <p>sizinle beraber güvenli yıllarla</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section><!-- /About Section -->

        <!-- Features Section -->
        <section id="features" class="features section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Özellikler</h2>
                <p>CO Guard'ın sunduğu akıllı güvenlik özellikleri ile evinizi 7/24 güvende tutun</p>
            </div>

            <div class="container">

                <div class="d-flex justify-content-center">

                    <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">

                        <li class="nav-item">
                            <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                                <h4>Sensörler</h4>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                                <h4>Bildirimler</h4>
                            </a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                                <h4>Raporlama</h4>
                            </a>
                        </li>

                    </ul>

                </div>

                <div class="tab-content" data-aos="fade-up" data-aos-delay="200">

                    <div class="tab-pane fade active show" id="features-tab-1">
                        <div class="row">
                            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 d-flex flex-column justify-content-center">
                                <h3>Hassas Sensör Teknolojisi</h3>
                                <p class="fst-italic">
                                    En son teknoloji sensörler ile evinizin havasını sürekli analiz eder ve güvenliğinizi sağlar.
                                </p>
                                <ul>
                                    <li><i class="bi bi-check2-all"></i> <span>Yüksek hassasiyetli karbonmonoksit sensörü ile tehlikeyi erkenden tespit eder.</span></li>
                                    <li><i class="bi bi-check2-all"></i> <span>Sıcaklık sensörü ile ortam ısısını sürekli kontrol eder.</span></li>
                                    <li><i class="bi bi-check2-all"></i> <span>Nem sensörü ile yaşam alanınızın nem seviyesini takip eder.</span></li>
                                </ul>
                            </div>
                            <div class="col-lg-6 order-1 order-lg-2 text-center">
                                <img src="{{asset("assets/img/sensor.png")}}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="features-tab-2">
                        <div class="row">
                            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 d-flex flex-column justify-content-center">
                                <h3>Akıllı Bildirim Sistemi</h3>
                                <p class="fst-italic">
                                    Tehlike anında size ve belirlediğiniz kişilere anında bildirim gönderir.
                                </p>
                                <ul>
                                    <li><i class="bi bi-check2-all"></i> <span>Sesli alarm sistemi ile evde uyarı verir.</span></li>
                                    <li><i class="bi bi-check2-all"></i> <span>Mobil uygulama üzerinden anlık bildirimler alırsınız.</span></li>
                                    <li><i class="bi bi-check2-all"></i> <span>Kritik durumlarda otomatik olarak birimlere size haber verir.</span></li>
                                </ul>
                            </div>
                            <div class="col-lg-6 order-1 order-lg-2 text-center">
                                <img src="{{asset("assets/img/bildirim.png")}}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="features-tab-3">
                        <div class="row">
                            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 d-flex flex-column justify-content-center">
                                <h3>Detaylı Raporlama</h3>
                                <ul>
                                    <li><i class="bi bi-check2-all"></i> <span>Günlük, haftalık ve aylık CO seviyesi raporları</span></li>
                                    <li><i class="bi bi-check2-all"></i> <span>Sıcaklık ve nem değişim grafikleri</span></li>
                                    <li><i class="bi bi-check2-all"></i> <span>Alarm geçmişi ve olay kayıtları</span></li>
                                </ul>
                            </div>
                            <div class="col-lg-6 order-1 order-lg-2 text-center">
                                <img src="{{asset("assets/img/raporlama.png")}}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </section><!-- /Features Section -->



        <!-- Features 2 Section -->
        <section id="mobil" class="features-2 section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row align-items-center">

                    <div class="col-lg-4">

                        <div class="feature-item text-end mb-5" data-aos="fade-right" data-aos-delay="200">
                            <div class="d-flex align-items-center justify-content-end gap-4">
                                <div class="feature-content">
                                    <h3>Her Cihazda Erişim</h3>
                                    <p>iOS ve Android uyumlu mobil uygulamamız ile evinizin güvenliğini her an, her yerden takip edin.</p>
                                </div>
                                <div class="feature-icon flex-shrink-0">
                                    <i class="bi bi-phone"></i>
                                </div>
                            </div>
                        </div>

                        <div class="feature-item text-end mb-5" data-aos="fade-right" data-aos-delay="300">
                            <div class="d-flex align-items-center justify-content-end gap-4">
                                <div class="feature-content">
                                    <h3>Anlık Bildirimler</h3>
                                    <p>Tehlike durumunda anında bildirim alın, gerekli önlemleri zamanında alarak güvende kalın.</p>
                                </div>
                                <div class="feature-icon flex-shrink-0">
                                    <i class="bi bi-bell"></i>
                                </div>
                            </div>
                        </div>

                        <div class="feature-item text-end" data-aos="fade-right" data-aos-delay="400">
                            <div class="d-flex align-items-center justify-content-end gap-4">
                                <div class="feature-content">
                                    <h3>Detaylı Raporlar</h3>
                                    <p>Günlük, haftalık ve aylık CO, sıcaklık ve nem raporlarını kolayca görüntüleyin ve analiz edin.</p>
                                </div>
                                <div class="feature-icon flex-shrink-0">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                        <div class="phone-mockup text-center">
                            <img src="{{asset("assets/img/phone-app-screen.png")}}" alt="CO Guard Mobil Uygulama" class="img-fluid">
                        </div>
                    </div>

                    <div class="col-lg-4">

                        <div class="feature-item mb-5" data-aos="fade-left" data-aos-delay="200">
                            <div class="d-flex align-items-center gap-4">
                                <div class="feature-icon flex-shrink-0">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Güvenli Sistem</h3>
                                    <p>En son güvenlik protokolleri ile verileriniz her zaman güvende ve şifreli olarak saklanır.</p>
                                </div>
                            </div>
                        </div>

                        <div class="feature-item mb-5" data-aos="fade-left" data-aos-delay="300">
                            <div class="d-flex align-items-center gap-4">
                                <div class="feature-icon flex-shrink-0">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Aile Paylaşımı</h3>
                                    <p>Ailenizin diğer üyeleriyle sistemi paylaşın, herkes güvende olsun.</p>
                                </div>
                            </div>
                        </div>

                        <div class="feature-item" data-aos="fade-left" data-aos-delay="400">
                            <div class="d-flex align-items-center gap-4">
                                <div class="feature-icon flex-shrink-0">
                                    <i class="bi bi-gear"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Kolay Kullanım</h3>
                                    <p>Kullanıcı dostu arayüz ile tüm özelliklere kolayca erişin ve yönetin.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </section><!-- /Features 2 Section -->


        <!-- Stats Section -->
        <section id="stats" class="stats section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="10000" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Aktif Kullanıcı</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="150" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Kurtarılan Hayat</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="8760" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Yıllık Çalışma Saati</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="50" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Teknik Ekip</p>
                        </div>
                    </div>

                </div>

            </div>

        </section><!-- /Stats Section -->

        <!-- Contact Section -->
        <section id="contact" class="contact section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>İletişim</h2>
                <p>Sorularınız için bize ulaşın, uzman ekibimiz size yardımcı olmaktan mutluluk duyacaktır</p>
            </div>

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row g-4 g-lg-5">
                    <div class="col-lg-5">
                        <div class="info-box" data-aos="fade-up" data-aos-delay="200">
                            <h3>İletişim Bilgileri</h3>
                            <p>CO Guard olarak 7/24 sizin güvenliğiniz için çalışıyoruz. Bize aşağıdaki kanallardan ulaşabilirsiniz.</p>

                            <div class="info-item" data-aos="fade-up" data-aos-delay="300">
                                <div class="icon-box">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="content">
                                    <h4>Adresimiz</h4>
                                    <p>Teknik Bilimler Fakültesi</p>
                                    <p>Çanakkale Onsekiz Mart Üniversitesi</p>
                                    <p>Çanakkale, 17100</p>
                                </div>
                            </div>

                            <div class="info-item" data-aos="fade-up" data-aos-delay="400">
                                <div class="icon-box">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div class="content">
                                    <h4>Telefon</h4>
                                    <p>0850 000 00 00</p>
                                    <p>0850 000 00 00</p>
                                </div>
                            </div>

                            <div class="info-item" data-aos="fade-up" data-aos-delay="500">
                                <div class="icon-box">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div class="content">
                                    <h4>E-posta</h4>
                                    <p>info@coguard.com</p>
                                    <p>destek@coguard.com</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="contact-form" data-aos="fade-up" data-aos-delay="300">
                            <h3>Bize Ulaşın</h3>
                            <p>Ürünlerimiz, kurulum veya teknik destek hakkında sorularınız için formu doldurun, size en kısa sürede dönüş yapalım.</p>

                            <form action="" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                                <div class="row gy-4">

                                    <div class="col-md-6">
                                        <input type="text" name="name" class="form-control" placeholder="Adınız Soyadınız" required>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="email" placeholder="E-posta Adresiniz" required>
                                    </div>

                                    <div class="col-12">
                                        <input type="text" class="form-control" name="subject" placeholder="Konu" required>
                                    </div>

                                    <div class="col-12">
                                        <textarea class="form-control" name="message" rows="6" placeholder="Mesajınız" required></textarea>
                                    </div>

                                    <div class="col-12 text-center">
                                        <div class="loading">Yükleniyor</div>
                                        <div class="error-message"></div>
                                        <div class="sent-message">Mesajınız gönderildi. Teşekkür ederiz!</div>

                                        <button type="submit" class="btn">Mesaj Gönder</button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                </div>

            </div>

        </section><!-- /Contact Section -->

    </main>

@endsection
