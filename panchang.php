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

require "plib.php";

//Usage: panchanga -d dd/mm/yyy -t hh:mm -z [+/-]hh:mm
//-d : Date in dd/mm/yyyy format.
//-t : Time in hh:mm 24-hour format.
//-z : Zone with respect to GMT in [+/-]hh:mm format.

$p = new Panchang();
$dd = 9;
$mm = 2;
$yy = 1981;
$hh = 21;
$mn = 00;


$zhh = 2;
$zmn = 0;

$hr = $hh + $mn/60.0;
$zhr = $zhh + $zmn/60.0;

$data = $p->calcPanchanga($dd, $mm, $yy, $hr, $zhr);
print_r($data);

?>
