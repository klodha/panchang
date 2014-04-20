<?php

/*
 * Copyright (C) 2012 Dinesh Copoosamy <dinesh@dinesh.co.za>
 * Ported from C https://github.com/santhoshn/panchanga
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('D2R',pi()/180.0);
define('R2D',180.0/pi());

class Panchang {

  public $Ls;
  public $Lm;
  public $Ms;
  public $Mm;
 
  public $month = array("January","February","March","April","May","June","July","August","September","October","November","December");
  public $rashi = array("Mesha","Vrishabha","Mithuna","Karka","Simha","Kanya","Tula","Vrischika","Dhanu","Makara","Kumbha","Meena");
  public $day = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
  public $tithi = array("Prathame","Dwithiya","Thrithiya","Chathurthi","Panchami","Shrashti","Saptami","Ashtami","Navami","Dashami","Ekadashi","Dwadashi","Thrayodashi","Chaturdashi","Poornima","Prathame","Dwithiya","Thrithiya","Chathurthi","Panchami","Shrashti","Saptami","Ashtami","Navami","Dashami","Ekadashi","Dwadashi","Thrayodashi","Chaturdashi","Amavasya");
  public $karan = array("Bava","Balava","Kaulava","Taitula","Garija","Vanija","Visti","Sakuni","Chatuspada","Naga","Kimstughna");
  public $yoga = array("Vishkambha","Prithi","Ayushman","Saubhagya","Shobhana","Atiganda","Sukarman","Dhrithi","Shoola","Ganda","Vridhi","Dhruva","Vyaghata","Harshana","Vajra","Siddhi","Vyatipata","Variyan","Parigha","Shiva","Siddha","Sadhya","Shubha","Shukla","Bramha","Indra","Vaidhruthi");
  public $nakshatra = array("Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardhra","Punarvasu","Pushya","Ashlesa","Magha","Poorva Phalguni","Uttara Phalguni","Hasta","Chitra","Swathi","Vishaka","Anuradha","Jyeshta","Mula","Poorva Ashada","Uttara Ashada","Sravana","Dhanishta","Shatabisha","Poorva Bhadra","Uttara Bhadra","Revathi");

  public function REV($x) {
    return (($x)-floor(($x)/360.0)*360.0);
  }

  public function ayanamsa($d) {
    $t = ($d+36523.5)/36525;
    $o = 259.183275-1934.142008333206*$t+0.0020777778*$t*$t;
    $l = 279.696678+36000.76892*$t+0.0003025*$t*$t;
    $ayan = 17.23*sin(($o)*D2R)+1.27*sin(($l*2)*D2R)-(5025.64+1.11*$t)*$t;
    $ayan = ($ayan-80861.27)/3600.0;
    return $ayan;    
  }

  public function sunLongitude($d) {
    $w = 282.9404+4.70935e-5*$d;
    $a = 1.000000;
    $e = 0.016709-1.151e-9*$d;
    $M = $this->REV(356.0470+0.9856002585*$d);
    $this->Ms = $M;
    $this->Ls = $w+$M;

    $tmp = $M*D2R;
    $E = $M+R2D*$e*sin($tmp)*(1+$e*cos($tmp));

    $tmp = $E*D2R;
    $x = cos($tmp)-$e;
    $y = sin($tmp)*sqrt(1-$e*$e);

    $r = sqrt($x*$x + $y*$y);
    $v = $this->REV(R2D*atan2($y,$x));

    return $this->REV($v+$w);    
  }

  public function moonLongitude($d) {

    $N = 125.1228-0.0529538083*$d;
    $i = 5.1454;
    $w = $this->REV(318.0634+0.1643573223*$d);
    $a = 60.2666;
    $e = 0.054900;
    $M = $this->REV(115.3654+13.0649929509*$d);
    $this->Mm = $M;
    $this->Lm = $N+$w+$M;

    //Calculate Eccentricity anamoly
    $tmp = $M*D2R;
    $E = $M+R2D*$e*sin($tmp)*(1+$e*cos($tmp));

    $tmp = $E*D2R;
    $Et = $E-($E-R2D*$e*sin($tmp)-$M)/(1-$e*cos($tmp));

    do {
      $E = $Et;
      $tmp = $E*D2R;
      $Et = $E-($E-R2D*$e*sin($tmp)-$M)/(1-$e*cos($tmp));
    } while($E-$Et>0.005);

    $tmp = $E*D2R;
    $x = $a*(cos($tmp)-$e);
    $y = $a*sqrt(1-$e*$e)*sin($tmp);

    $r = sqrt($x*$x + $y*$y);
    $v = $this->REV(R2D*atan2($y,$x));

    $tmp = D2R*$N;
    $tmp1 = D2R*($v+$w);
    $tmp2 = D2R*$i;
    $xec = $r*(cos($tmp)*cos($tmp1)-sin($tmp)*sin($tmp1)*cos($tmp2));
    $yec = $r*(sin($tmp)*cos($tmp1)+cos($tmp)*sin($tmp1)*cos($tmp2));
    $zec = $r*sin($tmp1)*sin($tmp2);

    //Do some corrections
    $D = $this->Lm - $this->Ls;
    $F = $this->Lm - $N;

    $lon = R2D*atan2($yec,$xec);

    $lon += -1.274*sin(($this->Mm-2*$D)*D2R);
    $lon+= +0.658*sin((2*$D)*D2R);
    $lon+= -0.186*sin(($this->Ms)*D2R);
    $lon+= -0.059*sin((2*$this->Mm-2*$D)*D2R);
    $lon+= -0.057*sin(($this->Mm-2*$D+$this->Ms)*D2R);
    $lon+= +0.053*sin(($this->Mm+2*$D)*D2R);
    $lon+= +0.046*sin((2*$D-$this->Ms)*D2R);
    $lon+= +0.041*sin(($this->Mm-$this->Ms)*D2R);
    $lon+= -0.035*sin(($D)*D2R);
    $lon+= -0.031*sin(($this->Mm+$this->Ms)*D2R);
    $lon+= -0.015*sin((2*$F-2*$D)*D2R);
    $lon+= +0.011*sin(($this->Mm-4*$D)*D2R);

    return $this->REV($lon);
  }

  //Calculate Panchanga
  public function calcPanchanga($dd, $mm, $yy, $hr, $zhr)
  {
    $pdata = array();

    //Calculate day number since 2000 Jan 0.0 TDT
    $d = (367*$yy-7*($yy+($mm+9)/12)/4+275*$mm/9+$dd-730530);

    //Calculate Ayanamsa, moon and sun longitude
    $ayanamsa = $this->ayanamsa($d);
    $slon = $this->sunLongitude($d+(($hr-$zhr)/24.0));
    $mlon = $this->moonLongitude($d+(($hr-$zhr)/24.0));

    //Calculate Tithi and Paksha
    $tmlon = $mlon+(($mlon<$slon)?360:0);
    $tslon = $slon;
    $n = (int)(($tmlon-$tslon)/12);
    $pdata['tithi'] = $this->tithi[$n];
    if($n<=14){
      $pdata['aksha'] = "Shukla"; 
    }
    else {
      $pdata['aksha'] = "Krishna";
    }
    //Calculate Nakshatra
    $tmlon = $this->REV($mlon+$ayanamsa);
    $pdata['nakshatra'] = $this->nakshatra[(int)($tmlon*6/80)];

    //Calculate Yoga
    $tmlon = $mlon+$ayanamsa;
    $tslon = $slon+$ayanamsa;
    $pdata['yoga'] = $this->yoga[(int)($this->REV($tmlon+$tslon)*6/80)];

    //Calculate Karana
    $tmlon = $mlon+(($mlon<$slon)?360:0);
    $tslon = $slon;
    $n = (int)(($tmlon-$tslon)/6);
    if($n==0) $n=10;
    if($n>=57) $n-=50;
    if($n>0 && $n<57) $n=($n-1)-(($n-1)/7*7);
    $pdata['karana'] = $this->karan[$n];

    //Calculate the rashi in which the moon is present
    $tmlon = $this->REV($mlon+$ayanamsa);
    $pdata['rashi'] = $this->rashi[(int)($tmlon/30)];
    return $pdata;
  }    
}
?>
