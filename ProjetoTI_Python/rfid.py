import requests
import time
from mfrc522 import SimpleMFRC522   #Biblioteca do RFID
from time import sleep              #Biblioteca do "delay"

reader = SimpleMFRC522()            #Leitura do RFID

#----------------------------URL da API
URL = "https://iot.dei.estg.ipleiria.pt/ti/ti063/ti/api/api.php"

while True:

    print("Aproxime um cartao")

#----------------Faz Leitura do cartão
    uid, text = reader.read()   

    text = text.strip()
    
#----------------DEBUG
    print(uid)
    print(text)
    #sleep(10);
#---------------HORA ATUAL
    hora = time.strftime("%Y-%m-%d %H:%M:%S")

#---------------Verifica quem é que está a brir a porta
    if text == "Sofia" or text == "Joaquim":
        #---------Faz o POST para a API [Abre a Porta] (Ligação raspberry -> Arduino e raspberry ->Dashboard) 
        r = requests.post(
            URL,
            data={
                "nome": "porta",
                "valor": 1,
                "hora": hora
            }
        )

        #DEBUG
        print("Status:", r.status_code)
        print("Resposta:", r.text)