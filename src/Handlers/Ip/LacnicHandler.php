<?php

/**
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
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
 * @see http://phpwhois.pw
 *
 * @copyright Copyright (C)1999,2005 easyDNS Technologies Inc. & Mark Jeftovic
 * @copyright Maintained by David Saez
 * @copyright Copyright (c) 2014 Dmitry Lukashin
 */

namespace phpWhois\Handlers\Ip;

use phpWhois\Handlers\AbstractHandler;

class LacnicHandler extends AbstractHandler
{
    public function parse(array $data_str, string $query): array
    {
        $translate = [
            'fax-no' => 'fax',
            'e-mail' => 'email',
            'nic-hdl-br' => 'handle',
            'nic-hdl' => 'handle',
            'person' => 'name',
            'netname' => 'name',
            'descr' => 'desc',
            'country' => 'address.country',
        ];

        $contacts = [
            'owner-c' => 'owner',
            'tech-c' => 'tech',
            'abuse-c' => 'abuse',
            'admin-c' => 'admin',
        ];

        $r = static::generic_parser_a($data_str, $translate, $contacts, 'network');

        unset($r['network']['owner'], $r['network']['ownerid'], $r['network']['responsible'], $r['network']['address'], $r['network']['phone'], $r['network']['aut-num'], $r['network']['nsstat'], $r['network']['nslastaa'], $r['network']['inetrev']);

        if (!empty($r['network']['aut-num'])) {
            $r['network']['handle'] = $r['network']['aut-num'];
        }

        if (isset($r['network']['nserver'])) {
            $r['network']['nserver'] = \array_unique($r['network']['nserver']);
        }

        $r = ['regrinfo' => $r];
        $r['regyinfo']['type'] = 'ip';
        $r['regyinfo']['registrar'] = 'Latin American and Caribbean IP address Regional Registry';

        return $r;
    }
}
