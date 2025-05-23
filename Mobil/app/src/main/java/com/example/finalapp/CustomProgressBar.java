package com.example.finalapp;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Paint;
import android.graphics.RectF;
import android.util.AttributeSet;
import android.view.MotionEvent;
import android.view.View;
import android.animation.ValueAnimator;
import android.animation.AnimatorSet;
import android.animation.ObjectAnimator;
import android.view.animation.AccelerateDecelerateInterpolator;
import android.view.animation.DecelerateInterpolator;
import android.view.animation.OvershootInterpolator;
import android.graphics.Color;
import android.graphics.LinearGradient;
import android.graphics.Shader;
import android.graphics.RadialGradient;
import android.graphics.DashPathEffect;
import android.graphics.Typeface;

public class CustomProgressBar extends View {
    private Paint backgroundPaint;
    private Paint progressPaint;
    private Paint thresholdPaint;
    private Paint thresholdActivePaint;
    private Paint thresholdGlowPaint;
    private Paint shadowPaint;
    private Paint thresholdHandlePaint;
    private Paint bubblePaint;
    private Paint bubbleTextPaint;
    private Paint thresholdValueTextPaint;
    private Paint thresholdValueBgPaint;

    private int viewId;

    private float maxValue = 100f;
    private float currentValue = 0f;
    private float thresholdValue = 0f;
    private float previousThresholdValue = 0f;
    
    private boolean isDraggingThreshold = false;
    private boolean isThresholdActive = false;
    private float thresholdTouchAreaWidth = 60f; // Daha geniş dokunma alanı
    private ThresholdChangeListener thresholdChangeListener;
    
    // Animasyon için değişkenler
    private ValueAnimator thresholdAnimator;
    private float thresholdScaleFactor = 1.0f;
    private RectF touchFeedbackRect = new RectF();
    private float rippleRadius = 0f;
    private ValueAnimator rippleAnimator;
    private float bubbleScale = 0f;
    private ValueAnimator bubbleAnimator;
    
    // Performans için değişkenler
    private long lastUpdateTime = 0;
    private static final long UPDATE_THROTTLE_MS = 8; // 120fps için ideal yenileme hızı
    
    // Arayüz tanımı
    public interface ThresholdChangeListener {
        void onThresholdChanged(CustomProgressBar progressBar, float thresholdValue);
        void onThresholdChangeFinished(CustomProgressBar progressBar, float thresholdValue);
    }

    public CustomProgressBar(Context context) {
        super(context);
        init();
    }

    public CustomProgressBar(Context context, AttributeSet attrs) {
        super(context, attrs);
        init();
    }

    public CustomProgressBar(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        init();
    }

    private void init() {
        // Arkaplan
        backgroundPaint = new Paint();
        backgroundPaint.setColor(0xFFE0E0E0); // Gri arkaplan
        backgroundPaint.setStyle(Paint.Style.FILL);
        backgroundPaint.setAntiAlias(true);

        // İlerleme çubuğu
        progressPaint = new Paint();
        progressPaint.setColor(0xFF3AA0FF); // Mavi progress
        progressPaint.setStyle(Paint.Style.FILL);
        progressPaint.setAntiAlias(true);

        // Eşik çizgisi
        thresholdPaint = new Paint();
        thresholdPaint.setColor(0xFFFF0000); // Kırmızı threshold çizgisi
        thresholdPaint.setStyle(Paint.Style.FILL);
        thresholdPaint.setAntiAlias(true);
        
        // Aktif eşik çizgisi
        thresholdActivePaint = new Paint();
        thresholdActivePaint.setColor(0xFFFF5252); // Daha parlak kırmızı (aktif durum)
        thresholdActivePaint.setStyle(Paint.Style.FILL);
        thresholdActivePaint.setAntiAlias(true);
        
        // Gölge
        shadowPaint = new Paint();
        shadowPaint.setColor(0x40000000); // Yarı saydam siyah
        shadowPaint.setStyle(Paint.Style.FILL);
        shadowPaint.setAntiAlias(true);
        
        // Tutamaç (handle)
        thresholdHandlePaint = new Paint();
        thresholdHandlePaint.setColor(0xFFFFFFFF); // Beyaz
        thresholdHandlePaint.setStyle(Paint.Style.FILL);
        thresholdHandlePaint.setAntiAlias(true);
        
        // Işıldama efekti
        thresholdGlowPaint = new Paint();
        thresholdGlowPaint.setStyle(Paint.Style.FILL);
        thresholdGlowPaint.setAntiAlias(true);
        
        // Değer baloncuğu
        bubblePaint = new Paint();
        bubblePaint.setColor(0xFFFF5252); // Kırmızı baloncuk
        bubblePaint.setStyle(Paint.Style.FILL);
        bubblePaint.setAntiAlias(true);
        
        // Değer baloncuğu metni
        bubbleTextPaint = new Paint();
        bubbleTextPaint.setColor(Color.WHITE);
        bubbleTextPaint.setTextSize(32f);
        bubbleTextPaint.setTextAlign(Paint.Align.CENTER);
        bubbleTextPaint.setTypeface(Typeface.DEFAULT_BOLD);
        bubbleTextPaint.setAntiAlias(true);
        
        // Threshold değeri metni (sürekli gösterilecek)
        thresholdValueTextPaint = new Paint();
        thresholdValueTextPaint.setColor(Color.WHITE);
        thresholdValueTextPaint.setTextSize(20f); // Daha küçük ve basit
        thresholdValueTextPaint.setTextAlign(Paint.Align.CENTER);
        thresholdValueTextPaint.setTypeface(Typeface.DEFAULT_BOLD);
        thresholdValueTextPaint.setAntiAlias(true);
        
        // Threshold değeri arka planı
        thresholdValueBgPaint = new Paint();
        thresholdValueBgPaint.setColor(0xFFD32F2F); // Koyu kırmızı
        thresholdValueBgPaint.setStyle(Paint.Style.FILL);
        thresholdValueBgPaint.setAntiAlias(true);
        
        // Dokunma olaylarını etkinleştir
        setClickable(true);
        setFocusable(true);
    }

    @Override
    protected void onAttachedToWindow() {
        super.onAttachedToWindow();
        viewId = getId();
    }

    @Override
    protected void onDraw(Canvas canvas) {
        super.onDraw(canvas);

        float width = getWidth();
        float height = getHeight();
        float cornerRadius = height/2;  // Tüm progress bar'lar için aynı yuvarlaklık

        RectF rect = new RectF(0, 0, width, height);

        // Arkaplanı çiz (gradient ile)
        int[] backgroundColors = {0xFFE0E0E0, 0xFFF5F5F5, 0xFFE0E0E0};
        float[] gradientPositions = {0f, 0.5f, 1f};
        backgroundPaint.setShader(new LinearGradient(0, 0, 0, height, 
                                                  backgroundColors, 
                                                  gradientPositions, 
                                                  Shader.TileMode.CLAMP));
        canvas.drawRoundRect(rect, cornerRadius, cornerRadius, backgroundPaint);

        // Progress'i çiz (gradient ile)
        float progressWidth = (currentValue / maxValue) * width;
        RectF progressRect = new RectF(0, 0, progressWidth, height);
        int[] progressColors = {0xFF64B5F6, 0xFF2196F3, 0xFF1976D2};
        progressPaint.setShader(new LinearGradient(0, 0, progressWidth, 0, 
                                                progressColors, 
                                                null, 
                                                Shader.TileMode.CLAMP));
        canvas.drawRoundRect(progressRect, cornerRadius, cornerRadius, progressPaint);

        // Threshold çizgisini çiz
        float thresholdX = (thresholdValue / maxValue) * width;
        float thresholdWidth = isThresholdActive ? 8f * thresholdScaleFactor : 4f; // Aktifken daha kalın
        float thresholdHeight = isThresholdActive ? height * 1.8f * thresholdScaleFactor : height * 1.4f;
        
        // Eşik gölgesini çiz
        if (isThresholdActive) {
            shadowPaint.setAlpha(100);
            canvas.drawCircle(thresholdX, height/2, thresholdWidth * 2.5f, shadowPaint);
        }
        
        // Threshold dikdörtgeni
        touchFeedbackRect.set(
            thresholdX - thresholdWidth/2, 
            height/2 - thresholdHeight/2, 
            thresholdX + thresholdWidth/2, 
            height/2 + thresholdHeight/2
        );
        
        // Aktif veya pasif duruma göre çiz (gradient ile)
        if (isThresholdActive) {
            int[] thresholdColors = {0xFFFF8A80, 0xFFFF5252, 0xFFD50000};
            thresholdActivePaint.setShader(new LinearGradient(
                touchFeedbackRect.left, 0, 
                touchFeedbackRect.right, 0,
                thresholdColors, 
                null, 
                Shader.TileMode.CLAMP));
                
            // Işıldama efekti
            if (rippleRadius > 0) {
                thresholdGlowPaint.setShader(new RadialGradient(
                    thresholdX, height/2, rippleRadius,
                    new int[]{0x60FF5252, 0x20FF5252, 0x00FF5252},
                    null, Shader.TileMode.CLAMP
                ));
                canvas.drawCircle(thresholdX, height/2, rippleRadius, thresholdGlowPaint);
            }
        } else {
            int[] thresholdColors = {0xFFEF9A9A, 0xFFE57373, 0xFFEF5350};
            thresholdPaint.setShader(new LinearGradient(
                touchFeedbackRect.left, 0, 
                touchFeedbackRect.right, 0,
                thresholdColors, 
                null, 
                Shader.TileMode.CLAMP));
        }
        
        // Eşik çubuğu çizimi
        canvas.drawRoundRect(touchFeedbackRect, 8f, 8f, 
                           isThresholdActive ? thresholdActivePaint : thresholdPaint);
        
        // Tutamaç (handle) çizimi - sürükleme işlemini daha anlaşılır hale getirir
        if (isThresholdActive) {
            float handleRadius = thresholdWidth * 2.0f;
            canvas.drawCircle(thresholdX, height/2, handleRadius, thresholdHandlePaint);
            
            // Tutamaca görsel efekt ekle
            Paint handleDetailPaint = new Paint(thresholdActivePaint);
            handleDetailPaint.setStyle(Paint.Style.STROKE);
            handleDetailPaint.setStrokeWidth(2f);
            handleDetailPaint.setShader(null);
            canvas.drawCircle(thresholdX, height/2, handleRadius * 0.7f, handleDetailPaint);
        }
        
        // Sürükleme sırasında değeri göster - Büyüteç tarzı baloncuk (aktifse ve sürükleniyorsa)
        if (isDraggingThreshold && bubbleScale > 0) {
            // Gerçek değeri hesapla
            float actualValue = thresholdValue;
            String valueText = String.format("%.1f", actualValue);
            
            // Baloncuk boyutu ve konumu
            float bubbleRadius = 40f * bubbleScale;
            float bubbleY = height/2 - bubbleRadius * 1.5f;
            
            // Baloncuk arka planını çiz
            bubblePaint.setShader(new RadialGradient(
                thresholdX, bubbleY, bubbleRadius,
                new int[]{0xFFFF5252, 0xFFFF1744}, 
                new float[]{0.7f, 1.0f},
                Shader.TileMode.CLAMP
            ));
            
            // Baloncuk gölgesi
            shadowPaint.setAlpha(50);
            canvas.drawCircle(thresholdX, bubbleY + 5, bubbleRadius, shadowPaint);
            
            // Baloncuk çizimi
            canvas.drawCircle(thresholdX, bubbleY, bubbleRadius, bubblePaint);
            
            // Üçgen gösterge
            float triangleSize = 12f * bubbleScale;
            float[] trianglePoints = new float[] {
                thresholdX, bubbleY + bubbleRadius,  // Üst
                thresholdX - triangleSize, bubbleY + bubbleRadius - triangleSize,  // Sol alt
                thresholdX + triangleSize, bubbleY + bubbleRadius - triangleSize   // Sağ alt
            };
            
            Paint trianglePaint = new Paint(bubblePaint);
            trianglePaint.setShader(null);
            trianglePaint.setColor(0xFFFF1744);
            
            // Üçgen path
            android.graphics.Path trianglePath = new android.graphics.Path();
            trianglePath.moveTo(trianglePoints[0], trianglePoints[1]);
            trianglePath.lineTo(trianglePoints[2], trianglePoints[3]);
            trianglePath.lineTo(trianglePoints[4], trianglePoints[5]);
            trianglePath.close();
            
            canvas.drawPath(trianglePath, trianglePaint);
            
            // Değer metnini çiz
            canvas.drawText(valueText, thresholdX, bubbleY + 12f, bubbleTextPaint);
        }
        
        // EN SON OLARAK: Threshold değerini basit bir şekilde göster
        // Bu şekilde her zaman en üstte olacak
        drawSimpleThresholdValue(canvas, thresholdX, height, thresholdHeight);
    }
    
    // Basit ve doğrudan threshold değerini gösteren metod
    private void drawSimpleThresholdValue(Canvas canvas, float thresholdX, float height, float thresholdHeight) {
        // Değeri formatla
        String thresholdText = String.format("%.1f", thresholdValue);
        
        // Değer arka planı için boyutlar
        float textWidth = thresholdValueTextPaint.measureText(thresholdText);
        float bgWidth = textWidth + 24f; // Daha geniş arka plan
        float bgHeight = 28f; // Daha yüksek arka plan
        
        // ÇUBUĞUN İÇİNE yerleştir
        float bgX = thresholdX;
        float bgY = height/2; // Tam ortalı
        
        // Modern arka plan çiz
        RectF bgRect = new RectF(
            bgX - bgWidth/2,
            bgY - bgHeight/2,
            bgX + bgWidth/2,
            bgY + bgHeight/2
        );
        
        // Arka plan gölgesi - daha belirgin
        shadowPaint.setAlpha(80);
        canvas.drawRoundRect(new RectF(
            bgRect.left + 2f,
            bgRect.top + 2f,
            bgRect.right + 2f,
            bgRect.bottom + 2f
        ), 14f, 14f, shadowPaint);
        
        // Değer arka planı - ​gradient ile daha modern görünüm
        Paint gradientPaint = new Paint(thresholdValueBgPaint);
        gradientPaint.setShader(new LinearGradient(
            bgRect.left, bgRect.top,
            bgRect.right, bgRect.bottom,
            new int[] {0xFFEF5350, 0xFFD32F2F},
            null, Shader.TileMode.CLAMP
        ));
        
        // Daha yuvarlatılmış köşeler
        canvas.drawRoundRect(bgRect, 14f, 14f, gradientPaint);
        
        // Hafif parlak kenar
        Paint strokePaint = new Paint();
        strokePaint.setColor(0x40FFFFFF); // Yarı şeffaf beyaz
        strokePaint.setStyle(Paint.Style.STROKE);
        strokePaint.setStrokeWidth(1.5f);
        strokePaint.setAntiAlias(true);
        canvas.drawRoundRect(bgRect, 14f, 14f, strokePaint);
        
        // Değer metnini çiz - daha büyük ve net
        thresholdValueTextPaint.setTextSize(22f); // Daha büyük yazı
        float textY = bgY + 8f; // Dikey hizalama
        canvas.drawText(thresholdText, bgX, textY, thresholdValueTextPaint);
    }

    @Override
    public boolean onTouchEvent(MotionEvent event) {
        float x = event.getX();
        float width = getWidth();
        float thresholdX = (thresholdValue / maxValue) * width;
        
        // Dokunma konumunun eşik değerine olan mesafesini kontrol et
        boolean isNearThreshold = Math.abs(x - thresholdX) < thresholdTouchAreaWidth;
        
        switch (event.getAction()) {
            case MotionEvent.ACTION_DOWN:
                if (isNearThreshold) {
                    isDraggingThreshold = true;
                    isThresholdActive = true;
                    previousThresholdValue = thresholdValue;
                    
                    // Animasyonları başlat
                    animateThresholdScale(1.0f, 1.3f);
                    startRippleAnimation();
                    startBubbleAnimation(0f, 1f);
                    
                    // Performans için bayrağı set et
                    lastUpdateTime = System.currentTimeMillis();
                    return true;
                }
                break;
                
            case MotionEvent.ACTION_MOVE:
                if (isDraggingThreshold) {
                    // Hızlı yanıt için her zaman güncelle
                    // Yeni eşik değerini hesapla (sınırlar içinde)
                    float newThreshold = (x / width) * maxValue;
                    newThreshold = Math.max(0, Math.min(maxValue, newThreshold));
                    
                    // Değer değiştiyse güncelle
                    if (newThreshold != thresholdValue) {
                        setThresholdValue(newThreshold);
                        
                        // Değişikliği dinleyiciye bildir
                        if (thresholdChangeListener != null) {
                            thresholdChangeListener.onThresholdChanged(this, newThreshold);
                        }
                    }
                    
                    // Her dokunuşta tekrar invalidate et (daha akıcı görüntü için)
                    invalidate();
                    return true;
                }
                break;
                
            case MotionEvent.ACTION_UP:
            case MotionEvent.ACTION_CANCEL:
                if (isDraggingThreshold) {
                    isDraggingThreshold = false;
                    isThresholdActive = false;
                    
                    // Animasyonu geri döndür
                    animateThresholdScale(thresholdScaleFactor, 1.0f);
                    stopRippleAnimation();
                    startBubbleAnimation(bubbleScale, 0f);
                    
                    // Değişikliğin bittiğini dinleyiciye bildir
                    if (thresholdChangeListener != null) {
                        // Değer gerçekten değiştiyse bildirim gönder
                        if (thresholdValue != previousThresholdValue) {
                            thresholdChangeListener.onThresholdChangeFinished(this, thresholdValue);
                        }
                    }
                    return true;
                }
                break;
        }
        
        return super.onTouchEvent(event);
    }
    
    private void animateThresholdScale(float from, float to) {
        if (thresholdAnimator != null && thresholdAnimator.isRunning()) {
            thresholdAnimator.cancel();
        }
        
        thresholdAnimator = ValueAnimator.ofFloat(from, to);
        thresholdAnimator.setDuration(150); // Daha hızlı animasyon
        thresholdAnimator.setInterpolator(new DecelerateInterpolator());
        thresholdAnimator.addUpdateListener(animation -> {
            thresholdScaleFactor = (float) animation.getAnimatedValue();
            invalidate();
        });
        thresholdAnimator.start();
    }
    
    private void startRippleAnimation() {
        if (rippleAnimator != null && rippleAnimator.isRunning()) {
            rippleAnimator.cancel();
        }
        
        rippleAnimator = ValueAnimator.ofFloat(0f, getHeight() * 1.2f);
        rippleAnimator.setDuration(700);
        rippleAnimator.setInterpolator(new DecelerateInterpolator());
        rippleAnimator.setRepeatCount(ValueAnimator.INFINITE);
        rippleAnimator.setRepeatMode(ValueAnimator.RESTART);
        rippleAnimator.addUpdateListener(animation -> {
            rippleRadius = (float) animation.getAnimatedValue();
            invalidate();
        });
        rippleAnimator.start();
    }
    
    private void stopRippleAnimation() {
        if (rippleAnimator != null) {
            rippleAnimator.cancel();
            rippleRadius = 0f;
            invalidate();
        }
    }
    
    private void startBubbleAnimation(float from, float to) {
        if (bubbleAnimator != null && bubbleAnimator.isRunning()) {
            bubbleAnimator.cancel();
        }
        
        bubbleAnimator = ValueAnimator.ofFloat(from, to);
        bubbleAnimator.setDuration(200);
        bubbleAnimator.setInterpolator(new OvershootInterpolator());
        bubbleAnimator.addUpdateListener(animation -> {
            bubbleScale = (float) animation.getAnimatedValue();
            invalidate();
        });
        bubbleAnimator.start();
    }

    public void setMaxValue(float maxValue) {
        this.maxValue = maxValue;
        invalidate();
    }

    public void setCurrentValue(float currentValue) {
        this.currentValue = currentValue;
        invalidate();
    }

    public void setThresholdValue(float thresholdValue) {
        this.thresholdValue = thresholdValue;
        invalidate();
    }
    
    public float getThresholdValue() {
        return thresholdValue;
    }
    
    public void setThresholdChangeListener(ThresholdChangeListener listener) {
        this.thresholdChangeListener = listener;
    }
    
    public void setThresholdTouchAreaWidth(float width) {
        this.thresholdTouchAreaWidth = width;
    }

    // Yukarı yuvarlanmış değeri döndüren fonksiyon
    public float getRoundedThresholdValue() {
        return (float) Math.ceil(thresholdValue);
    }
} 