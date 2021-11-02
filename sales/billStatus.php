<?php

function billStatus($bill)
{	
	if( fnmatch("B*",$bill) || fnmatch("C*",$bill) || fnmatch("GB*",$bill) || fnmatch("GC*",$bill) || fnmatch("PB*",$bill) || fnmatch("PC*",$bill))
		return true;
	else
		return false;
}