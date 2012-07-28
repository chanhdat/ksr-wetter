#include <Ethernet.h>
//Bibliothek für Ethernet Shield

#include <SPI.h>
//Nötige Bibliothek für das Erstellen einer Datenverbindung zwischen Ethernet Shield und Arduino

//Für die Umwandlung von Text in float-Variable braucht man hier ein Array
char cArray[10]; 
int ic=0;

double t = 0;//Messwerte von BMP085
double t2 = 0;//Messwerte von DHT22 
double qfe = 0;
double qff = 0;
double f = 0;

//Variablen für QFF-Brechnung
double x;
double konst1 = 2731.5;
double h = 480; //Wetterstationshöhe über Meer
double konst2 = h/30.8;

//Zusätzliche Variablen (für Taupunkt berechnen)
double a, b;

//Server-Infomation eingeben
char serverName[] = "www.chanhdat.us";
byte mac[] = {0x90, 0xA2, 0xDA, 0x00, 0x9D, 0x4E}; //MAC-Adresse von Ethernet Shield
EthernetClient client; //Client tragt Daten an Server ein, normalerweise durch Port 80

void setup(){
  Serial.begin(9600);  
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Fehler beim konfigurieren Ethernet mit DHCP");
    // Es bringt nichts, weiter zu gehen, wenn Arduino nicht mit Internet verbinden kann.
    for(;;)
      ;
  }
  delay(1000);  // warten, bis LAN gestartet ist.
}

void loop(){
  while (dataLesen() != 1) {
  delay(100);
  dataLesen();
  }
  
  Serial.print("T1: ");
  Serial.println(t);
  Serial.print("T2: ");
  Serial.println(t2);
  Serial.print("Druck: ");
  Serial.println(qfe);
  Serial.print("Feuchte: ");
  Serial.println(f);
   
  //QFF-Berechnung
  x = ((t + konst1)/(qfe/100)*exp(((t+konst1+konst2)*11.5526-26821)/(t+konst1+konst2-1060)));
  qff = (qfe/100)*exp(konst2*10.5152/(x+t+konst1+konst2));
     
//Taupunkt berechnen
  if (t >= 0) {
    a = 7.5;
    b = 237.3;
  } else {
    a = 7.6;
    b = 240.7;
  }
  
  double SDD = 6.1078 * pow(10,((a*t)/(b+t))); // Sättigungsdampfdruck in hPa
  double DD = f/100 * SDD; //Dampfdruck in hPa
  double v = log(DD/6.1078) / log(10);
  double ta = b * v /(a - v); //Taupunkttemperatur in °C
  
  //Messwerten abschicken
  if (client.connect(serverName, 80)) {
    Serial.println("Verbunden ... sende ... fertig!");
    // URL anrufen:
    client.print("GET /upload.php?TEMP=");
    client.print(t);
    client.print("&TEMP2=");
    client.print(t2);
    client.print("&TAU=");
    client.print(ta);
    client.print("&QFE=");
    client.print(qfe/100, 1);
    client.print("&QFF=");
    client.print(qff,1);
    client.print("&FEUCHTE=");
    client.print(f);
    client.println("&key=root HTTP/1.0\n");
    client.println("Host: localhost");
    client.println("User-Agent: Arduino");
    client.println();
    char c = client.read();
    Serial.print(c);
  } else if (!client.connected()) {
    Serial.println();
    Serial.println("Verbindung trennen...");
    client.stop();
  } else {
    Serial.print("RAWWWWWR!");
  }
  delay(897500);
  softReset();  
}


int dataLesen() {
  int done = 0;
  
  int incomingByte = 0;
  
  while (Serial.available() > 0) {
    //Lesen
    incomingByte = Serial.read();
    
    if (incomingByte == 'T') t = atof(cArray);
    if (incomingByte == 'C') t2 = atof(cArray);
    if (incomingByte == 'D') qfe = atof(cArray);
    if (incomingByte == 'F') {f = atof(cArray); done = 1;}
    
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