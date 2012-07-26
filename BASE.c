#include <Ethernet.h>
//Bibliothek für Ethernet Shield

#include <SPI.h>
//Nötige Bibliothek für das Erstellen einer Datenverbindung zwischen Ethernet Shield und Arduino

#include <math.h>
//Mathematik-Bibliothek

//Für die Umwandlung von Text in float-Variable braucht man hier ein Array
char cArray[10]; 
int ic=0;

double temp = 0;//Messwerte von BMP085
double temp2 = 0;//Messwerte von DHT22 
double qfe = 0;
double qff = 0;
double feuchte = 0;

//Variablen für QFF-Brechnung
double x;
double konst1 = 2731.5;
double h = 480; //Wetterstationshöhe über Meer
double konst2 = h/30.8;

//Zusätzliche Variablen (für Taupunkt berechnen)
double a, b;

//LED-Signal (Debug)
int led = 13;

//Server-Infomation eingeben
char serverName[] = "www.chanhdat.us";
byte mac[] = {0x90, 0xA2, 0xDA, 0x00, 0x9D, 0x4E}; //MAC-Adresse von Ethernet Shield
EthernetClient client; //Client tragt Daten an Server ein, normalerweise durch Port 80

void setup(){
  Serial.begin(9600);
  pinMode(led, OUTPUT);
  delay(1000);  // warten, bis LAN gestartet
  
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Fehler beim konfigurieren Ethernet mit DHCP");
    // Es bringt nichts, weiter zu gehen, wenn Arduino nicht mit Internet verbinden kann.
    digitalWrite(led, HIGH);
    for(;;)
      ;
  }
  if (dataLesen()==0) {
    digitalWrite(led, HIGH);
    delay(1000);
    digitalWrite(led, LOW);
    delay(1000);
    softReset();
    }
}

void loop(){
  if (dataLesen()==1) {
  //QFF-Berechnung
  x = ((temp + konst1)/(qfe/100)*exp(((temp+konst1+konst2)*11.5526-26821)/(temp+konst1+konst2-1060)));
  qff = (qfe/100)*exp(konst2*10.5152/(x+temp+konst1+konst2));
  
    
//Taupunkt berechnen
  if (temp >= 0) {
    a = 7.5;
    b = 237.3;
  } else {
    a = 7.6;
    b = 240.7;
  }
  
  double SDD = 6.1078 * pow(10,((a*temp)/(b+temp))); // Sättigungsdampfdruck in hPa
  double DD = feuchte/100 * SDD; //Dampfdruck in hPa
  double v = log(DD/6.1078) / log(10);
  double taupunkt = b * v /(a - v); //Taupunkttemperatur in °C
  
  //Messwerten abschicken
  if (client.connect(serverName, 80)) {
    //Serial.println("Verbunden ... sende ... fertig!");
    // URL anrufen:
    client.print("GET /upload.php?TEMP=");
    client.print(temp);
    client.print("&TEMP2=");
    client.print(temp2);
    client.print("&TAU=");
    client.print(taupunkt);
    client.print("&QFE=");
    client.print(qfe/100, 1);
    client.print("&QFF=");
    client.print(qff,1);
    client.print("&FEUCHTE=");
    client.print(feuchte);
    client.println("&key=root HTTP/1.0\n");
    client.println("Host: localhost");
    client.println("User-Agent: Arduino");
    client.println();
    client.stop();
  } 
  if (!client.connected()) {
    /*Serial.println();
    Serial.println("disconnecting.");*/
    client.stop();
} digitalWrite(led, HIGH);
  delay(1000);
  digitalWrite(led, LOW);
  delay(1000);
  digitalWrite(led, HIGH);
  delay(1000);
  digitalWrite(led, LOW);
  delay(1000);  
  delay(1795000);
  softReset();
/*  else {
    // 2. Worst-Case-Szenario
    Serial.println("connection failed");
  }*/
}
}

int dataLesen() {
  int done = 0;
  
  int incomingByte = 0;
  
  while (Serial.available() > 0) {
    //Lesen
    incomingByte = Serial.read();
    
    if (incomingByte == 'T') temp = atof(cArray);
    if (incomingByte == 'C') temp2 = atof(cArray);
    if (incomingByte == 'D') qfe = atof(cArray);
    if (incomingByte == 'F') {feuchte = atof(cArray); done = 1;}
    
      //If the recieved byte is a digit (specified by ASCII values 65 to 90)
      if (incomingByte>64 && incomingByte<91) 
      {
         ic=0;
         for(int i=0; i<10; i++) cArray[i] = 0;
      }

      //If the recieved byte is a digit (specified by ASCII values 65 to 90)
      //or a decimal point ASCII 46
      if ((incomingByte>47 && incomingByte<58) || incomingByte==46)   
      {
         //add to character array
         cArray[ic] = incomingByte; ic++;
      }
   }
   
   //Return status
   return done;
}

void softReset() {
  asm volatile ("  jmp 0");
}