<?php
namespace WScore\DiContainer\Types;

class DateJpn extends \DateTime
{
    /**
     * @var string
     */
    public $format = 'Y年m月d日';

    /**
     * @param $w
     * @return string
     */
    function getWeek( $w ) {
        $week = array( '日','月','火','水','木','金','土','日' );
        return array_key_exists( $w, $week ) ? $week[ $w ] : null;
    }

    /**
     * @return string
     */
    function week() {
        $wk = $this->format( 'w' );
        return $this->getWeek( $wk );
    }

    /**
     * returns Japanese Era format.
     * 
     * @return string
     */
    function getNengou() {
        return $this->formatNengou( $this->format( 'Y-m-d' ) );
    }

    /**
     * 日本の年度表記（明治、大正、昭和、平成）。
     *  $date in 'YYYY-mm-dd'
     *
     * @param string $date
     * @return string
     */
    function formatNengou( $date )
    {
        $jpn = array(
            '平成' => '1989-01-08',
            '昭和' => '1926-12-25',
            '大正' => '1912-07-30',
            '明治' => '1868-01-25' );
        list( $year, $month, $day ) = explode( '-', $date );
        foreach( $jpn as $gou => $start )
        {
            if( $date < $start ) continue;
            $month   = (int) $month;
            $day     = (int) $day;
            $year    = $year - substr( $start, 0, 4 ) + 1;
            $date    = "{$gou}{$year}年{$month}月{$day}日";
            break;
        }
        return $date;
    }
}
