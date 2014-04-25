<?php
namespace XE\UITest\Selenium\Util;

/**
  * @file Output.php
  * @brief 출력 결과에 color 적용 \n
  * @author NAVER (developers@xpressengine.com)

    $this->foreground_colors['black'] = '0;30';
    $this->foreground_colors['dark_gray'] = '1;30';
    $this->foreground_colors['blue'] = '0;34';
    $this->foreground_colors['light_blue'] = '1;34';
    $this->foreground_colors['green'] = '0;32';
    $this->foreground_colors['light_green'] = '1;32';
    $this->foreground_colors['cyan'] = '0;36';
    $this->foreground_colors['light_cyan'] = '1;36';
    $this->foreground_colors['red'] = '0;31';
    $this->foreground_colors['light_red'] = '1;31';
    $this->foreground_colors['purple'] = '0;35';
    $this->foreground_colors['light_purple'] = '1;35';
    $this->foreground_colors['brown'] = '0;33';
    $this->foreground_colors['yellow'] = '1;33';
    $this->foreground_colors['light_gray'] = '0;37';
    $this->foreground_colors['white'] = '1;37';

    $this->background_colors['black'] = '40';
    $this->background_colors['red'] = '41';
    $this->background_colors['green'] = '42';
    $this->background_colors['yellow'] = '43';
    $this->background_colors['blue'] = '44';
    $this->background_colors['magenta'] = '45';
    $this->background_colors['cyan'] = '46';
    $this->background_colors['light_gray'] = '47';
  */
class Output
{
    /**
      * @brief 글자색
      * @param string $forground_color
      * @return string
      */
    private static function getForeground($forground_color)
    {
        return "\033[".$forground_color."m";
    }

    /**
      * @brief 배경색 
      * @param string $background_color
      * @return string
      */
    private static function getBackground($background_color)
    {
        if ($background_color) {
            return "\033[".$background_color."m";
        }
    }

    /**
      * @brief 배경색 
      * @param string $str
      * @param string $forground_color
      * @param string $background_color
      * @return string
      */
    private static function getColorString($str, $forground_color, $background_color)
    {
        return self::getForeground($forground_color) . self::getBackground($background_color) . $str . "\033[0m";
    }

    /**
      * @brief 정보 표시 설정
      * @param string $str
      * @return void
      */
    public static function info($str)
    {
        echo self::getColorString($str, "1;32", "") . "\n";
    }

    /**
      * @brief 로그 표시 설정
      * @param string $str
      * @return void
      */
    public static function log($str)
    {
        echo self::getColorString($str, "1;35", "") . "\n";
    }

    /**
      * @brief 오류 표시 설정
      * @param string $str
      * @return void
      */
    public static function error($str)
    {
        echo self::getColorString($str, "0;37", "41") . "\n";
    }

    /**
      * @brief 경고 표시 설정
      * @param string $str
      * @return void
      */
    public static function warning($str)
    {
        echo self::getColorString($str, "0;30", "43") . "\n";
    }
}
