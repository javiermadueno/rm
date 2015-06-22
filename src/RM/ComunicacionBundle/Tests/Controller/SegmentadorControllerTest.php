<?php

namespace RM\ComunicacionBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SegmentadorControllerTest extends WebTestCase
{
    public function testShowsegmentadoravanzado()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/campaign/segmentador/promocion/{id_promocion}');
    }

}
