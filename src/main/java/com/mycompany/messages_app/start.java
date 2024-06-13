/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.messages_app;

import java.sql.Connection;

/**
 *
 * @author nicolassanchez--desarrolladorestandar
 */
public class start {
    
    public static void main (String[] args){
       Config config = new Config();

       try(Connection cnx = config.get_connection()) {
           
           System.out.println("CNX" + cnx);
           
       }catch(Exception e){
           System.out.println(e);
       }
       
    }
    
}
