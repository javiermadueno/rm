<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 04/12/2015
 * Time: 10:02
 */

namespace RM\TrackingBundle\Model;


class EventsCount
{

    /**
     * @var
     */
    private $events = [];


    /**
     * @param array $evento
     */
    private  function addEvent(array $evento)
    {
        if(isset($evento['_id'])) {
            $this->setEvent($evento['_id'], $evento['total']);
        }
    }

    /**
     * @param $tipo
     * @param $total
     *
     * @return $this
     */
    public function setEvent($tipo, $total)
    {
        $this->events[$tipo] =  $total;
        return $this;
    }


    /**
     * @param $tipo
     *
     * @return int
     */
    public function getEventCount($tipo)
    {
        if($this->eventExist($tipo)) {
            return (int) $this->events[$tipo];
        }

        return 0;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param $tipo
     *
     * @return bool
     */
    public function eventExist($tipo)
    {
        if(is_null($this->events)) {
            $this->events = [];
        }
        return array_key_exists($tipo, $this->events);
    }

    /**
     * @param array $eventos
     *
     * @return EventsCount
     */
    public static function createFromMongoArray($eventos = [])
    {
        $self = new self();

        foreach ($eventos as $evento) {
            $self->addEvent($evento);
        }

        return $self;

    }

} 