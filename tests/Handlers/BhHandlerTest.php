<?php

/**
 * @copyright Copyright (c) 2020 Joshua Smith
 * @license   See LICENSE file
 */

namespace Tests\Handlers;

use DMS\PHPUnitExtensions\ArraySubset\Assert;
use phpWhois\Handlers\BhHandler;

/**
 * BhHandlerTest.
 */
class BhHandlerTest extends AbstractHandler
{
    /**
     * @var BhHandler
     */
    protected $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new BhHandler();
        $this->handler->deepWhois = false;
    }

    public function testParseNicDotBh(): void
    {
        $query = 'nic.bh';

        $fixture = $this->loadFixture($query);
        $data = [
            'rawdata' => $fixture,
            'regyinfo' => [],
        ];

        $actual = $this->handler->parse($data, $query);

        $expected = [
            'domain' => [
                'name' => 'NIC.BH',
                'changed' => '2023-08-31',
                'created' => '2019-04-24',
                'expires' => '2029-04-24',
            ],
            'registered' => 'yes',
        ];

        Assert::assertArraySubset($expected, $actual['regrinfo'], 'Whois data may have changed');
        $this->assertArrayHasKey('rawdata', $actual);
        Assert::assertArraySubset($fixture, $actual['rawdata'], 'Fixture data may be out of date');
    }
}
