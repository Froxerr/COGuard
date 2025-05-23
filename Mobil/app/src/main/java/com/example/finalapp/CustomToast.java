package com.example.finalapp;

import android.content.Context;
import android.graphics.drawable.Drawable;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.ContextCompat;

public class CustomToast {
    
    public static final int TYPE_INFO = 0;
    public static final int TYPE_SUCCESS = 1;
    public static final int TYPE_WARNING = 2;
    public static final int TYPE_ERROR = 3;
    
    /**
     * Modern görünümlü, animasyonlu ve ikona sahip özel bir Toast mesajı gösterir.
     * 
     * @param context Uygulama bağlamı
     * @param message Gösterilecek mesaj
     * @param duration Toast süresi (Toast.LENGTH_SHORT veya Toast.LENGTH_LONG)
     * @param toastType Mesaj tipi (TYPE_INFO, TYPE_SUCCESS, TYPE_WARNING, TYPE_ERROR)
     */
    public static void showToast(Context context, String message, int duration, int toastType) {
        LayoutInflater inflater = LayoutInflater.from(context);
        View layout = inflater.inflate(R.layout.custom_toast_layout, null);
        
        // Arka plan belirleme
        switch (toastType) {
            case TYPE_SUCCESS:
                layout.setBackgroundResource(R.drawable.toast_success_background);
                break;
            case TYPE_WARNING:
                layout.setBackgroundResource(R.drawable.toast_warning_background);
                break;
            case TYPE_ERROR:
                layout.setBackgroundResource(R.drawable.toast_error_background);
                break;
            default:
                layout.setBackgroundResource(R.drawable.toast_background);
                break;
        }
        
        // İkon belirleme
        ImageView iconView = layout.findViewById(R.id.toast_icon);
        int iconResId = getIconForType(toastType);
        iconView.setImageResource(iconResId);
        
        // Metin ayarlama
        TextView text = layout.findViewById(R.id.toast_text);
        text.setText(message);
        
        // Toast oluşturma
        Toast toast = new Toast(context);
        toast.setGravity(Gravity.BOTTOM | Gravity.CENTER_HORIZONTAL, 0, 100);
        toast.setDuration(duration);
        toast.setView(layout);
        
        // Animasyon ekleme
        Animation animation = AnimationUtils.loadAnimation(context, R.anim.toast_fade_in);
        layout.startAnimation(animation);
        
        toast.show();
    }
    
    /**
     * Standart bir bilgi mesajı gösterir.
     */
    public static void showInfo(Context context, String message) {
        showToast(context, message, Toast.LENGTH_SHORT, TYPE_INFO);
    }
    
    /**
     * Başarılı işlem bildirimi gösterir.
     */
    public static void showSuccess(Context context, String message) {
        showToast(context, message, Toast.LENGTH_SHORT, TYPE_SUCCESS);
    }
    
    /**
     * Uyarı mesajı gösterir.
     */
    public static void showWarning(Context context, String message) {
        showToast(context, message, Toast.LENGTH_SHORT, TYPE_WARNING);
    }
    
    /**
     * Hata mesajı gösterir.
     */
    public static void showError(Context context, String message) {
        showToast(context, message, Toast.LENGTH_SHORT, TYPE_ERROR);
    }
    
    /**
     * Uzun süreli bilgi mesajı gösterir.
     */
    public static void showLongInfo(Context context, String message) {
        showToast(context, message, Toast.LENGTH_LONG, TYPE_INFO);
    }
    
    private static int getIconForType(int toastType) {
        switch (toastType) {
            case TYPE_SUCCESS:
                return android.R.drawable.ic_menu_info_details;
            case TYPE_WARNING:
                return android.R.drawable.ic_dialog_alert;
            case TYPE_ERROR:
                return android.R.drawable.ic_delete;
            default:
                return android.R.drawable.ic_dialog_info;
        }
    }
} 