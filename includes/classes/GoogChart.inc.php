<?php

  class chart_util
  {
      public static function count_r($mixed)
      {
          $totalCount = 0;

          foreach ($mixed as $temp) {
              if (is_array($temp)) {
                  $totalCount += chart_util::count_r($temp);
              } else {
                  ++$totalCount;
              }
          }

          return $totalCount;
      }

      public static function addArrays($mixed)
      {
          $summedArray = [];

          foreach ($mixed as $temp) {
              $a = 0;
              if (is_array($temp)) {
                  foreach ($temp as $tempSubArray) {
                      $summedArray[$a] += $tempSubArray;
                      ++$a;
                  }
              } else {
                  $summedArray[$a] += $temp;
              }
          }

          return $summedArray;
      }

      public static function getScaledArray($unscaledArray, $scalar)
      {
          $scaledArray = [];

          foreach ($unscaledArray as $temp) {
              if (is_array($temp)) {
                  array_push($scaledArray, chart_util::getScaledArray($temp, $scalar));
              } else {
                  array_push($scaledArray, round($temp * $scalar, 2));
              }
          }

          return $scaledArray;
      }

      public static function getMaxCountOfArray($ArrayToCheck)
      {
          $maxValue = count($ArrayToCheck);

          foreach ($ArrayToCheck as $temp) {
              if (is_array($temp)) {
                  $maxValue = max($maxValue, chart_util::getMaxCountOfArray($temp));
              }
          }

          return $maxValue;
      }

      public static function getMaxOfArray($ArrayToCheck)
      {
          $maxValue = 1;

          foreach ($ArrayToCheck as $temp) {
              if (is_array($temp)) {
                  $maxValue = max($maxValue, chart_util::getMaxOfArray($temp));
              } else {
                  $maxValue = max($maxValue, $temp);
              }
          }

          return $maxValue;
      }
  }
    class gChart
    {
        public $types = ['lc','lxy','bhs','bvs','bhg','bvg','p','p3','v','s'];
        public $type = 1;
        public $dataEncodingType = 't';
        public $values = [];
        public $valueLabels;
        public $dataColors;
        public $width = 200; //default
        public $height = 200; //default
        protected $scaledValues = [];
        private $baseUrl = 'http://chart.apis.google.com/chart?';
        private $scalar = 1;
        private $title;

        public function setTitle($newTitle)
        {
            $this->title = str_replace("\r\n", '|', $newTitle);
            $this->title = str_replace(' ', '+', $this->title);
        }

        public function addDataSet($dataArray)
        {
            array_push($this->values, $dataArray);
        }

        public function clearDataSets()
        {
            $this->values = [];
        }

        public function getUrl()
        {
            $this->prepForUrl();

            return $this->concatUrl();
        }

        public function printIt()
        {
            echo "<br>Scalar:$this->scalar <br>";
            echo '<br>Values:' . print_r($this->values) . '<br>';
            echo '<br>Values:' . print_r($this->scaledValues) . '<br>';
        }

        public function setScalar()
        {
            $maxValue = 100;
            $maxValue = max($maxValue, chart_util::getMaxOfArray($this->values));
            if ($maxValue < 100) {
                $this->scalar = 1;
            } else {
                $this->scalar = 100 / $maxValue;
            }
        }

        protected function encodeData($data, $encoding, $separator)
        {
            switch ($this->dataEncodingType) {
                case 's':
                    return $this->simpleEncodeData();
                case 'e':
                    return $this->extendedEncodeData();
                default:
                    $retStr = $this->textEncodeData($data, $separator, '|');
                    $retStr = trim($retStr, '|');

                    return $retStr;
            }
        }

        protected function prepForUrl()
        {
            $this->scaleValues();
        }

        protected function concatUrl()
        {
            $fullUrl .= $this->baseUrl;
            $fullUrl .= 'cht=' . $this->types[$this->type];
            $fullUrl .= '&chs=' . $this->width . 'x' . $this->height;
            $fullUrl .= '&chd=' . $this->dataEncodingType . ':' . $this->encodeData($this->scaledValues, '', ',');
            if (isset($this->valueLabels)) {
                $fullUrl .= '&chdl=' . $this->encodeData($this->getApplicableLabels($this->valueLabels), '', '|');
            }
            $fullUrl .= '&chco=' . $this->encodeData($this->dataColors, '', ',');
            if (isset($this->title)) {
                $fullUrl .= '&chtt=' . $this->title;
            }
            $fullUrl .= '&chf=bg,s,000000';
            $fullUrl .= '&chm=';
            for ($i = 0; $i < count($this->values); ++$i) {
                $fullUrl .= 'N*f0*,ffffff,' . $i . ',-1,20,11|';
            }
            $fullUrl = substr($fullUrl, 0, strlen($fullUrl) - 1);

            //<label type><label contents>,<color>,<data set index>,<data point>,<size>,<priority>|

            return $fullUrl;
        }

        protected function getApplicableLabels($labels)
        {
            $trimmedValueLabels = $labels;

            return array_splice($trimmedValueLabels, 0, count($this->values));
        }

        protected function scaleValues()
        {
            $this->setScalar();
            $this->scaledValues = chart_util::getScaledArray($this->values, $this->scalar);
        }

        private function textEncodeData($data, $separator, $datasetSeparator)
        {
            $retStr = '';
            if (!is_array($data)) {
                return $data;
            }
            foreach ($data as $currValue) {
                if (is_array($currValue)) {
                    $retStr .= $this->textEncodeData($currValue, $separator, $datasetSeparator);
                } else {
                    $retStr .= $currValue . $separator;
                }
            }

            $retStr = trim($retStr, $separator);
            $retStr .= $datasetSeparator;

            return $retStr;
        }

        private function simpleEncodeData()
        {
            return '';
        }

        private function extendedEncodeData()
        {
            return '';
        }
    }

    class gPieChart extends gChart
    {
        public function __construct()
        {
            $this->type = 6;
            $this->width = $this->height * 1.5;
        }

        public function setScalar()
        {
            return 1;
        }

        public function getUrl()
        {
            $retStr = parent::getUrl();
            $retStr .= '&chl=' . $this->encodeData($this->valueLabels, '', '|');

            return $retStr;
        }

        public function set3D($is3d)
        {
            if ($is3d) {
                $this->type = 7;
                $this->width = $this->height * 2;
            } else {
                $this->type = 6;
                $this->width = $this->height * 1.5;
            }
        }

        private function getScaledArray($unscaledArray, $scalar)
        {
            return $unscaledArray;
        }
    }

    class gLineChart extends gChart
    {
        public function __construct()
        {
            $this->type = 0;
        }
    }

    class gBarChart extends gChart
    {
        public $barWidth;
        public $groupSpacerWidth = 1;
        protected $totalBars = 1;
        private $isHoriz = false;

        public function getUrl()
        {
            $this->scaleValues();
            $retStr = parent::concatUrl();
            $this->setBarWidth();
            $retStr .= "&chbh=$this->barWidth,$this->groupSpacerWidth";

            return $retStr;
        }

        public function setBarCount()
        {
            $this->totalBars = chart_util::count_r($this->values);
        }

        private function setBarWidth()
        {
            if (isset($this->barWidth)) {
                return;
            }
            $this->setBarCount();
            $totalGroups = chart_util::getMaxCountOfArray($this->values);
            $chartSize = $this->width - 50;
            if ($this->isHoriz) {
                $chartSize = $this->height - 50;
            }
            $chartSize -= $totalGroups * $this->groupSpacerWidth;
            $this->barWidth = round($chartSize / $this->totalBars);
        }
    }
    class gGroupedBarChart extends gBarChart
    {
        public function __construct()
        {
            $this->type = 5;
        }

        public function setHorizontal($isHorizontal)
        {
            if ($isHorizontal) {
                $this->type = 4;
            } else {
                $this->type = 5;
            }
            $this->isHoriz = $isHorizontal;
        }
    }
    class gStackedBarChart extends gBarChart
    {
        public function __construct()
        {
            $this->type = 3;
        }

        public function setBarCount()
        {
            $this->totalBars = chart_util::getMaxCountOfArray($this->values);
        }

        public function setHorizontal($isHorizontal)
        {
            if ($isHorizontal) {
                $this->type = 2;
            } else {
                $this->type = 3;
            }
            $this->isHoriz = $isHorizontal;
        }

        public function setScalar()
        {
            $maxValue = 100;
            $maxValue = max($maxValue, chart_util::getMaxOfArray(chart_util::addArrays($this->values)));
            if ($maxValue < 100) {
                $this->scalar = 1;
            } else {
                $this->scalar = 100 / $maxValue;
            }
        }

        protected function scaleValues()
        {
            $this->setScalar();
            $this->scaledValues = chart_util::getScaledArray($this->values, $this->scalar);
        }
    }
