package com.dmitriy.sinyak.myapplication2.app;

import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;

import org.apache.http.HttpEntity;

import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpGet;

import org.apache.http.client.protocol.HttpClientContext;
import org.apache.http.impl.client.CloseableHttpClient;

import org.apache.http.impl.client.HttpClients;
import org.jsoup.Connection;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;

import org.jsoup.select.Elements;

import java.security.Principal;
import java.util.HashMap;

import java.util.List;
import java.util.Map;
import java.util.jar.Attributes;


public class MainActivity extends AppCompatActivity implements View.OnClickListener {

    private Button load_btn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        load_btn = (Button) findViewById(R.id.load_btn);

    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onClick(View v) {
        switch (v.getId()){
            case R.id.load_btn:{
                new RequestTask().execute("http://menu24.ee/ru/restaurant/mafia/");
//                new RequestTask2().execute("http://menu24.ee/ru/restaurant/mafia/");
                System.out.println("load_btn");
                break;
            }
            default:
                System.out.println("default");
        }
    }


    class RequestTask extends AsyncTask<String, String, String> {


        String url;
        @Override
        protected String doInBackground(String... params) {
            Connection.Response resp = null;
            try {
                Connection conn = Jsoup.connect(params[0]);
                conn.ignoreContentType(true);

                conn.data("wc-ajax", "add_to_cart").data("lang", "ru").data("product_id", "48").method(Connection.Method.GET);
                resp = conn.execute();
                conn.cookies(resp.cookies());

                conn.data("wc-ajax", "add_to_cart").data("lang", "ru").data("product_id", "148").method(Connection.Method.GET);
                resp = conn.execute();
                conn.cookies(resp.cookies());

                Document doc = resp.parse();
                Elements elements = doc.select("input");
                String _wpnonce = null;
                String _wp_http_referer = null;

                boolean flag1 = false;
                boolean flag2 = false;
                for (Element element:elements){
                    if (element.attr("id").contains("wpnonce")){
                        if (element.attr("name").contains("_wpnonce")){
                            if (element.attr("type").contains("hidden")) {
                                _wpnonce = Tools.getCutting(element.attr("value"));
                                flag1 = true;
                            }
                        }
                    }

                    if (element.attr("name").contains("_wp_http_referer")){
                        if (element.attr("type").contains("hidden")){
                            _wp_http_referer = Tools.getCutting(element.attr("value"));
                            flag2 = true;
                        }
                    }

                    if (flag1 && flag2){
                        break;
                    }
                }

                conn.url("http://menu24.ee/checkout/");


                conn.data("billing_delivery", "с доставкой");
                conn.data("billing_first_name", "frighten");
                conn.data("billing_country", "EE");
                conn.data("billing_city", "Таллин");
                conn.data("billing_address_1", "address");
                conn.data("billing_address_2", "111");
                conn.data("billing_address_3", "222");
                conn.data("billing_time", "25.12.2015 09:30");
                conn.data("billing_email", "fff@mail.ru");
                conn.data("billing_phone", "0663200266");
                conn.data("_wpnonce", _wpnonce);
                conn.data("_wp_http_referer", _wp_http_referer);
                conn.data("payment_method", "banklinkmaksekeskus");
                conn.data("PRESELECTED_METHOD_banklinkmaksekeskus", "seb");
                conn.data("woocommerce_checkout_place_order", "Maksma");

                conn.method(Connection.Method.POST);

                resp = conn.execute();
                conn.cookies(resp.cookies());

                Elements forms = null;


                conn.url(conn.response().url());

                conn.data("billing_delivery", "с доставкой");
                conn.data("billing_first_name", "frighten");
                conn.data("billing_country", "EE");
                conn.data("billing_city", "Таллин");
                conn.data("billing_address_1", "address");
                conn.data("billing_address_2", "111");
                conn.data("billing_address_3", "222");
                conn.data("billing_time", "25.12.2015 09:30");
                conn.data("billing_email", "fff@mail.ru");
                conn.data("billing_phone", "0663200266");
                conn.data("_wpnonce", _wpnonce);
                conn.data("_wp_http_referer", _wp_http_referer);
                conn.data("payment_method", "banklinkmaksekeskus");
                conn.data("PRESELECTED_METHOD_banklinkmaksekeskus", "seb"); //change
                conn.data("woocommerce_checkout_place_order", "Maksma");

                conn.method(Connection.Method.POST);

                resp = conn.execute();
                conn.cookies(resp.cookies());
                doc = null;
                doc = resp.parse();

                forms = doc.getElementsByAttribute("action");
                url = forms.get(0).attr("action");

                System.out.println(resp);

            } catch (Exception e) {
                System.out.println(resp);
                e.printStackTrace();
            }

            return null;
        }



        @Override
        protected void onPostExecute(String s) {
            super.onPostExecute(s);
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
            startActivity(intent);
        }
    }


    class RequestTask2 extends AsyncTask<String, String, String> {
        CloseableHttpClient httpclient;
        HttpClientContext context1;
        @Override
        protected void onPreExecute() {
            super.onPreExecute();

        }

        @Override
        protected String doInBackground(String... params) {
            try {

                httpclient = HttpClients.createDefault();
                context1 = HttpClientContext.create();
                HttpGet httpget1 = new HttpGet(params[0]);
                CloseableHttpResponse response1 = httpclient.execute(httpget1, context1);
                try {
                    HttpEntity entity1 = response1.getEntity();
                } finally {
                    response1.close();
                }
                Principal principal = context1.getUserToken(Principal.class);

                HttpClientContext context2 = HttpClientContext.create();
                context2.setUserToken(principal);
                HttpGet httpget2 = new HttpGet(params[0]);
                CloseableHttpResponse response2 = httpclient.execute(httpget2, context2);
                try {
                    HttpEntity entity2 = response2.getEntity();
                } finally {
                    response2.close();
                }

                System.out.println(1);



            } catch (Exception e) {
                e.printStackTrace();
            }

            return null;
        }

    }
}
