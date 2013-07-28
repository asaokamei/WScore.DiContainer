<?php
namespace WScore\DiContainer\Types;

class DateTime extends \DateTime
{
    /**
     * @var string
     */
    public $format = 'Y-m-d H:i:s';

    /**
     * @param string $format
     */
    public function setFormat( $format ) {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->format( $this->format );
    }

    /**
     * @param int $years
     * @return $this
     */
    public function addYear( $years ) {
        $this->add( new \DateInterval( "P{$years}Y" ) );
        return $this;
    }

    /**
     * @param int $months
     * @return $this
     */
    public function addMonth( $months ) {
        $this->add( new \DateInterval( "P{$months}M" ) );
        return $this;
    }

    /**
     * @param int $days
     * @return $this
     */
    public function addDay( $days ) {
        $this->add( new \DateInterval( "P{$days}D" ) );
        return $this;
    }

    /**
     * @param int $months
     * @return $this
     */
    public function nextMonth( $months=1 ) {
        $currDay = $this->format( 'd' );
        $this->addMonth( $months );
        $nextDay = $this->format( 'd' );
        if( $currDay !== $nextDay ) {
            $this->setDate( $this->format( 'Y' ), $this->format( 'm' ), $this->format( 't' ) );
        }
        return $this;
    }
}