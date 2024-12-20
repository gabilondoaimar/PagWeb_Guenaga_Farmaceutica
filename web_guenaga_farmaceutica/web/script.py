import mysql.connector
from mysql.connector import Error
import smtplib
from email.message import EmailMessage
import os

def get_users_without_images():
    try:
        connection = mysql.connector.connect(
            host="localhost",
            user="root", 
            password="",  
            database="bdweb"  
        )

        if connection.is_connected():
            cursor = connection.cursor(dictionary=True)
            query = "SELECT email, izena FROM users WHERE irudia IS NULL"
            cursor.execute(query)
            users = cursor.fetchall()
            return users

    except Error as e:
        print(f"Error al conectar con la base de datos: {e}")
        return []
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()

def send_email(to_email, name):
    try:
        #cambiar o poner el gmail...
        smtp_server = ""
        smtp_port = 587
        from_email = os.getenv("")  
        password = os.getenv("") 

        if not from_email or not password:
            print("Faltan las credenciales de Gmail.")
            return

        msg = EmailMessage()
        msg["Subject"] = "Irudia igotzea beharrezkoa"
        msg["From"] = from_email
        msg["To"] = to_email
        msg.set_content(f"Kaixo {name},\n\nMesedez, igo zure profilaren irudia gure plataforman.\n\nEskerrik asko!")

        with smtplib.SMTP(smtp_server, smtp_port) as server:
            server.starttls() 
            server.login(from_email, password)
            server.send_message(msg)
            print(f"Correo enviado a: {to_email}")

    except Exception as e:
        print(f"Error al enviar el correo: {e}")

def main():
    users_without_images = get_users_without_images()
    if not users_without_images:
        print("No hay usuarios sin imagen.")
        return

    for user in users_without_images:
        send_email(user["email"], user["izena"])

if __name__ == "__main__":
    main()
