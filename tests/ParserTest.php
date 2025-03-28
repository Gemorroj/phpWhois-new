<?php

/**
 * @copyright Copyright (c)2017 Joshua Smith
 * @license   GPL-2.0
 */

namespace phpWhois\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use phpWhois\Handlers\AbstractHandler;

/**
 * ParserTest.
 */
class ParserTest extends BaseTestCase
{
    protected const RAW_DATA = [
        "Domain: \${field}\n",
        "Registrar:\n",
        "\tName:\t \${field}\n",
        "fax: \${field}\n",
        "Registrant Name: \${k}\n",
    ];

    public static function get_dateProvider(): array
    {
        return [
            'nic.ag' => [
                'date' => '1998-05-02T04:00:00Z',
                'format' => 'ymd',
                'expected' => '1998-05-02',
            ],
            'nic.at' => [
                'date' => '20121116 16:58:21',
                'format' => 'Ymd',
                'expected' => '2012-11-16',
            ],
            'telstra.com.au' => [
                'date' => '11-May-2016 05:18:45 UTC',
                'format' => 'mdy',
                'expected' => '2016-05-11',
            ],
            'registro.br' => [
                'date' => '19971217',
                'format' => 'Ymd',
                'expected' => '1997-12-17',
            ],
            'registro.br-2' => [
                'date' => '19990221 #142485',
                'format' => 'Ymd',
                'expected' => '1999-02-21',
            ],
            'cira.ca' => [
                'date' => '1998/02/05',
                'format' => 'Ymd',
                'expected' => '1998-02-05',
            ],
            'nic.co' => [
                'date' => '2010-04-23T09:12:48Z',
                'format' => 'mdy',
                'expected' => '2010-04-23',
            ],
            'day smaller than month' => [
                'date' => '2010-06-02T01:32:58Z',
                'format' => 'ymd',
                'expected' => '2010-06-02',
            ],
            'nic.cz' => [
                'date' => '06.03.2002 18:11:00',
                'format' => 'dmy',
                'expected' => '2002-03-06',
            ],
            'nic.cz-2' => [
                'date' => '15.03.2027 18:11:00',
                'format' => 'dmy',
                'expected' => '2027-03-15',
            ],
            'nic.fr' => [
                'date' => '23/08/2005 hostmaster@nic.fr',
                'format' => 'dmY',
                'expected' => '2005-08-23',
            ],
            'nic.hu' => [
                'date' => '1996.06.27 13:36:21',
                'format' => 'ymd',
                'expected' => '1996-06-27',
            ],
            'domainregistry.ie' => [
                'date' => '01-January-2025',
                'format' => 'Ymd',
                'expected' => '2025-01-01',
            ],
            'isnic.is' => [
                'date' => 'November  6 2000',
                'format' => 'mdy',
                'expected' => '2000-11-06',
            ],
            'dns.lu' => [
                'date' => '31/05/1995',
                'format' => 'dmy',
                'expected' => '1995-05-31',
            ],
            'olsns.co.uk' => [
                'date' => '21-Feb-2001',
                'format' => 'dmy',
                'expected' => '2001-02-21',
            ],
            'dominis.cat' => [
                'date' => '2017-07-29T11:00:47.438Z',
                'format' => 'mdy',
                'expected' => '2017-07-29',
            ],
            'google.ws' => [
                'date' => '2021-03-03T00:00:00-0800',
                'format' => 'mdy',
                'expected' => '2021-03-03',
            ],
        ];
    }

    #[DataProvider('get_dateProvider')]
    public function testGetDate(string $date, string $format, string $expected): void
    {
        $actual = AbstractHandler::getDate($date, $format);
        $this->assertEquals($expected, $actual);
    }

    #[Group('CVE-2015-5243')]
    public function testGenericParserABlocks(): void
    {
        $translate = [
            'Registrant Name' => 'owner.name',
        ];

        $disclaimer = [];
        $output = AbstractHandler::generic_parser_a_blocks(static::RAW_DATA, $translate, $disclaimer);
        $this->assertEquals('${k}', $output['main']['owner']['name']);
    }

    #[Group('CVE-2015-5243')]
    public function testGenericParserB(): void
    {
        $output = AbstractHandler::generic_parser_b(static::RAW_DATA);
        $this->assertEquals('${k}', $output['owner']['name']);
    }

    #[Group('CVE-2015-5243')]
    public function testGetBlocksOne(): void
    {
        $items = [
            'domain.name' => 'Domain:',
        ];

        $output = AbstractHandler::getBlocks(static::RAW_DATA, $items);
        $this->assertEquals('${field}', $output['domain']['name']);
    }

    #[Group('CVE-2015-5243')]
    public function testGetBlocksTwo(): void
    {
        $items = [
            'agent' => 'Registrar:',
        ];

        $output = AbstractHandler::getBlocks(static::RAW_DATA, $items);
        $this->assertEquals("Name:\t \${field}", $output['agent'][0]);
    }

    #[Group('CVE-2015-5243')]
    public function testGetContact(): void
    {
        $output = AbstractHandler::getContact(static::RAW_DATA);
        $this->assertEquals('${field}', $output['fax']);
    }
}
