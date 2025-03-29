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
 *
 * @see      http://phpwhois.pw
 *
 * @copyright Copyright (C)1999,2005 easyDNS Technologies Inc. & Mark Jeftovic
 * @copyright Maintained by David Saez
 * @copyright Copyright (c) 2014 Dmitry Lukashin
 */
namespace phpWhois\Handlers\Gtld;

use phpWhois\Handlers\AbstractHandler;
use phpWhois\WhoisClient;

class GtldHandler extends WhoisClient
{
    protected array $result = [];

    public const REG_FIELDS = [
        'Domain Name:' => 'regrinfo.domain.name',
        'Registrar:' => 'regyinfo.registrar',
        'Whois Server:' => 'regyinfo.whois',
        'Referral URL:' => 'regyinfo.referrer',
        'Name Server:' => 'regrinfo.domain.nserver.', // identical descriptors
        'Updated Date:' => 'regrinfo.domain.changed',
        'Last Updated On:' => 'regrinfo.domain.changed',
        'EPP Status:' => 'regrinfo.domain.epp_status.',
        'Status:' => 'regrinfo.domain.status.',
        'Creation Date:' => 'regrinfo.domain.created',
        'Created On:' => 'regrinfo.domain.created',
        'Expiration Date:' => 'regrinfo.domain.expires',
        'Registry Expiry Date:' => 'regrinfo.domain.expires',
        'No match for ' => 'nodomain',
    ];

    public function parse(array $data, string $query): array
    {
        $this->query = [];
        $this->result = AbstractHandler::generic_parser_b($data['rawdata'], self::REG_FIELDS, 'dmy');

        unset($this->result['registered']);

        if (isset($this->result['nodomain'])) {
            unset($this->result['nodomain']);
            $this->result['regrinfo']['registered'] = 'no';

            return $this->result;
        }

        if ($this->deepWhois) {
            $this->result = $this->deepWhois($query, $this->result);
        }

        // Next server could fail to return data
        if (empty($this->result['rawdata']) || \count($this->result['rawdata']) < 3) {
            $this->result['rawdata'] = $data['rawdata'];
        }

        // Domain is registered no matter what next server says
        $this->result['regrinfo']['registered'] = 'yes';

        return $this->result;
    }
}
