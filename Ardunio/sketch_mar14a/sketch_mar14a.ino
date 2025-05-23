#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecure.h>
#include <Firebase_ESP_Client.h>
#include <DHT.h>
#include <time.h>
#include <ArduinoJson.h>
#include <Servo.h> 

//GÜVEN ARALIKLARI ODA KOŞULLARINDA GERÇEK HAYATTA
//NEM 65%
//SICAKLIK 20-22
//2.5 3PPM



// Sensör verileri yapısı - Bu yapı en üstte olmalı
struct SensorData { 
  unsigned long lastSendTime; //Sensörün en son ne zaman gönderildiği 
  float lastValue; //En son değeri
  String sensorType; //en son sensor tipi Karbonomoksit mi ne
  bool isValid; //Bu hala geçerli bir değer mi
};

// Sensör ayarları yapısı
struct SensorSettings { //Her sensörün kendine ait ayarları
  String sensorId; //Sensorün hangi sensore ait olduğunu belirten id
  unsigned long intervalMin; //bu sensör kaç dakika da bir çalışacak
  bool isConfigured; //bu sensör veritabanından çekilip ayarlandı mı
};

// Global sensör ayarları
SensorSettings tempSettings = {"sensor_type_1", 1, false};  // Varsayılan 1 dakika
SensorSettings humSettings = {"sensor_type_2", 1, false};   // Varsayılan 1 dakika
SensorSettings coSettings = {"sensor_type_3", 1, false}; // Varsayılan 1 dakika 

// Token durumu için callback fonksiyonu
void tokenStatusCallback(TokenInfo info) //Token var mı mevcut mu varsa durumu ne tarzında bilgiler veritabanına bağlanırken hata ayıklama da iş yapıyor
{
  Serial.println("Token Durumu: ");
  Serial.println("Status: " + String(info.status == token_status_ready ? "Hazır" : "Hazır Değil"));
  Serial.println("Tip: " + String(info.type));
}

// sendSensorData fonksiyon prototipi
void sendSensorData(SensorData &sensor); //Önceden böyle bir sensor yapısı olduğunu belirtiyoruz

#define DHTPIN D1     // DHT sensörünün bağlı olduğu pin
#define DHTTYPE DHT22 // DHT22 sensörü
#define MQ7_AOUT_PIN A0   // MQ-7 sensörünün bağlı olduğu pin

// Çıkış pinleri tanımlamaları
#define LED_PIN D4        // LED için dijital pin
#define BUZZER_PIN D2     // Buzzer için dijital pin
#define SERVO_PIN D3      // Servo motor için dijital pin DURUMA GÖRE DÜZENLENECEK

// MQ-7 Sensör Kalibrasyon Değerleri
#define RL_VALUE 10.0     // Yük direnci (kΩ)
#define R0 10.0          // Temiz havadaki direnç değeri (kΩ)
#define VOLTAGE 3.3      // ESP8266'nın çalışma voltajı

// Wi-Fi bilgileri
const char* ssid = ""; // Wi-Fi Kullanıcı adı
const char* password = ""; // Wi-Fi Şifresi

// API URL
const char* apiUrl = ""; //Benim php apim

// API Bilgileri - VERİ BAĞLAMA DURUMU
const char* apiGetUrl = "";  // Sadece domain adı
const char* apiKey = ""; // Web API Key
FirebaseData fbdo; //fbdo diye veritabanından veri çekerken bu kütüphaneyi kullanıcaz
FirebaseAuth auth; //veritabanına bağlanırken de Auth kullanıcaz
FirebaseConfig config; //Ayarlama da veri çekme de kullanıcaz
bool signUpOk = false; //Bu sadece kontrol giriş yapıp yapmadığına dair

// Bağlantı denemesi sayısı
const int MAX_CONNECTION_RETRIES = 3;

// HTTP zaman aşımı süresi (milisaniye)
const int HTTP_TIMEOUT = 10000; // 10 saniye

// Sensör ısınma süresi (milisaniye)
const unsigned long SENSOR_WARMUP_TIME = 5000; // 5 saniye

// Çıkış kontrolü aralığı (milisaniye)
const unsigned long OUTPUT_CHECK_INTERVAL = 5000; // 30 saniye

// Sensör ayarları güncelleme aralığı (milisaniye)
const unsigned long SETTINGS_UPDATE_INTERVAL = 1 * 60 * 1000; // 1 dakika

// Buzzer durumu için global değişkenler
bool buzzerActive = false;
unsigned long lastBuzzerUpdate = 0;
const unsigned long BUZZER_CYCLE_INTERVAL = 20; // 20ms döngü hızı

DHT dht(DHTPIN, DHTTYPE); //Nem ve Sıcaklık kütüphanesinden bir ayar

// Sensör nesneleri
SensorData temperatureSensor = {0, 0, "tempature", false}; //Sensör nesneleri varsayılan olarak burada ayarlıyoruz
SensorData humiditySensor = {0, 0, "humidity", false};
SensorData coSensor = {0, 0, "carbonmonoxide", false};

// Servo nesnesi
Servo myServo;  // Servo motor için nesne oluştur

// Çıkışları kontrol eden fonksiyon
void controlOutput(String outputType, int status) { //2 tane değer alıyor hangi çıktı tetiklenecek ve durumu ne
  Serial.println("\n>> Çıkış Kontrolü:");
  Serial.println("   Tip: " + outputType);
  Serial.println("   Durum: " + String(status));
  
  if (outputType == "Led") { //Eğer çıktı led ise
    digitalWrite(LED_PIN, status == 1 ? HIGH : LOW); //Ledpin durumu 1 ise yak değilse söndür
    Serial.println("   LED " + String(status == 1 ? "AÇILDI" : "KAPANDI"));
  }
  else if (outputType == "Buzzer") { //Eğer çıktı buzzer ise
    if (status == 1) { //durumu bir ise ses çıkart
      // Buzzer'ı aktif hale getir (loop içinde kontrol edilecek)
      buzzerActive = true;
      Serial.println("   Buzzer AÇILDI (sürekli çalışacak)");
    } else { //değilse hiçbir ses verme
      buzzerActive = false;
      noTone(BUZZER_PIN);
      Serial.println("   Buzzer KAPANDI");
    }
  }
  else if (outputType == "servoMotor") { //Servo motor da şu anlık bir sorun yaşıyoruz pinlerden dolayı sadece bağlantı sağlayıp burayı DÜZENLEYECEĞİZ UNUTMA.
    if (status == 1) {
      // Servo motoru 180 dereceye çevir (tam açık pozisyon)
      myServo.write(30);
      Serial.println("   Servo Motor AÇILDI (180 derece)");
    } else {
      // Servo motoru 0 dereceye çevir (kapalı pozisyon)
      myServo.write(180);
      Serial.println("   Servo Motor KAPANDI (0 derece)");
    }
    Serial.println("   Servo Motor durumu değiştirildi: " + String(status));
  }
  else {
    Serial.println("   !! UYARI: Bilinmeyen çıkış tipi: " + outputType);
  }
}
// Acil durum alarm sesi için
void updateBuzzer() {
  if (!buzzerActive) {
    return; // Buzzer aktif değilse hiçbir şey yapma
  }
  
  // Acil durum alarmı için parametreler
  static unsigned long lastToneChange = 0;
  static bool isHighTone = true;
  static int alarmCycle = 0;
  
  unsigned long currentMillis = millis();
  
  // Acil durum alarm sistemi - iki keskin ton arası hızlı geçiş
  if (currentMillis - lastToneChange >= 200) { // Her 200ms'de bir ton değişimi
    if (isHighTone) {
      // Yüksek perdeli uyarı tonu
      tone(BUZZER_PIN, 2000); // Daha dikkat çekici yüksek frekans
      isHighTone = false;
    } else {
      // Kısa sessizlik veya düşük ton
      if (alarmCycle % 5 == 0) { // Her 5 çevrimde bir sessizlik
        noTone(BUZZER_PIN);
      } else {
        tone(BUZZER_PIN, 1500); // Düşük ton
      }
      isHighTone = true;
      alarmCycle++;
    }
    
    // Uzun sessizlik eklemek için (alarm patternini belirginleştirmek için)
    if (alarmCycle >= 20) { // 20 çevrimden sonra
      noTone(BUZZER_PIN);
      delay(300); // Biraz daha uzun bir sessizlik
      alarmCycle = 0;
    }
    
    lastToneChange = currentMillis;
  }
}

void setup() {
  Serial.begin(115200);
  dht.begin();
  
  // Çıkış pinlerini ayarla
  pinMode(LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(SERVO_PIN, OUTPUT);
  
  // Servo motoru başlat
  myServo.attach(SERVO_PIN);
  myServo.write(0);  // Başlangıç pozisyonuna getir
  
  // Başlangıçta tüm çıkışları kapat
  digitalWrite(LED_PIN, LOW); //Kontrol olarak kapatıyoruz hepsini 
  noTone(BUZZER_PIN);
  
  // Wi-Fi bağlantısını başlat
  WiFi.begin(ssid, password); //Wifi kütüphanesinden bağlanma muhabbeti
  Serial.print("Bağlanıyor...");

  int wifiAttempts = 0; //Bağlanmayı 20 defa denemsiyle kısıtladım bağlanmıyorsa bağlanmıyor işte
  while (WiFi.status() != WL_CONNECTED && wifiAttempts < 20) {
    delay(500);
    Serial.print(".");
    wifiAttempts++;
  }
  
  if (WiFi.status() == WL_CONNECTED) { //Eğer bağlanmışsa ekrana bağlandı yaz ve veritabanı Auth kısmını yavaştan oluşturmaya başla
    Serial.println("\nWiFi Bağlandı!");
    Serial.println("IP: " + WiFi.localIP().toString());

    // Firebase yapılandırması
    config.api_key = apiKey; //Yukarıda tanımladığım firebase api anahtarı
    config.database_url = apiGetUrl; //Yukarıda tanımladığım firebase urlm
    config.token_status_callback = tokenStatusCallback; //Firebase Auth kısmı için token durumunun geri dönüşü bu yukarıdaki fonksiyonu tetikliyor

    // Firebase'i başlat
    Firebase.begin(&config, &auth); //Firebase config ve auth belirlediğim şeylerle başlat
    
    // WiFi bağlantısını kontrol et
    Firebase.reconnectWiFi(true); //ne olur ne olmaz kontrol et

    // Firebase anonim oturum açma
    if(Firebase.signUp(&config, &auth, "", "")) //Bunun için firebaseden Auth kısmından anonim girişlere izin vermek gerekiyor yoksa kabul etmiyor
    {
      Serial.println("Firebase anonim oturum açma başarılı");
      signUpOk = true; //Eğer bağlantı olduysa signupOk true yap
      
      // Token hazır olana kadar bekle
      unsigned long tokenTimeout = millis(); //Tokenin gelmesini beklemek için bir süre yapıyoruz
      while (!Firebase.ready() && (millis() - tokenTimeout < 15000)) { // Timeout süresini 15 saniyeye çıkardık bazen biraz daha beklemek daha iyi oluyor
        Serial.println("Token bekleniyor...");
        delay(1000);
      }
      
      if (Firebase.ready()) { //Eğer firebase hazır ise ekrana yazdır sadece kontrol amaclı ileride silinebilir
        Serial.println("Firebase hazır!");
      } else {
        Serial.println("Firebase token zaman aşımı!");
      }
    }
    else  //Değilse hata ver sorun vardır onu çöz
    {
      Serial.println("Firebase oturum açma başarısız");
      Serial.println("Hata: " + String(config.signer.signupError.message.c_str()));
    }
    
    // NTP sunucusuna bağlan ve zamanı senkronize et
    //NTP = Network Time Protocol internetten zamanı senkronizasyon etmeyi amaçlıyo
    configTime(3 * 3600, 0, "pool.ntp.org", "time.nist.gov");
    //3*3600 Türkiye zaman dilimi anlamına geliyor
    //0 Yaz saati uygulaması kullanılmıyor muhabbeti
    //o iki tane site de zaman bilgisini almak için kullandığımız siteler
    Serial.println("NTP sunucusuna bağlanılıyor...");
    
    time_t now = time(nullptr); //Şu an ki zaman bilgisini al
    while (now < 8 * 3600 * 2) { //Geçerli bir zaman alasıya kadar döngüyü oluştur
      delay(500);
      Serial.print(".");
      now = time(nullptr);
    }
    Serial.println("");
    struct tm timeinfo;
    gmtime_r(&now, &timeinfo); //Unix Unixtimestampden okunabilir bir formata çevir
    Serial.print("Mevcut zaman: ");
    Serial.println(asctime(&timeinfo));
    
    // Sensörlerin ısınması için bekle
    Serial.println("Sensörler ısınıyor...");
    delay(SENSOR_WARMUP_TIME);
    
    // İlk okuma denemesi
    float t = dht.readTemperature(); //dht kütüphanesinden sıcaklığı okuyoruz
    float h = dht.readHumidity(); //dht kütüphanesinden nemi okuyoruz
    int co = analogRead(MQ7_AOUT_PIN); //Analog girişli olan karbonomoksiti okuyoruz
    
    // Sensör verilerinin geçerli olup olmadığını kontrol et
    bool validReadings = false; //Okunan değerler geçerli mi önce hayır diyoruz
    int attempts = 0; //Geçerli olup olmadığını tutmak için bir deneme diye bir değişken oluşturuyoruz
    const int maxAttempts = 3; //Max deneme süremiz 3 sefer olacak
    
    while (!validReadings && attempts < maxAttempts) { //Eğer değerler geçerli olmayasıya kadar ve denenme denemeden küçük olasıya kadar true verecek bize bu da sonsuz bir döngü oluşturacak
      t = dht.readTemperature(); //Yukarıdaki okuduğumz değerleri tekrar kontrol ediyor
      h = dht.readHumidity(); 
      co = analogRead(MQ7_AOUT_PIN);
      
      if (!isnan(t) && !isnan(h) && co > 0 && t > -40 && t < 80 && h >= 0 && h <= 100) { 
        //Hiçbiri null değilse ve değerler olması gerektiği aralıkta ise
        validReadings = true; //Okunan değerler doğru
        temperatureSensor.lastValue = t; //Son ölçülen değeri eşitle hepsi için
        temperatureSensor.isValid = true;
        humiditySensor.lastValue = h;
        humiditySensor.isValid = true;
        coSensor.lastValue = calculateCOppm(co); //Burada karbonomoksit için yaptığımız ölçüm ile eşitliyoruz son değeri
        coSensor.isValid = true; 
      } else {
        attempts++; //Eğer başarısız olursa her seferinde deneme sayısını artır
        Serial.println("Sensör verilerini okuma denemesi " + String(attempts) + "/" + String(maxAttempts));
        delay(2000); //2 saniye de zaman aralığı koy
      }
    }
    
    if (validReadings) { //Burası sadece ekrana gösterme ve takip etmek amacıyla yapıldı başlangıçta 1 defa çalışacak bir daha çalışmayacak
      Serial.println("\n--- Sensör Başlangıç Kontrolleri ---");
      Serial.println("Sensörler başarıyla başlatıldı");
      Serial.println("İlk Okunan Değerler:");
      Serial.println("Sıcaklık: " + String(t) + " °C");
      Serial.println("Nem: " + String(h) + " %");
      Serial.println("CO: " + String(co) + " ppm");
      Serial.println("--------------------------------");
      Serial.println("Veri gönderimi loop içinde başlayacak...\n");
    } else {
      Serial.println("!! UYARI: Sensör başlatma başarısız!");
      Serial.println("Sensörlerinizin bağlantılarını kontrol edin.");
    }
  } else {
    Serial.println("WiFi bağlantısı kurulamadı!");
  }
}

void loop() {
  // Sensör ayarlarını periyodik olarak güncelle (15 dakikada bir)
  static unsigned long lastSettingsUpdate = 0; //En son sensor ayarlarını güncelleme
  static unsigned long lastOutputCheck = 0; //En son çıktılara bakma
  unsigned long currentMillis = millis(); //Şu an mevcut zamanı alıyor
  
  // Buzzer'ı güncelle - status 0 olana kadar sürekli çalışacak
  if (buzzerActive) {
    if (currentMillis - lastBuzzerUpdate >= BUZZER_CYCLE_INTERVAL) {
      updateBuzzer();
      lastBuzzerUpdate = currentMillis;
    }
  }
  
  if (currentMillis - lastSettingsUpdate >= SETTINGS_UPDATE_INTERVAL) {
    //Mevcut zamandan - son sensör ayarlarının süresini çıkarıyor ve bu eğer default olarak 1 saniye olarak atadım yukarıda ondan büyükse 
    getSensorSettings(); //Tekrardan veritabanından değerleri her 1dakika da bir okuyor
    lastSettingsUpdate = currentMillis; //şu anki zamanı tekrar en son ayara atıyor ki her 1 dakika da 1 tetiklenmesini sağlamak için
  }
  
  // Sıcaklık kontrolü ve gönderimi
  if (shouldSendSensorData(temperatureSensor, tempSettings)) {
    checkAndSendTemperature(); //Eğer gönder yani gönderme zamanı geldiyse direkt kontrol et ve veriyi veritabanına kaydet gönderiliyor
  }
  //shouldSendSensorData true veya false üretiyor ki o an göndersin mi göndermesin mi diye
  //Aynısı nem ve karbo için de geçerli
  
  // Nem kontrolü ve gönderimi
  if (shouldSendSensorData(humiditySensor, humSettings)) {
    checkAndSendHumidity();
  }
  
  // CO kontrolü ve gönderimi
  if (shouldSendSensorData(coSensor, coSettings)) {
    checkAndSendCO();
  }

  // API'den çıkış kontrollerini her 30 saniyede bir çek
  if (currentMillis - lastOutputCheck >= OUTPUT_CHECK_INTERVAL) {
    //Bu da yukarıda 1 dakika olarak yaptığımız ayarlar kısmında bu sefer çıktılar 0 mı 1 mi değerleri üretiyor olarak algılıyor
    getDataFromAPI(); //Eğer zamanı gelmişse verileri çek
    lastOutputCheck = currentMillis; //son zamanı al
  }
  
  // WiFi bağlantısını kontrol et
  if (WiFi.status() != WL_CONNECTED) { //Herhangi bir mevcut döngü de wifi kesilme sorunu yaşanırsa diye kontrol amaçlı koydum
    Serial.println("WiFi bağlantısı kesildi, yeniden bağlanılıyor...");
    WiFi.begin(ssid, password);
    delay(5000); //her 5 saniye de bir tekrar kontrol ediyor
  }
  
  delay(1000); //1 saniye de 1 de sürekli bu döngü sağlanıyor
}

void checkAndSendTemperature() { //Kontrol et ve verileri veritabanına gönder sıcaklık olanı
  float t = dht.readTemperature();
  if (!isnan(t) && t > -40 && t < 80) { //yukarıda okuduğumz en son değer boş herhangi bir sorun yoksa
    temperatureSensor.lastValue = t; //yukarıda yaptığımız adımları tekrar burada tekrarlıyoruz
    temperatureSensor.lastSendTime = millis();
    temperatureSensor.isValid = true;
    sendSensorData(temperatureSensor); // ve en sonunda tüm her şey tamam olduğunda da gönderiyoruz
  } else { //Eğer zaman içerisinde herhangi bir sorun olumuşsa da sıcaklık okunmadı hatası yolluyoruz
    Serial.println("Sıcaklık okunamadı veya geçersiz değer!");
    temperatureSensor.isValid = false;
  }
}

void checkAndSendHumidity() { //nem olanı
  float h = dht.readHumidity();
  if (!isnan(h) && h >= 0 && h <= 100) {
    humiditySensor.lastValue = h;
    humiditySensor.lastSendTime = millis();
    humiditySensor.isValid = true;
    sendSensorData(humiditySensor);
  } else {
    Serial.println("Nem okunamadı veya geçersiz değer!");
    humiditySensor.isValid = false;
  }
}

void checkAndSendCO() { //karbonmonoksit olanı
  int rawValue = analogRead(MQ7_AOUT_PIN);
  if (rawValue > 0) {
    float ppm = calculateCOppm(rawValue);
    coSensor.lastValue = ppm;
    coSensor.lastSendTime = millis();
    coSensor.isValid = true;
    Serial.print("Ham CO değeri: ");
    Serial.print(rawValue);
    Serial.print(" -> Hesaplanan CO ppm: ");
    Serial.println(ppm);
    sendSensorData(coSensor);
  } else {
    Serial.println("CO okunamadı veya geçersiz değer!");
    coSensor.isValid = false;
  }
}

// sendSensorData fonksiyonu
void sendSensorData(SensorData &sensor) { //Burada sensorü alırsak eğer SensorData dizisinden çekerek direkt onu yollayabiliriz o yüzden SensorData istiyor fonksiyonumuz
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http; //httpden api ile bağlantı sağlayacağım için böyle bir yapı kuruyorum
    WiFiClient client;
    
    // API URL'sini yazdır
    Serial.print("API URL: ");
    Serial.println(apiUrl);
    
    // Zaman aşımı süresini ayarla
    http.setTimeout(HTTP_TIMEOUT); //zaman aşımı süresini yani gönderme de bir sorun olursa 10 saniye beklemesini istiyoruz yukarıda tanımlamasını yaptık
    
    // Bağlantıyı başlat
    if (!http.begin(client, apiUrl)) { //Bağlantıyı bağlatıyoruz bizim kendi api urlme gönderiyorum
      Serial.println("HTTP bağlantısı başlatılamadı!");
      return;
    }
    
    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); //Bunu post olarak göndereceğim onu ayarlıyorum başlıkla

    String postData = sensor.sensorType + "=" + String(sensor.lastValue); //sensor de diziden çektiğimiz değerleri yerine yazıyor
    //postData örneğin şunu tutuyor Sıcaklık=10 ben php tarafından da $_POST["Sıcaklık"] yazarsam eğer bana 10 değerini vermiş oluyor

    // Veriyi yazdır
    Serial.println("Gönderilen veri:");
    Serial.println(sensor.sensorType + ": " + String(sensor.lastValue));

    // Yeniden deneme mekanizması
    int retryCount = 0;
    int httpResponseCode = -1;
    
    while (retryCount < MAX_CONNECTION_RETRIES && httpResponseCode < 0) { //Eğer gönderme sırasında bir hata olursa 
      // İstek başlamadan önce WiFi durumunu kontrol et
      if (WiFi.status() != WL_CONNECTED) { //Öncelikle wifi olmadığı için mi bir sorun çıkıp çıkmadığını kontrol ediyor
        Serial.println("WiFi bağlantısı kesildi, yeniden bağlanılıyor...");
        WiFi.begin(ssid, password); //Tekrardan wifi başlatma durumunu ayarlıyor
        delay(5000); //ve 5 saniye bekliyor
        if (WiFi.status() != WL_CONNECTED) {
          Serial.println("WiFi bağlantısı kurulamadı, istek iptal ediliyor!");
          return;
        }
      }
      
      httpResponseCode = http.POST(postData); //Sonra bize jsondan gelen veriyi alıyoruz ve httpResponseCode'a dönüştürüyoruz ki hani durum ne başarılı mı başarısız mı diye
      
      if (httpResponseCode > 0) { //Eğer bu kod içeriği sıfırdan büyükse yani doluysa hemen ekrana yazdırıyoruz
        String response = http.getString(); // bu gelen verileri hepsini tutmak için response diye bir şey oluşturuyoruz ilerde belki kullanırım diye
        Serial.println("HTTP Yanıt Kodu: " + String(httpResponseCode));
        Serial.println("Yanıt: " + response);
      } else {
        Serial.print("HTTP Hatası: ");
        Serial.println(httpResponseCode);
        Serial.println("Hata açıklaması: " + http.errorToString(httpResponseCode));
        
        retryCount++; //eğer gelmezse tekrar deniyor
        if (retryCount < MAX_CONNECTION_RETRIES) { //Burada artık wifi değil de api ile ardunio arasında iletişim var mı yok mu onu kontrol ediyor
          Serial.println("Yeniden deneniyor... (" + String(retryCount) + "/" + String(MAX_CONNECTION_RETRIES) + ")");
          delay(3000); // Yeniden denemeden önce daha uzun bekle
        }
      }
    }
    
    http.end(); //En sonunda ne olursa olsun bağlantı kısmını kapatıyor
    
    // Tüm denemeler başarısız olduysa
    if (httpResponseCode < 0) { //Kodun eğer bağlantı durumu başarsızısa ya apiye bağlanmamıştır ya da wifi yoktur burada onu kontrol ediyoruz
      Serial.println("API'ye bağlanılamadı, lütfen API URL'sini ve sunucu durumunu kontrol edin!");
    }
  } else {
    Serial.println("Wi-Fi bağlantısı yok, veri gönderilemedi!");
  }
}

float calculateCOppm(int rawValue) {//Burada co ile ilgili hesaplamalar nurşen yaptı.
  // Analog değeri voltaja çevir (0-1023 -> 0-3.3V)
  float voltage = (float)rawValue * (VOLTAGE / 1023.0);
  
  // Sensör direncini (Rs) hesapla
  // Formül: Rs = ((Vc * RL) / Vout) - RL
  float rs = ((VOLTAGE * RL_VALUE) / voltage) - RL_VALUE;
  
  // Rs/R0 oranını hesapla
  float ratio = rs / R0;
  
  // CO ppm değerini hesapla
  // Formül: ppm = 100 * pow(Rs/R0, -1.53)
  // Not: Bu formül MQ-7 datasheet'inden alınmıştır
  float ppm = 100 * pow(ratio, -1.53);
  
  // Değeri makul bir aralıkta tut (0-1000 ppm)
  if (ppm < 0) ppm = 0;
  if (ppm > 1000) ppm = 1000;
  
  return ppm;
}

void getDataFromAPI() {
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n----------------------------------------");
    Serial.println("Firebase'den veri çekme işlemi başlatılıyor...");
    
    int retryCount = 0;
    bool success = false;
    
    while (!success && retryCount < MAX_CONNECTION_RETRIES) {
      if (retryCount > 0) {
        Serial.println("\nYeniden bağlantı deneniyor... (" + String(retryCount + 1) + "/" + String(MAX_CONNECTION_RETRIES) + ")");
        delay(3000);
      }
      
      if (Firebase.ready() && signUpOk) {
        Serial.println("Firebase bağlantısı hazır.");
        
        if (Firebase.RTDB.getString(&fbdo, "/device_outputs")) {
          String payload = fbdo.stringData();
          
          StaticJsonDocument<1024> doc;
          DeserializationError error = deserializeJson(doc, payload);
          
          if (!error) {
            bool dataFound = false;
            Serial.println("Alınan veriler işleniyor...");
            
            // Output Type 1
            if(doc.containsKey("output_type_1")) {
              dataFound = true;
              JsonObject output1 = doc["output_type_1"];
              String type1 = output1["output_type"].as<String>();
              int status1 = output1["status"].as<int>();
              bool isAcknowledged1 = output1.containsKey("is_alarm_acknowledged") ? output1["is_alarm_acknowledged"].as<bool>() : true;
              
              Serial.println("\n>> Output Type 1 Bulundu:");
              Serial.println("   Tip: " + type1);
              Serial.println("   Durum: " + String(status1));
              Serial.println("   Onaylandı: " + String(isAcknowledged1));
              
              // Alarm mantığı kontrolü
              if (status1 == 1) {
                if (!isAcknowledged1) {
                  // Case 1: isAcknowledged False ve status 1 ise alarm çalsın
                  Serial.println("   >> Durum 1: Alarm durum aktif ve onaylanmamış, alarm çalıştırılıyor");
                  controlOutput(type1, 1); // Alarmı çalıştır
                } else {
                  // Case 2: isAcknowledged True ve status 1 ise hiçbir şey yapmasın
                  Serial.println("   >> Durum 2: Alarm durumu aktif fakat kullanıcı tarafından onaylanmış, işlem yok");
                  // Hiçbir şey yapma, sadece log
                }
              } else {
                // Case 3 ve 4: Status 0 ise isAcknowledged değerine bakmadan kapat
                Serial.println("   >> Durum 3/4: Alarm durumu aktif değil, alarm kapatılıyor");
                controlOutput(type1, 0); // Alarmı kapat
              }
            }
            
            // Output Type 2
            if(doc.containsKey("output_type_2")) {
              dataFound = true;
              JsonObject output2 = doc["output_type_2"];
              String type2 = output2["output_type"].as<String>();
              int status2 = output2["status"].as<int>();
              bool isAcknowledged2 = output2.containsKey("is_alarm_acknowledged") ? output2["is_alarm_acknowledged"].as<bool>() : true;
              
              Serial.println("\n>> Output Type 2 Bulundu:");
              Serial.println("   Tip: " + type2);
              Serial.println("   Durum: " + String(status2));
              Serial.println("   Onaylandı: " + String(isAcknowledged2));
              
              // Alarm mantığı kontrolü
              if (status2 == 1) {
                if (!isAcknowledged2) {
                  // Case 1: isAcknowledged False ve status 1 ise alarm çalsın
                  Serial.println("   >> Durum 1: Alarm durum aktif ve onaylanmamış, alarm çalıştırılıyor");
                  controlOutput(type2, 1); // Alarmı çalıştır
                } else {
                  // Case 2: isAcknowledged True ve status 1 ise hiçbir şey yapmasın
                  Serial.println("   >> Durum 2: Alarm durumu aktif fakat kullanıcı tarafından onaylanmış, işlem yok");
                  // Hiçbir şey yapma, sadece log
                }
              } else {
                // Case 3 ve 4: Status 0 ise isAcknowledged değerine bakmadan kapat
                Serial.println("   >> Durum 3/4: Alarm durumu aktif değil, alarm kapatılıyor");
                controlOutput(type2, 0); // Alarmı kapat
              }
            }
            
            // Output Type 3
            if(doc.containsKey("output_type_3")) {
              dataFound = true;
              JsonObject output3 = doc["output_type_3"];
              String type3 = output3["output_type"].as<String>();
              int status3 = output3["status"].as<int>();
              bool isAcknowledged3 = output3.containsKey("is_alarm_acknowledged") ? output3["is_alarm_acknowledged"].as<bool>() : true;
              
              Serial.println("\n>> Output Type 3 Bulundu:");
              Serial.println("   Tip: " + type3);
              Serial.println("   Durum: " + String(status3));
              Serial.println("   Onaylandı: " + String(isAcknowledged3));
              
              // Alarm mantığı kontrolü
              if (status3 == 1) {
                if (!isAcknowledged3) {
                  // Case 1: isAcknowledged False ve status 1 ise alarm çalsın
                  Serial.println("   >> Durum 1: Alarm durum aktif ve onaylanmamış, alarm çalıştırılıyor");
                  controlOutput(type3, 1); // Alarmı çalıştır
                } else {
                  // Case 2: isAcknowledged True ve status 1 ise hiçbir şey yapmasın
                  Serial.println("   >> Durum 2: Alarm durumu aktif fakat kullanıcı tarafından onaylanmış, işlem yok");
                  // Hiçbir şey yapma, sadece log
                }
              } else {
                // Case 3 ve 4: Status 0 ise isAcknowledged değerine bakmadan kapat
                Serial.println("   >> Durum 3/4: Alarm durumu aktif değil, alarm kapatılıyor");
                controlOutput(type3, 0); // Alarmı kapat
              }
            }
            
            if (!dataFound) {
              Serial.println("!! UYARI: Veritabanında hiçbir çıkış tipi bulunamadı!");
            }
            
            success = true;
          } else {
            Serial.println("!! HATA: JSON Parse Hatası: " + String(error.c_str()));
            Serial.println("Ham Veri: " + payload);
            retryCount++;
          }
        } else {
          Serial.println("!! HATA: Firebase'den veri çekilemedi: " + fbdo.errorReason());
          retryCount++;
        }
      } else {
        Serial.println("!! HATA: Firebase bağlantısı hazır değil veya giriş yapılmamış!");
        retryCount++;
      }
      
      if (!success && retryCount < MAX_CONNECTION_RETRIES) {
        delay(3000);
      }
    }
    
    if (!success) {
      Serial.println("\n!! HATA: Maksimum deneme sayısına ulaşıldı. Veri çekme başarısız!");
    }
    
    Serial.println("----------------------------------------\n");
  } else {
    Serial.println("!! HATA: WiFi bağlantısı yok, veri çekilemedi!");
  }
}

void getSensorSettings() { //Aslında yukarıda yaptığımzın aynısını bu sefer burada sensor ayarları için yapıyoruz
//Burada tek fark hepsini bir dizi içerisinde tutup diziden elemanları direkt çekiyoruz hani bir fonksiyona götürüp kontrol etmiyoruz
//Dizi de tutmamızın avantajı da direkt yukarıda kontrolleri sağlayıp ardından gönderebiliyorum
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n----------------------------------------");
    Serial.println("Sensör ayarları çekiliyor...");
    
    if (Firebase.ready() && signUpOk) {
      Serial.println("Firebase bağlantısı hazır.");
      
      // Sensör ayarlarını çek
      bool success = false;
      int retryCount = 0;
      
      while (!success && retryCount < MAX_CONNECTION_RETRIES) {
        if (Firebase.RTDB.getString(&fbdo, "/user_sensors_settings")) {
          String payload = fbdo.stringData();
          
          StaticJsonDocument<1024> doc;
          DeserializationError error = deserializeJson(doc, payload);
          
          if (!error) {
            // Sıcaklık sensörü ayarları
            if (doc.containsKey("sensor_settings_1")) {
              JsonObject settings1 = doc["sensor_settings_1"];
              tempSettings.sensorId = settings1["sensor_id"].as<String>();
              tempSettings.intervalMin = settings1["interval_min"].as<unsigned long>();
              tempSettings.isConfigured = true;
              
              Serial.println("\nSıcaklık Sensörü Ayarları:");
              Serial.println("Sensör ID: " + tempSettings.sensorId);
              Serial.println("Aralık: " + String(tempSettings.intervalMin) + " dakika");
            }
            
            // Nem sensörü ayarları
            if (doc.containsKey("sensor_settings_2")) {
              JsonObject settings2 = doc["sensor_settings_2"];
              humSettings.sensorId = settings2["sensor_id"].as<String>();
              humSettings.intervalMin = settings2["interval_min"].as<unsigned long>();
              humSettings.isConfigured = true;
              
              Serial.println("\nNem Sensörü Ayarları:");
              Serial.println("Sensör ID: " + humSettings.sensorId);
              Serial.println("Aralık: " + String(humSettings.intervalMin) + " dakika");
            }
            
            // CO sensörü ayarları
            if (doc.containsKey("sensor_settings_3")) {
              JsonObject settings3 = doc["sensor_settings_3"];
              coSettings.sensorId = settings3["sensor_id"].as<String>();
              coSettings.intervalMin = settings3["interval_min"].as<unsigned long>();
              coSettings.isConfigured = true;
              
              Serial.println("\nCO Sensörü Ayarları:");
              Serial.println("Sensör ID: " + coSettings.sensorId);
              Serial.println("Aralık: " + String(coSettings.intervalMin) + " dakika");
            }
            
            success = true;
            Serial.println("\nSensör ayarları başarıyla güncellendi!");
          } else {
            Serial.println("JSON Parse Hatası: " + String(error.c_str()));
            retryCount++;
          }
        } else {
          Serial.println("Firebase'den sensör ayarları çekilemedi: " + fbdo.errorReason());
          retryCount++;
        }
        
        if (!success && retryCount < MAX_CONNECTION_RETRIES) {
          Serial.println("Yeniden deneniyor... (" + String(retryCount + 1) + "/" + String(MAX_CONNECTION_RETRIES) + ")");
          delay(3000);
        }
      }
      
      if (!success) {
        Serial.println("!! UYARI: Sensör ayarları çekilemedi, varsayılan değerler kullanılacak!");
      }
    } else {
      Serial.println("!! HATA: Firebase bağlantısı hazır değil!");
    }
    Serial.println("----------------------------------------\n");
  } else {
    Serial.println("!! HATA: WiFi bağlantısı yok!");
  }
}

// Sensör verisi gönderme kontrolü
bool shouldSendSensorData(SensorData &sensor, SensorSettings &settings) { //Acaba veriyi gönderse mi göndermesem mi sorgusu
  if (!settings.isConfigured) return false; //Eğer ayarlar da herhangi bir veri daha gelmemişse veriyi gönderme
  
  unsigned long currentTime = millis(); //şu an mevcut zamanı al
  unsigned long interval = settings.intervalMin * 60 * 1000; // Dakikayı milisaniyeye çevir
  //milisaniye üzerinden işlem yapacağımız için dakikayı milisaniyeye çeviriyorum
  return (currentTime - sensor.lastSendTime) >= interval;
  //burada gönderirken eğer mevcut zaman son gönderilen zamandan çıkarıp bize gelen dakikadan büyükse gönderme işlemi yapacağız mesela
  //zaman 10 aldım son gönderilen zaman da 2
  //interval değerim de 10 o zaman veriyi göndermez çünkü 8 10dan büyük değil
}