import smtplib
import mysql.connector
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart


# Datu-basearen konfigurazioa
DB_CONFIG = {
    'host': '',  # Datu-basearen host-a aldatu beharrezkoa bada
    'user': 'root',  # Datu-basearen erabiltzailea
    'password': 'root',  # Datu-basearen pasahitza
    'database': 'bdweb',  # Datu-basearen izena
    'port': '3308', # Datu-basearen portua
}

# Gmail-en SMTP zerbitzariaren konfigurazioa
SMTP_CONFIG = {
    'server': 'localhost',
    'port': 25,
    'email': 'guenagafarmazeutika@gmail.com',  # Zure posta elektronikoa hemen sartu
    'password': 'xsle lvuq htdt mwmb  ',  # Zure Gmail pasahitza edo aplikazio pasahitza sartu
}




def users_argazkirik_gabe():
    """'irudia' eremua NULL den erabiltzaileak lortzen ditu (argazkirik ez dutenak)."""
    try:
        konexioa = mysql.connector.connect(**DB_CONFIG)
        if konexioa.is_connected():
            print("Konexioa arrakastatsua da.")
        cursor = konexioa.cursor(dictionary=True)
        cursor.execute("SELECT id, username, izena FROM users WHERE (irudia IS NULL OR irudia = '') AND username IS NOT NULL;")
        usuarios = cursor.fetchall()
        print(f"Erabiltzaileak aurkitu dira: {usuarios}")  # Imprime los usuarios encontrados
        konexioa.close()
        return usuarios
    except mysql.connector.Error as err:
        print(f"Errorea datu-basearekin konektatzean: {err}")
        return []

def mezua_bidali(jasotzailea, izena):
    """Mezu elektroniko bat bidaltzen dio hartzaileari, argazki bat eransteko eskatuz."""
    try:
        # Mezua konfiguratu
        mezua = MIMEMultipart()
        mezua['From'] = SMTP_CONFIG['email']
        mezua['To'] = jasotzailea
        mezua['Subject'] = 'Mesedez, gehitu zure argazkia'

        # Postaren edukia
        gorputza = f"""
        Kaixo {izena},

        Zure profilak oraindik ez du lotutako argazkirik. Mesedez, gehitu argazki bat lehenbailehen zure profila osatzeko.

        Laguntza behar baduzu, jar zaitez gurekin harremanetan.

        Agur bero bat.
        Administrazio-taldea
        """
        mezua.attach(MIMEText(gorputza, 'plain'))

        # SMTP zerbitzarira konektatu
        serbitzaria = smtplib.SMTP(SMTP_CONFIG['server'], SMTP_CONFIG['port'])
        serbitzaria.login(SMTP_CONFIG['email'], SMTP_CONFIG['password'])

        # Posta bidali
        serbitzaria.send_message(mezua)
        serbitzaria.quit()
        print(f"Posta honetara bidalita: {jasotzailea}")
    except Exception as e:
        print(f"Errorea posta helbide honetara bidaltzean {jasotzailea}: {e}")

if __name__ == "__main__":
    print("Argazkirik gabeko erabiltzaileen bila...")
    erabiltzaileak = users_argazkirik_gabe()

    if erabiltzaileak:
        for erabiltzailea in erabiltzaileak:
                
            print("SMTP_CONFIG behar bezala kargatuta:", SMTP_CONFIG)
            mezua_bidali(erabiltzailea['username'], erabiltzailea['izena'])
    else:
        print("Une honetan ez da argazkirik gabeko erabiltzailerik aurkitu.")