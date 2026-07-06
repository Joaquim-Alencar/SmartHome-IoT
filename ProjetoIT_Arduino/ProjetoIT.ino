#include <DHT.h>
#include <WiFi101.h> //Biblioteca Wifi para o MKR1000
//#include <WiFiNINA.h> //Biblioteca wifi para MKR 1010
#include <ArduinoHttpClient.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <time.h>
#include <SPI.h>
#include <Servo.h>

//-----------------------------------------Pinos dos Sensores e Atuadores-------------------------------------------------
const int pinoPIR = 6;   //Pino do Pir
#define DHTPIN 1        //Pino do Sensor de Humidade e Temperatura
#define LED_BUILTIN 5   //DEBUG
#define PIN_BUTTON 4    //Pino do Butao
#define BUZZER_BUILTIN 3  //Pino do Buzzer
Servo servo_9;          //Pino do servomotor

//----------------------------------------Configuração-----------------------------------------------
#define DHTTYPE DHT11   //Tipo do Sensor de Temperatura e Humidade
int pos = 0;          //Posição inicial do ServoMotor

DHT dht(DHTPIN, DHTTYPE); //Objeto

//-------------------------------------Objeto Wifi-----------------------------------------------------------------------------
WiFiUDP clienteUDP;

//---------------------------------------------Conexão NTP-------------------------------------------------------------------
char NTP_SERVER[] = "0.pool.ntp.org";
NTPClient clienteNTP(clienteUDP, NTP_SERVER, 3600);

//----------------------------------------Servidor
char HOST[] = "iot.dei.estg.ipleiria.pt";
int PORTO = 80;

//-----------------------------------------WIFI
char SSID[] = "labs";
char PASS_WIFI[] = "1nv3nt@r2023_IPLEIRIA";

int status = WL_IDLE_STATUS;
WiFiClient clienteWifi;
HttpClient clienteHTTP = HttpClient(clienteWifi, HOST, PORTO);

//----------------------------------------------Setup----------------------------------------------------------------------
void setup()
{
  servo_9.attach(9, 500, 2500); //Configuração servo motor

  pinMode(LED_BUILTIN, OUTPUT); //Configuração 
  pinMode(BUZZER_BUILTIN, OUTPUT);//Configuração Buzzer
  pinMode(pinoPIR, INPUT);  //Configuração  PIR
  pinMode(PIN_BUTTON, INPUT_PULLUP);  //Configuração Butao 

  Serial.begin(9600);
  while (!Serial);

//----------------------------------------Conexão WIFI
  Serial.print("Connecting ");
  Serial.println(SSID);

  while (status != WL_CONNECTED) {
    status = WiFi.begin(SSID, PASS_WIFI);
    Serial.print(".");
    delay(250);
  }

  Serial.println("\nWiFi Connected");

  dht.begin();    //""Configuração""" do sensor Humidade e Temperatura
  clienteNTP.begin();

  digitalWrite(LED_BUILTIN, LOW);
  delay(600);
}

//-------------------------------------------------------loop----------------------------------------------------------------
void loop()
{
  clienteNTP.update();

  // Le o estado do PIR
  int estadoPIR = digitalRead(pinoPIR);
 
  //--------------------------------Modo manual - Ler do dashboard
  int manual_mode = getFromAPI("manual").toInt();

  //--------------------------------Porta - Ler do dashboard / raspberry
  int estado_porta = getFromAPI("porta").toInt();
  Serial.println(estado_porta);

  //----------------------------------Ler Sensores Temperatura e Humidade -----------------------------------
  float tempC = dht.readTemperature();
  float humid = dht.readHumidity();

  // ---------------------- NOVA DATA + HORA COMPLETA ----------------------
  time_t epochTime = clienteNTP.getEpochTime();
  struct tm *ptm = gmtime((time_t *)&epochTime);

  int dia = ptm->tm_mday;
  int mes = ptm->tm_mon + 1;
  int ano = ptm->tm_year + 1900;

  int hora = ptm->tm_hour;
  int minuto = ptm->tm_min;
  int segundo = ptm->tm_sec;

  String datahora = String(ano) + "-" +
                    String(mes) + "-" +
                    String(dia) + " " +
                    String(hora) + ":" +
                    String(minuto) + ":" +
                    String(segundo);

  //BOTAO
  int estadoButao = digitalRead(PIN_BUTTON);
  if (estadoButao == LOW)
  {
    Serial.println("butao ligado");
    post2API("porta", 0, datahora);
  }
  

  //------PIR
  static int ultimoPIR = -1;  //Define o Ultimo estado do PIR

  //Resumidamente: So faz o POST do estado do PIR, caso o valor seja diferente do valor anterior
  if (estadoPIR != ultimoPIR) 
  {
    if (estadoPIR == HIGH)
    {
      Serial.println("[PIR] 1 - Movimento");
      post2API("pir", 1, datahora);
    }
    else
    {
      Serial.println("[PIR] 0 - Sem movimento");
      post2API("pir", 0, datahora);
    }
    ultimoPIR = estadoPIR;
  }

  //-----------------------------DEBUG-------------------------
  Serial.println("------ SENSORES ------");

  Serial.print("Temperatura: ");
  Serial.print(tempC);
  Serial.println(" °C");

  Serial.print("Humidade: ");
  Serial.print(humid);
  Serial.println(" %");

  Serial.print("Data/Hora: ");
  Serial.println(datahora);

  //-------------------------------------Envio da Temperatura e Humidade Post---------
  if(!isnan(tempC)) //Verifica se o valor existe primeiro
  {
    post2API("temperatura", tempC, datahora);
  }
  if(!isnan(humid))
  {
    post2API("humidade", humid, datahora);
  }
  

  // ---------------- CONTROLO DE SENSORES
  bool arCondicionado = false;
  bool ventoinhas = false;
  bool alarme = false;
  bool porta = false;

//---------Flame (Lê da Dashboard)
  int flameEstado = getFromAPI("flame").toInt();

  // -------- AR CONDICIONADO --------
  if(manual_mode==0)  //Se o modo manual tiver ligado não é possivel alterar o valor do arcondicionado automaticamente
  {
    if (tempC > 30) {   //Se a temperatura ta alta, ativa o AC
      arCondicionado = true;
      post2API("led", 1, datahora);
      
    } else {
      arCondicionado = false;
      post2API("led", 0, datahora);
      
    }
  }

  // -------- VENTOINHAS --------
  if(manual_mode==0)  //Se o modo manual tiver ligado não é possivel alterar o valor da ventilação automaticamente
  {
    if (humid < 55) { //Se a humidade ta baixa, ativa a ventilação
      ventoinhas = true;
      post2API("ventoinha", 1, datahora);
      
    } else {
      ventoinhas = false;
      post2API("ventoinha", 0, datahora);
      
    }
  }

  // -------- ALARME --------

  //Ativa o buzzer se: temperatura for alta, humidade for baixa e o detetor de chamas ativar
  if (tempC > 30 && humid < 100 && flameEstado == 1) {  //A humidade deveria ser <30
    alarme = true;
    post2API("alarme", 1, datahora);
    tone(BUZZER_BUILTIN, 3000);
    
  } else {
    alarme = false;
    post2API("alarme", 0, datahora);
    noTone(BUZZER_BUILTIN);
    
  }

  // -------- DEBUG SISTEMA --------
  Serial.println("------ ESTADO SISTEMA ------");

  Serial.print("Ar Condicionado: ");
  Serial.println(arCondicionado ? "ON" : "OFF");

  Serial.print("Ventoinhas: ");
  Serial.println(ventoinhas ? "ON" : "OFF");

  Serial.print("Alarme: ");
  Serial.println(alarme ? "ON" : "OFF");

  Serial.print("Porta: ");
  Serial.println(estado_porta);

  Serial.println("-----------------------------\n");

  // -------- SERVO --------
  if(estado_porta==0)     //Se a porta estiver fechada, o servo motor vai para posição 0
  {
    servo_9.write(0);
    porta = false;
  }
  else      //Se tiver aberta, servo motor vai para posição 70
  {
    servo_9.write(70);
    porta = true;
  }
}

//---------------------------------------------------FUNÇÕES POST E GET ----------------------------------------------------------

void post2API(String enviaNome, float enviaValor, String enviaHora)
{
  String URLPath = "/ti/ti063/ti/api/api.php";
  String contentType = "application/x-www-form-urlencoded";

  String body = "nome=" + enviaNome +
                "&valor=" + String(enviaValor) +
                "&hora=" + enviaHora;

  clienteHTTP.post(URLPath, contentType, body);

  clienteHTTP.responseStatusCode();
  clienteHTTP.responseBody();
  clienteHTTP.stop();
}

String getFromAPI(String nome)
{
  String URLPath = "/ti/ti063/ti/api/api.php?nome=" + nome;

  clienteHTTP.get(URLPath);

  String responseBody = clienteHTTP.responseBody();
  clienteHTTP.stop();

  responseBody.trim();
  return responseBody;
}