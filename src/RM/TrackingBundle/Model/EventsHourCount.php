<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 04/12/2015
 * Time: 14:22
 */

namespace RM\TrackingBundle\Model;


class EventsHourCount
{
    /**
     * @var array
     */
    private $event = [];


    /**
     * @param $tipo
     * @param $hora
     * @param $total
     *
     * @return $this
     */
    public function setEvent($tipo, $hora, $total)
    {
        $this->event[$tipo][$hora] = $total;
        return $this;
    }

    /**
     * @param $tipo
     *
     * @return array|bool
     */
    public function getEventsByHour($tipo)
    {
        if ($this->eventExist($tipo)) {
            ksort($this->event[$tipo]);
            return $this->event[$tipo];
        }

        return [];
    }

    /**
     * @param $tipo
     *
     * @return bool
     */
    public function eventExist($tipo)
    {
        return array_key_exists($tipo, $this->event);
    }

    /**
     * @param array $events
     *
     * @return EventsHourCount
     */
    public static function createFromMongoArray(array $events)
    {
        $self = new self();
        foreach ($events as $event) {
            if(isset($event['_id'])) {
                $self->setEvent(
                    $event['_id']['tipo'],
                    $event['_id']['hora'],
                    $event['total']
                );
            }
        }

        return $self;
    }

} 