<footer id="footer" class="footer">

    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6 footer-about">
                <a href="/" class="logo d-flex align-items-center">
                    <span class="sitename">CO Guard</span>
                </a>
                <div class="footer-contact pt-3">
                    <p>Teknik Bilimler Fakültesi</p>
                    <p>Çanakkale, 17100</p>
                    <p class="mt-3"><strong>Telefon:</strong> <span>0850 000 00 00</span></p>
                    <p><strong>Email:</strong> <span>info@coguard.com</span></p>
                </div>
                <div class="social-links d-flex mt-4">
                    <a href=""><i class="bi bi-twitter-x"></i></a>
                    <a href=""><i class="bi bi-facebook"></i></a>
                    <a href=""><i class="bi bi-instagram"></i></a>
                    <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Hızlı Erişim</h4>
                <ul>
                    <li><a href="#">Anasayfa</a></li>
                    <li><a href="#">Hakkımızda</a></li>
                    <li><a href="#">Özellikler</a></li>
                    <li><a href="#">Kullanım Koşulları</a></li>
                    <li><a href="#">Gizlilik Politikası</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Ürünlerimiz</h4>
                <ul>
                    <li><a href="#">CO Sensörü</a></li>
                    <li><a href="#">Sıcaklık Sensörü</a></li>
                    <li><a href="#">Nem Sensörü</a></li>
                    <li><a href="#">Mobil Uygulama</a></li>
                    <li><a href="#">Teknik Destek</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Destek</h4>
                <ul>
                    <li><a href="#">Sıkça Sorulan Sorular</a></li>
                    <li><a href="#">Kurulum Rehberi</a></li>
                    <li><a href="#">Bakım</a></li>
                    <li><a href="#">İletişim</a></li>
                    <li><a href="#">Bayi Ağı</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Kurumsal</h4>
                <ul>
                    <li><a href="#">Kariyer</a></li>
                    <li><a href="#">Basın</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Sertifikalar</a></li>
                    <li><a href="#">Referanslar</a></li>
                </ul>
            </div>

        </div>
    </div>

</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files - Async yükleme -->
<script src="{{asset("assets/vendor/bootstrap/js/bootstrap.bundle.min.js")}}" defer></script>
<script src="{{asset("assets/vendor/aos/aos.js")}}" defer></script>
<script src="{{asset("assets/vendor/glightbox/js/glightbox.min.js")}}" defer></script>
<script src="{{asset("assets/vendor/swiper/swiper-bundle.min.js")}}" defer></script>
<script src="{{asset("assets/vendor/purecounter/purecounter_vanilla.js")}}" defer></script>

<!-- jQuery ve Toastr sadece ihtiyaç duyulduğunda yüklensin -->
@if(Session::has('success'))
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- jQuery ve Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    // Toastr ayarları
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "500",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "5000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    
    // Success mesajını göster
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            toastr.success("{{ Session::get('success') }}", "Başarılı!");
        }, 500);
    });
</script>
@endif

<!-- Main JS File -->
<script src="{{asset("assets/js/main.js")}}" defer></script>
