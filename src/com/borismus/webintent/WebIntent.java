package com.borismus.webintent;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.ListIterator;
import java.util.Map;
import java.lang.Object;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.ContentResolver;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.os.Parcelable;
import android.provider.MediaStore;
import android.provider.MediaStore.Images;
import android.util.Log;

import com.phonegap.api.Plugin;
import com.phonegap.api.PluginResult;

/**
 * WebIntent is a PhoneGap plugin that bridges Android intents and web applications:
 *  
 * 1. web apps can spawn intents that call native Android applications. 
 * 2. (after setting up correct intent filters for PhoneGap applications), Android
 * 	  intents can be handled by PhoneGap web applications.
 * 
 * @author boris@borismus.com
 *
 */
public class WebIntent extends Plugin {

	/**
	 * Executes the request and returns PluginResult.
	 * 
	 * @param action 		The action to execute.
	 * @param args 			JSONArray of arguments for the plugin.
	 * @param callbackId	The callback id used when calling back into JavaScript.
	 * @return 				A PluginResult object with a status and message.
	 */
	public PluginResult execute(String action, JSONArray args, String callbackId) {
		try {
			if (action.equals("startActivity")) {
				if(args.length() != 1) {
					return new PluginResult(PluginResult.Status.INVALID_ACTION);
				}
				
				// Parse the arguments
				JSONObject obj = args.getJSONObject(0);
				String type = obj.has("type") ? obj.getString("type") : null;
				Uri uri = obj.has("url") ? Uri.parse(obj.getString("url")) : null;
				JSONObject extras = obj.has("extras") ? obj.getJSONObject("extras") : null;
				Map<String, String> extrasMap = new HashMap<String, String>();
				
				// Populate the extras if any exist
				if (extras != null) {
					JSONArray extraNames = extras.names();
					for (int i = 0; i < extraNames.length(); i++) {
						String key = extraNames.getString(i);
						String value = extras.getString(key);
						extrasMap.put(key, value);
					}
				}
				
				startActivity(obj.getString("action"), uri, type, extrasMap);
				return new PluginResult(PluginResult.Status.OK);
				
			} else if (action.equals("hasExtra")) {
				if (args.length() != 1) {
					
					return new PluginResult(PluginResult.Status.INVALID_ACTION);
				}
				Intent i = this.ctx.getIntent();
				String extraName = args.getString(0);
				return new PluginResult(PluginResult.Status.OK, i.hasExtra(extraName));
				
			} else if (action.equals("getExtra")) {
				
				if (args.length() != 1) {
					return new PluginResult(PluginResult.Status.INVALID_ACTION);
				}
				
				Intent i = this.ctx.getIntent();
				String extraName = args.getString(0);
				if (i.hasExtra(extraName)) {
					//return new PluginResult(PluginResult.Status.OK, "test");
					
					Bundle extras = i.getExtras();
				
					/*
					*/
					if (i.getAction().equals("android.intent.action.SEND_MULTIPLE")) {
						
						ArrayList<Parcelable> uris = extras.getParcelableArrayList(Intent.EXTRA_STREAM);
						ListIterator<Parcelable> itr = uris.listIterator();
						ArrayList<JSONObject> jsoa = new ArrayList<JSONObject>();
						
						while (itr.hasNext()) {	
							JSONObject jso = new JSONObject();
							Uri element = (Uri) itr.next();
							jso.put("uri",  element);
							jso.put("type", this.ctx.getContentResolver().getType(element));
							
							jsoa.add(jso);
						} 
						
						return new PluginResult(PluginResult.Status.OK, jsoa.toString());
						//return new PluginResult(PluginResult.Status.OK, al.toString());
					}
					
					if (i.getAction().equals("android.intent.action.SEND")) {
						Uri uri = (Uri) extras.getParcelable(Intent.EXTRA_STREAM);
						
						return new PluginResult(PluginResult.Status.OK,uri.toString());
					}
						
				} else {
					return new PluginResult(PluginResult.Status.ERROR); 
				}
			} else if (action.equals("getDataString")) {
				if (args.length() != 0) {
					return new PluginResult(PluginResult.Status.INVALID_ACTION);
				}
				Intent i = this.ctx.getIntent();
				return new PluginResult(PluginResult.Status.OK, i.getDataString());
			}
			return new PluginResult(PluginResult.Status.INVALID_ACTION);
		} catch (JSONException e) {
			e.printStackTrace();
			return new PluginResult(PluginResult.Status.JSON_EXCEPTION);
		}
	}
	
	void startActivity(String action, Uri uri, String type, Map<String, String> extras) {
		Intent i = (uri != null ? new Intent(action, uri) : new Intent(action));
		if (type != null) {
			i.setType(type);
		}
		for (String key : extras.keySet()) {
			String value = extras.get(key);
			i.putExtra(key, value);
		}
		this.ctx.startActivity(i);
	}
}
