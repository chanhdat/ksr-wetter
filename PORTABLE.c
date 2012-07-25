#include <LiquidCrystal.h>
LiquidCrystal lcd(6, 8, 9, 10, 11, 12);
//nötige Bibliothek, um LCD zu steuern

#include <DHT22.h> 
//Steuerung-Library für DHT22: Temp./Feuchtigkeit

#include <stdio.h> 
// enthält diverse Standard-Input-Output-Funktionen 

#include <Wire.h>
//Steuerung-Library für BMP085: Luftdruck/Temp.sensor

#define DHT22_PIN 7 //Data-Pin von DHT22 verbindet mit Pin 7 von MIC
DHT22 myDHT22(DHT22_PIN);

#define BMP085_ADDRESS 0x77 //I2C Adresse von BMP085
const unsigned char OSS = 0;  // Oversampling Setting; 0: ultra low power, 1: standard, 2: high resolution, 3: ultra high resolution
// Lies mehr in Datasheet. Seite 12 und Information von jeder Einstellung in Seite 6

//Kalibrierungswerte für BMP085
int ac1, ac2, ac3;
unsigned int ac4, ac5, ac6;
int b1, b2, mb, mc, md;

long b5; 
//wird in bmp085GetTemperature und auch in bmp085GetPressure berechnet
//Temp() muss vor Druck() kommen

void setup(){
  lcd.begin(16, 2);
  lcd.print("Meteo-KSR");    
  Serial.begin(9600);
  Wire.begin(); //Luftdrucksensor aktivieren
  bmp085Calibration(); //Kalibrierung 
  delay(5000);
}

void loop(){
  
//Temperatur und Luftdruck messen (durch Sensor BMP085)
  float temperatur = bmp085GetTemperature(bmp085ReadUT());
  float druck = bmp085GetPressure(bmp085ReadUP());
  float altitude = calcAltitude(druck); 

//Feuchtigkeit messen (durch Sensor DHT22)
  myDHT22.readData();
  float temp = myDHT22.getTemperatureC();
  float feuchte = myDHT22.getHumidity();
  
  lcd.setCursor(10, 0); //Spalte 10, erste Reihe
  lcd.print(altitude, 0); //Höhe über Meer
  lcd.setCursor(14,0);
  lcd.print("m");
  
  lcd.setCursor(0, 1); //erste Spalte, zweite Reihe
  lcd.print(temperatur);

  lcd.setCursor(6, 1); 
  lcd.print(druck/100, 1);

  lcd.setCursor(12, 1);
  lcd.print(feuchte);
  delay(10000);
  
}

  void bmp085Calibration() //Datasheet lesen (Seite 12)
{
  ac1 = bmp085ReadInt(0xAA);
  ac2 = bmp085ReadInt(0xAC);
  ac3 = bmp085ReadInt(0xAE);
  ac4 = bmp085ReadInt(0xB0);
  ac5 = bmp085ReadInt(0xB2);
  ac6 = bmp085ReadInt(0xB4);
  b1 = bmp085ReadInt(0xB6);
  b2 = bmp085ReadInt(0xB8);
  mb = bmp085ReadInt(0xBA);
  mc = bmp085ReadInt(0xBC);
  md = bmp085ReadInt(0xBE);
}

//Temperatursberechnung
float bmp085GetTemperature(unsigned int ut){
  long x1, x2;

  x1 = (((long)ut - (long)ac6)*(long)ac5) >> 15;
  x2 = ((long)mc << 11)/(x1 + md);
  b5 = x1 + x2;

  float temp = ((b5 + 8)>>4);
  temp = temp /10;

  return temp;
}

//Luftdruckberechnung
long bmp085GetPressure(unsigned long up) {
  long x1, x2, x3, b3, b6, p;
  unsigned long b4, b7;

  b6 = b5 - 4000;
  // Berechne B3
  x1 = (b2 * (b6 * b6)>>12)>>11;
  x2 = (ac2 * b6)>>11;
  x3 = x1 + x2;
  b3 = (((((long)ac1)*4 + x3)<<OSS) + 2)>>2;

  // Berechne B4
  x1 = (ac3 * b6)>>13;
  x2 = (b1 * ((b6 * b6)>>12))>>16;
  x3 = ((x1 + x2) + 2)>>2;
  b4 = (ac4 * (unsigned long)(x3 + 32768))>>15;

  b7 = ((unsigned long)(up - b3) * (50000>>OSS));
  if (b7 < 0x80000000)
    p = (b7<<1)/b4;
  else
    p = (b7/b4)<<1;

  x1 = (p>>8) * (p>>8);
  x1 = (x1 * 3038)>>16;
  x2 = (-7357 * p)>>16;
  p += (x1 + x2 + 3791)>>4;

  long temp = p;
  return temp;
}

// 1 byte bei "address" ablesen
char bmp085Read(unsigned char address) {
  unsigned char data;

  Wire.beginTransmission(BMP085_ADDRESS);
  Wire.write(address);
  Wire.endTransmission();

  Wire.requestFrom(BMP085_ADDRESS, 1);
  while(!Wire.available())
    ;

  return Wire.read();
}

int bmp085ReadInt(unsigned char address) {
  unsigned char msb, lsb;

  Wire.beginTransmission(BMP085_ADDRESS);
  Wire.write(address);
  Wire.endTransmission();

  Wire.requestFrom(BMP085_ADDRESS, 2);
  while(Wire.available()<2)
    ;
  msb = Wire.read();
  lsb = Wire.read();

  return (int) msb<<8 | lsb;
}

// Unkompesierte Temperaturswerte ablesen
unsigned int bmp085ReadUT() {
  unsigned int ut;

  // 0x2E an Reg. 0xF4 schreiben
  // Sensor wird die Temperatur messen
  Wire.beginTransmission(BMP085_ADDRESS);
  Wire.write((byte)0xF4);
  Wire.write((byte)0x2E);
  Wire.endTransmission();

  // mind. 4.5ms warten
  delay(5);

  // 2 Bytes von Reg. 0xF6 and 0xF7 ablesen
  ut = bmp085ReadInt((byte)0xF6);
  return ut;
}

// Unkompesierte Luftdruckwerte ablesen
unsigned long bmp085ReadUP() {

  unsigned char msb, lsb, xlsb;
  unsigned long up = 0;

  // 0x34+(OSS<<6) in Reg. 0xF4 eintragen
  Wire.beginTransmission(BMP085_ADDRESS);
  Wire.write((byte)0xF4);
  Wire.write((byte)0x34 + (OSS<<6));
  Wire.endTransmission();

  // Warten auf die Umrechnung, es hängt von der Genauigkeit-Einstellung (OSS) ab.
  delay(2 + (3<<OSS));

  // Reg. 0xF6 (MSB), 0xF7 (LSB), und 0xF8 (XLSB) ablesen
  msb = bmp085Read(0xF6);
  lsb = bmp085Read(0xF7);
  xlsb = bmp085Read(0xF8);

  up = (((unsigned long) msb << 16) | ((unsigned long) lsb << 8) | (unsigned long) xlsb) >> (8-OSS);

  return up;
}

void writeRegister(int deviceAddress, byte address, byte val) {
  Wire.beginTransmission(deviceAddress); // Die Übertragung zum Mikrokontroller anfangen 
  Wire.write(address);       // Die Adresse von Reg. anrufen
  Wire.write(val);         // Werte dorthin schreiben
  Wire.endTransmission();     // Übertragungsende
}

int readRegister(int deviceAddress, byte address) {

  int v;
  Wire.beginTransmission(deviceAddress);
  Wire.write(address); 
  Wire.endTransmission();

  Wire.requestFrom(deviceAddress, 1); // 1 Byte davon ablesen

  while(!Wire.available()) {
    // warten
  }

  v = Wire.read();
  return v;
}

float calcAltitude(float pressure){

  float A = pressure/101325;
  float B = 1/5.25588;
  float C = pow(A,B);
  C = 1 - C;
  C = C /0.0000225577;

  return C;
}