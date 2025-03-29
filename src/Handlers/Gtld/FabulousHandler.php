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

namespace phpWhois\Handlers\Gtld;

use phpWhois\Handlers\AbstractHandler;

class FabulousHandler extends AbstractHandler
{
    public function parse(array $data_str, string $query): array
    {
        $items = [
            'owner' => 'Domain '.$query.':',
            'admin' => 'Administrative contact:',
            'tech' => 'Technical contact:',
            'billing' => 'Billing contact:',
            '' => 'Record dates:',
        ];

        $r = static::easyParser($data_str, $items, 'mdy', [], false, true);

        if (!isset($r['tech'])) {
            $r['tech'] = $r['billing'];
        }

        if (!isset($r['admin'])) {
            $r['admin'] = $r['tech'];
        }

        return $r;
    }
}
