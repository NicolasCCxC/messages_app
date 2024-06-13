/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.messages_app;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 *
 * @author nicolassanchez--desarrolladorestandar
 */
public class Config {
    
    public Connection get_connection(){
        
        Connection connection = null;
        
        try{
            connection = DriverManager.getConnection("jdbc:mysql://localhost:3306/messages_app", "root", "");
            
            if(connection != null){
                System.out.println("Sucesfull connection");
            }
            
        }catch (SQLException e){
            System.out.println(e);
        }
        
        return connection;
        
    }
    
}
