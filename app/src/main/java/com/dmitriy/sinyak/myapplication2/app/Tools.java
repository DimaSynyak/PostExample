package com.dmitriy.sinyak.myapplication2.app;

/**
 * Created by 1 on 19.11.2015.
 */
public class Tools {

    public static String getCutting(String str){
        StringBuilder stringBuilder = new StringBuilder();

        for (int i = 2; i < str.length() - 2; i++) {
            stringBuilder.append(str.charAt(i));
        }
        System.out.println(stringBuilder);
        return stringBuilder.toString();
    }
}
