<?php

namespace MFB\AccountBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetControllerTest extends WebTestCase
{
    public function testSendemail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/send');
    }

}
