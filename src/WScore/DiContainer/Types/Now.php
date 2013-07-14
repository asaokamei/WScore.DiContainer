<?php
namespace WScore\DiContainer\Types;

class Now
{
    /**
     * @var \DateTime
     */
    public $datetime;

    /**
     * @param string $time
     * @param \DateTimeZone $timezone
     */
    public function __construct ($time='', \DateTimeZone $timezone=null) 
    {
        if( $time ) $this->datetime = new \DateTime( $time, $timezone );
    }

    /**
     * @param string $time
     */
    public function setNow( $time='now' ) {
        $this->datetime = new \DateTime( $time );
    }
    
    /**
     * @return \DateTime
     */
    public function getNow() {
        if( !isset( $this->datetime ) ) {
            $this->datetime = new \DateTime();
        }
        return $this->datetime;
    }

    /**
     * @param string $format
     * @return string
     */
    public function format( $format ) {
        return $this->getNow()->format( $format );
    }

    /**
     * @param int $year
     * @return $this
     */
    public function addYear( $year ) {
        $interval = new \DateInterval( "P" . abs( $year ) . "Y" );
        return $this->mod( $interval, $year );
    }

    /**
     * @param int $month
     * @return $this
     */
    public function addMonth( $month ) {
        $interval = new \DateInterval( "P" . abs( $month ) . "M" );
        return $this->mod( $interval, $month );
    }

    /**
     * @param int $days
     * @return $this
     */
    public function addDay( $days ) {
        $interval = new \DateInterval( "P" . abs( $days ) . "D" );
        return $this->mod( $interval, $days );
    }

    /**
     * time in HH:mm:ss, or -HH:mm:ss. mm and ss maybe omitted.
     * 
     * @param string $time
     * @return $this
     */
    public function addTime( $time ) 
    {
        $value = 1;
        if( substr( $time, 0, 1 ) === '-' ) {
            $time = substr( $time, 1 );
            $value = -1;
        }
        $times = explode( ':', $time );
        $interval = 'PT';
        $periods   = array( 'H', 'M', 'S' );
        foreach( $times as $t ) {
            $interval .= $t.$periods[0];
            array_shift( $periods );
        }
        return $this->mod( $interval, $value );
    }

    /**
     * @param string $interval
     * @param int $value
     * @return $this
     */
    public function mod( $interval, $value=1 )
    {
        $method   = $value < 0 ? 'sub' : 'add';
        $this->getNow()->$method( $interval );
        return $this;
    }

    /**
     * @param string $method
     * @param array  $args
     * @return mixed|null
     */
    public function __call( $method, $args )
    {
        $now = $this->getNow();
        if( method_exists( $now, $method ) ) {
            return call_user_func_array( array( $now, $method ), $args );
        }
        return null;
    }
}