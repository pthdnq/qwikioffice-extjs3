package my.appb;

import com.phonegap.DroidGap;

//import android.content.Intent;
import android.content.res.Configuration;
import android.os.Bundle;

public class myappB extends DroidGap {
    /** Called when the activity is first created. */
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
         
        super.loadUrl("file:///android_asset/www/index.html");
        
    }
    
    @Override
    public void onConfigurationChanged(Configuration newConfig){        
        super.onConfigurationChanged(newConfig);
    }

}