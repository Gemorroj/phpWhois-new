<?php

/**
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @license
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * @copyright Copyright (c) 2018 Joshua Smith
 */

namespace phpWhois\Tests\Handlers;

use phpWhois\Handlers\OrgHandler;
use phpWhois\WhoisClient;

/**
 * OrgHandlerTest.
 */
class OrgHandlerTest extends AbstractHandler
{
    /**
     * @var OrgHandler
     */
    protected $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new OrgHandler(new WhoisClient());
        $this->handler->deepWhois = false;
    }

    public function testParseGoogleDotOrg(): void
    {
        $query = 'google.org';

        $fixture = $this->loadFixture($query);
        $data = [
            'rawdata' => $fixture,
            'regyinfo' => [],
        ];

        $actual = $this->handler->parse($data, $query);

        $expected = [
            'domain' => [
                'name' => 'GOOGLE.ORG',
                'changed' => '2017-09-18',
                'created' => '1998-10-21',
                'expires' => '2018-10-20',
            ],
            'registered' => 'yes',
        ];

        self::assertArrayIsEqualToArrayOnlyConsideringListOfKeys($expected['domain'], $actual['regrinfo']['domain'], $expected['domain'], 'Whois data may have changed');
        self::assertEquals($expected['registered'], $actual['regrinfo']['registered'], 'Whois data may have changed');
        self::assertArrayHasKey('rawdata', $actual);
        self::assertArrayIsEqualToArrayOnlyConsideringListOfKeys($fixture, $actual['rawdata'], $fixture, 'Fixture data may be out of date');
    }

    public function testParseDatesProperly(): void
    {
        $query = 'scottishrecoveryconsortium.org';

        $fixture = $this->loadFixture($query);
        $data = [
            'rawdata' => $fixture,
            'regyinfo' => [],
        ];

        $actual = $this->handler->parse($data, $query);

        $expected = [
            'domain' => [
                'name' => 'SCOTTISHRECOVERYCONSORTIUM.ORG',
            ],
            'registered' => 'yes',
        ];

        self::assertArrayIsEqualToArrayOnlyConsideringListOfKeys($expected['domain'], $actual['regrinfo']['domain'], $expected['domain'], 'Whois data may have changed');
        self::assertEquals($expected['registered'], $actual['regrinfo']['registered'], 'Whois data may have changed');
        self::assertArrayHasKey('rawdata', $actual);
        self::assertArrayIsEqualToArrayOnlyConsideringListOfKeys($fixture, $actual['rawdata'], $fixture, 'Fixture data may be out of date');

        $this->assertEquals('2020-01-13', $actual['regrinfo']['domain']['changed'], 'Incorrect change date');
        $this->assertEquals('2012-10-01', $actual['regrinfo']['domain']['created'], 'Incorrect created date');
        $this->assertEquals('2020-10-01', $actual['regrinfo']['domain']['expires'], 'Incorrect expiration date');
    }
}
