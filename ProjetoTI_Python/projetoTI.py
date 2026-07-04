import requests
import time
from gpiozero import LED
import RPi.GPIO as GPIO
import asyncio
from bleak import BleakScanner
import cv2
from time import localtime, strftime, sleep

# LEDs
ledvermelho = LED(2)
ledverde = LED(3)
ledamarelo = LED(14)

# Sensor de chama
channel = 4

GPIO.setmode(GPIO.BCM)
GPIO.setup(channel, GPIO.IN)

#-------------------------URL da api
URL = "https://iot.dei.estg.ipleiria.pt/ti/ti063/ti/api/api.php"

#-------------------------URL da webcam
webcam_url = "http://10.20.228.119:4747/video" #na droidcam: "http://"+"wifi IP (labs)"+":"+"porto droidcam"+"/video"
server_url="https://iot.dei.estg.ipleiria.pt/ti/ti063/ti/api/upload.php"

#---------------------------------------funcao da capura camara-------------------------------------------
def captura():
    try:
        
        cap = cv2.VideoCapture(webcam_url)
        ret, frame = cap.read()
        if ret:
          # escreve na mesma diretoria do script python o ficheiro webcam.jpg
          cv2.imwrite('webcam.jpg', frame)
          print("Imagem capturada: webcam.jpg") #debug
          
          data=strftime("%Y-%m-%d %H:%M:%S ", localtime())
          #files = {'imagem': open('webcam.jpg', 'rb'),'hora': data} #tem de ser 'hora' para o post do upload.php reconhecer
          files = {'imagem': open('webcam.jpg', 'rb')}
          hora = {'hora': data}
          
          r = requests.post(server_url, files=files, data=hora, timeout=10) #para nao ficar infinitamente em post
          if r.status_code == 200:
              #print(r.text) #debug
              print("sucesso de post!")
          else:
              print("erro de post")
              print(r.status_code)
          sleep(1)
        else:
          print("Erro: nao foi possivel capturar imagem.") 
        cap.release()
    
    except requests.exceptions.RequestException as e:
        print(f"\nERRO DE REQUISIÇÃO GET: {e}")
        return -1
    except Exception as e:
        print(f"\nERRO INESPERADO GET: {e}")
        return -1
#-------------------------fim funcao camara----------------------------------

#---------------------------LOOP PRINCIPAL----------------------------------
try:
    while True:

        #---------------------Ler estados da API
        temp = requests.get(URL + "?nome=led")
        ventoinha = requests.get(URL + "?nome=ventoinha")
        alarme = requests.get(URL + "?nome=alarme")
        pir = requests.get(URL + "?nome=pir")

        #---------------------Verifica se o GET foi bem sucedido
        if temp.status_code == 200:
            
            #----------Muda o estado do LED vermelho [Temperatura] (Ligação Arduino -> Raspbery e/ou Ligação DashBoard -> Raspberry)
            if temp.text.strip() == "1" or temp.text.strip() == "1.00":
                ledvermelho.on()
            else:
                ledvermelho.off()

            print("Arcondicionado:", temp.text)

            #----------Muda o estado do LED Amarelo [Alarme] (Ligação Dashboard -> Raspbery)
            if alarme.text.strip() == "1" or alarme.text.strip() == "1.00":
                ledamarelo.on()
            else:
                ledamarelo.off()

            # Data/Hora atual
            hora = time.strftime("%Y-%m-%d %H:%M:%S")
            print(hora)

            #----------Muda o estado do LED verde [Humidade] (Ligação Arduino -> Raspbery e/ou Ligação DashBoard -> Raspberry)
            if ventoinha.text.strip() == "1" or ventoinha.text.strip() == "1.00":
                ledverde.on()
                print("Vent: on")
            else:
                ledverde.off()
                print("Vent: off")

            #--------------Ler sensor de chama
            valor = GPIO.input(channel)

            if valor == 1:
                print("Fogo detetado!")
            else:
                print("Sem fogo")

            #--------------Enviar Estado do Flame para a API por POST (Raspeberry -> Arduino e Raspberry -> Dashboard)
            r = requests.post(
                URL,
                data={
                    "valor": valor,
                    "nome": "flame",
                    "hora": hora
                }
            )
            #----------Faz a Captura de Foto de acordo com o estado do PIR (Ligação Arduino -> Raspbery -> Dashboard)
            if pir.text.strip() == "1" or pir.text.strip() == "1.00":
                #tirar foto
                captura()

            #-----------DEBUG
            print("POST:", r.status_code)
            print("Resposta:", r.text)
            print("Valor flame:", valor)

        else:
            print("Erro GET:", temp.status_code)

        time.sleep(1)

except KeyboardInterrupt:
    print("Programa terminado.")

finally:
    GPIO.cleanup()