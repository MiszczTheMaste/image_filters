<?php

require_once "Entity/Image.php";
require_once "Entity/GaussMask.php";
require_once "Entity/SharpnessMask.php";
require_once "Entity/RobertCross.php";

require_once "ValueObject/AbstractValueObject.php";
require_once "ValueObject/RGBValueObject.php";

require_once "Filter/AbstractFilter.php";
require_once "Filter/BrightnessFilter.php";
require_once "Filter/GrayScaleFilter.php";
require_once "Filter/ContrastFilter.php";
require_once "Filter/NegativeFilter.php";
require_once "Filter/BinarizationFilter.php";
require_once "Filter/AvarageFilter.php";
require_once "Filter/GaussFilter.php";
require_once "Filter/SharpnessFilter.php";
require_once "Filter/RobertFilter.php";

require_once "Histogram/HistogramGenerator.php";