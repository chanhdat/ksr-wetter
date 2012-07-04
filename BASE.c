#include <Ethernet.h>
//Bibliothek für Ethernet Shield

#include <SPI.h>
//Nötige Bibliothek für das Erstellen einer Datenverbindung zwischen Ethernet Shield und Arduino

//Für die Umwandlung von Text in float-Variable braucht man hier ein Array
char cArray[10]; 
int ic=0;

double temperatur = 0;
double druck = 0;
double feuchte = 0;

//Server-Infomation eingeben
char serverName[] = "www.chanhdat.us";
byte mac[] = {0x90, 0xA2, 0xDA, 0x00, 0x9D, 0x4E}; //MAC-Adresse von Ethernet Shield
EthernetClient client; //Client tragt Daten an Server ein, normalerweise durch Port 80

void setup(){
  Serial.begin(9600);
  delay(1000);  // warten, bis LAN gestartet
  
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Fehler beim konfigurieren Ethernet mit DHCP");
    // Es bringt nichts, weiter zu gehen, wenn Arduino nicht mit Internet verbinden kann.
    for(;;)
      ;
  }

}

void loop(){
  if (dataLesen()==1) {
  if (client.connect(serverName, 80)) {
    //Serial.println("Verbunden ... sende ... fertig!");
    // URL anrufen:
    client.print("GET /upload.php?TEMP=");
    client.print(temperatur);
    client.print("&DRUCK=");
    client.print(druck/100, 1);
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
}    
  delay(1799000);
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
    
    if (incomingByte == 'T') temperatur = atof(cArray);
    if (incomingByte == 'P') taupunkt = atof(cArray);
    if (incomingByte == 'D') druck = atof(cArray);
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