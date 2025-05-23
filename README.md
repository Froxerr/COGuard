<h1 align="center">
  <br>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/main_logo_rectangle.jpg" alt="Proje Logo" width="120"/>
  <br>
  D1D0
  <br>
</h1>

<h4 align="center">Karbonmonoksit Sensörü ile Akıllı Güvenlik Sistemi</h4>

<div align="center" style="margin-bottom: 24px;">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Android-3DDC84?logo=android&logoColor=white" alt="Android Studio">
  <img src="https://img.shields.io/badge/Arduino-00878F?logo=arduino&logoColor=fff&style=plastic" alt="Ardunio">
  <img src="https://img.shields.io/badge/Firebase-039BE5?logo=Firebase&logoColor=white" alt="Firebase">
</div>

<div align="center">
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/main-all.gif" alt="Sistem Genel Akış" width="350" style="margin-bottom: 24px;"/>
</div>

---

## İçindekiler

- [Proje Özeti & Amacı](#proje-özeti--amacı)
- [Arduino/ESP8266 Donanımı](#ardunio-esp8266)
- [PHP API (Ara Sunucu)](#php-api)
- [Firebase Realtime Database](#firebase-realtime)
- [Laravel Web Panel](#laravel-web-panel)
- [Android Mobil Uygulama](#android-mobil)
- [Maket ve Demo](#maket-demo)
- [Katkı, Lisans ve İletişim](#katki-lisans)

---

<h2 id="proje-özeti--amacı"> Proje Özeti & Amacı </h2>

Karbonmonoksit (CO), renksiz, kokusuz ve tatsız bir gaz olup, yüksek seviyelerde maruz kalındığında ciddi sağlık sorunlarına ve ölüme yol açabilir. Bu proje, karbonmonoksit gazının yanı sıra ortam sıcaklığı ve nem seviyesini de sürekli izleyerek, kullanıcıyı mobil uygulama ve web arayüzü üzerinden anlık olarak bilgilendirmeyi ve gerektiğinde pencerenin otomatik olarak açılmasını sağlamayı amaçlamaktadır.

Sistem, kullanıcıların karbonmonoksit, sıcaklık ve nem için eşik değerlerini belirleyebileceği, kritik seviyelerde sesli ve görsel uyarılarla anında bilgilendirileceği, geçmiş verilerin ve alarmların detaylı şekilde izlenebileceği modern bir güvenlik çözümü sunar. Tüm veriler bulut tabanlı olarak saklanır ve hem mobil hem web arayüzüyle senkronize çalışır. Böylece, ortam güvenliği her an ve her yerden kontrol edilebilir hale gelir.

Bu projenin temel hedefi, karbonmonoksit seviyelerini algılayan, sıcaklık ve nem takibi yapan, uyarı sistemleriyle kullanıcıyı bilgilendiren, web tabanlı bir kontrol paneli sunan ve pencere açma mekanizması ile güvenliği artıran bütünleşik ve akıllı bir ortam izleme sistemi tasarlamaktır.


<h2 id="ardunio-esp8266"> Arduino/ESP8266 Donanımı </h2>

### 🔎 Tanım & Önemi
Akıllı güvenlik sisteminin kalbini oluşturan bu donanım, ortamdan karbonmonoksit, sıcaklık ve nem verilerini toplar, kritik durumlarda alarm ve pencere açma mekanizmasını tetikler ve tüm verileri buluta iletir. Gerçek zamanlı ve otomatik müdahale ile güvenliği bir üst seviyeye taşır.

### Kullanılan Bileşenler
- MQ-7 Karbonmonoksit Sensörü
- DHT22 Sıcaklık & Nem Sensörü
- SG90 Servo Motor
- Buzzer & LED
- 2 Kanal Röle Kartı
- ESP8266 Wi-Fi Modülü

### Donanım Görselleri
| Bileşen | Görsel |
|---------|--------|
| MQ-7 Sensörü | <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mq-7.png" width="80"/> |
| DHT22 Sensörü | <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/dht22.png" width="80"/> |
| Servo Motor | <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/servo-motor.png" width="80"/> |
| Buzzer Kartı | <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/buzzer-kart.png" width="80"/> |
| ESP8266 | <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/esp8266.png" width="80"/> |

### Kurulum
1. Tüm bileşenleri aşağıdaki videoya göre uygun bir şekilde bağlayın.
2. VİDEO LİNKİ GELECEK BURAYA 
3. `sketch_mar14a.ino` dosyasını Arduino IDE ile açın.
4. Kodda aşağıdaki bilgileri güncelleyin:
   - Firebase URL ve API Key
   - WiFi SSID ve şifre
   - API sunucu IP adresi
5. Kodu ESP8266'ya yükleyin.
6. Cihazı başlatın ve seri monitörden bağlantıyı kontrol edin.
7. Lütfen sorun yaşarsanız beni-oku.pdf dosyasını da okumayı unutmayın.

### Kullanım
- Sensörler ortamı izler, veriler otomatik olarak sunucuya iletilir.
- Kritik durumda alarm çalar ve pencere açılır.

---

<h2 id="php-api"> PHP API (Ara Sunucu) </h2>

### 🔎 Tanım & Önemi
Donanımdan gelen verileri güvenli şekilde alıp Firebase'e aktaran, sistemin bulut ile donanım arasındaki köprüsüdür. Ayrıca web paneli ile veri alışverişini sağlar.

### Bileşenler
- `index.php` ana dosyadır.
- Sunucu (XAMPP, WAMP vb.) üzerinde çalışır.

### Kurulum
1. Sunucunuza PHP ortamı kurun (XAMPP önerilir).
2. `index.php` dosyasını sunucuya yerleştirin.
3. Dosya başındaki `firebaseUrl` ve `firebaseKey` değişkenlerini yeni Firebase bilgilerinizle güncelleyin.
4. Sunucuyu başlatın.
5. Lütfen sorun yaşarsanız beni-oku.pdf dosyasını da okumayı unutmayın.

### Kullanım
- ESP8266, bu API'ye veri gönderir.
- API, verileri Firebase'e kaydeder.

---

<h2 id="firebase-realtime"> Firebase Realtime Database </h2>

### 🔎 Tanım & Önemi
Tüm sensör verileri, kullanıcı işlemleri ve sistem ayarlarının merkezi olarak saklandığı, web ve mobil uygulama ile anlık senkronize çalışan bulut veritabanıdır. Güvenli, hızlı ve ölçeklenebilir veri yönetimi sağlar.

### Kurulum
1. <a href="https://console.firebase.google.com/">Firebase Console</a> üzerinden yeni bir proje oluşturun.
2. Realtime Database'i etkinleştirin.
3. Project Settings > General'dan Web API Key, App ID alın.
4. Service Accounts sekmesinden admin anahtarını indirin.
5. Authentication'da Email/Password ve Anonymous giriş yöntemlerini aktif edin.
6. Veritabanı kurallarını `{ "rules": { ".read": true, ".write": true } }` olarak ayarlayın.
7. Github da bulunan eski verileri JSON ile içe aktarabilirsiniz.

### Kullanım
- Tüm uygulamalar Firebase ile veri alışverişi yapar.

---

<h2 id="laravel-web-panel"> Laravel Web Panel </h2>

### Tanım & Önemi
Kullanıcı ve admin girişleri, sensör verilerinin ve alarmların izlenmesi, eşik değerlerinin ve sensör ayarlarının yönetimi, alarm geçmişi ve grafiksel raporlama için modern ve kullanıcı dostu bir arayüz sunar. Sistemin tüm yönetimi tek panelden kolayca yapılabilir.

### Kurulum
1. `.env` dosyasında `FIREBASE_DATABASE_URL` ve `FIREBASE_AUTH_KEY` değerlerini girin.
2. `storage/app/` dizinine `firebase-credentials.json` dosyasını ekleyin.
3. `resources/views/panel/layouts/app.blade.php` içinde Firebase yapılandırma objesini güncelleyin.
4. Giriş için: `admin@gmail.com` / `123123`
5. Lütfen sorun yaşarsanız beni-oku.pdf dosyasını da okumayı unutmayın.

### Kullanım
- Web panel üzerinden tüm sistem yönetilebilir.
- Alarm ve sensör geçmişi grafiksel olarak görüntülenebilir.

### Ekran Görüntüleri
<div align="center">
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-2.png" alt="Web Panel 1" width="400" style="margin: 16px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-1.png" alt="Web Panel 2" width="400" style="margin: 16px;"/>
</div>

<div align="center">
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-panel-1.png" alt="Admin Panel 1" width="260" style="margin: 16px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-panel-2.png" alt="Admin Panel 2" width="260" style="margin: 16px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-panel-3.png" alt="Admin Panel 3" width="260" style="margin: 16px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-panel-4.png" alt="Admin Panel 4" width="260" style="margin: 16px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-panel-5.png" alt="Admin Panel 5" width="260" style="margin: 16px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/web-panel-6.png" alt="Admin Panel 6" width="260" style="margin: 16px;"/>
</div>

---

<h2 id="android-mobil"> Android Mobil Uygulama </h2>

### 🔎 Tanım & Önemi
Kullanıcıya ortam durumu ve alarmları gösterir, bildirimler gönderir ve eşik değerlerinin ayarlanmasını sağlar. Her an, her yerden güvenlik ve kontrol imkanı sunar.

### Kurulum
1. Firebase'den `google-services.json` dosyasını indirip uygulama dizinine ekleyin.
2. Giriş için: `admin@gmail.com` / `123123`
3. Android Studio ile projeyi açın ve derleyin.
4. Lütfen sorun yaşarsanız beni-oku.pdf dosyasını da okumayı unutmayın.

### Kullanım
- Mobil uygulama ile ortam durumu ve alarmlar takip edilebilir.
- Bildirimler alınır, eşik değerleri ayarlanabilir.

### Ekran Görüntüleri
<div align="center">
  <div>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-5.jpeg" alt="Mobil 1" width="180" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-1.jpeg" alt="Mobil 2" width="180" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-6.jpeg" alt="Mobil 3" width="180" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-4.jpeg" alt="Mobil 4" width="180" style="margin: 12px;"/>
  </div>
  <div>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-3.jpeg" alt="Mobil 5" width="180" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-2.jpeg" alt="Mobil 6" width="180" style="margin: 12px;"/>
  </div>
</div>

<div align="center">
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-bildiirm-1.jpeg" alt="Mobil Bildirim 1" width="250" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-bildirim-2.jpeg" alt="Mobil Bildirim 2" width="250" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/mobil-bildirim-3.jpeg" alt="Mobil Bildirim 3" width="250" style="margin: 12px;"/>
</div>

---

<h2 id="maket-demo"> Maket ve Demo </h2>

<div align="center">
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/maket-1.jpeg" alt="Maket 1" width="260" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/maket-2.jpeg" alt="Maket 2" width="260" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/maket-3.jpeg" alt="Maket 3" width="260" style="margin: 12px;"/>
</div>

<div align="center">
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/maket-tasarim-1.png" alt="Maket Tasarım 1" width="260" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/maket-tasarim-2.png" alt="Maket Tasarım 2" width="260" style="margin: 12px;"/>
  <img src="https://github.com/Froxerr/COGuard/blob/main/github-img/maket-tasarim-3.png" alt="Maket Tasarım 3" width="260" style="margin: 12px;"/>
</div>

---

<h2 id="katki-lisans"> 🤝 Katkı, Lisans ve İletişim </h2>

- Katkıda bulunmak için lütfen bir pull request gönderin veya issue açın.
- Lisans bilgisi için proje sahibine başvurunuz.
- Sorularınız için: ibrahimaral20@gmail.com
- Sorularınız için: aktasnursen813@gmail.com
---

**Hazırlayanlar:** Nurşen AKTAŞ, İbrahim ARAL
