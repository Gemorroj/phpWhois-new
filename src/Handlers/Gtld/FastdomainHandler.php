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

class FastdomainHandler extends AbstractHandler
{
    public function parse(array $data_str, string $query): array
    {
        $items = [
            'owner' => 'Registrant Info:',
            'admin' => 'Administrative Info:',
            'tech' => 'Technical Info:',
            'domain.name' => 'Domain Name:',
            'domain.sponsor' => 'Provider Name....:',
            'domain.referrer' => 'Provider Homepage:',
            'domain.nserver' => 'Domain servers in listed order:',
            'domain.created' => 'Created on..............:',
            'domain.expires' => 'Expires on..............:',
            'domain.changed' => 'Last modified on........:',
            'domain.status' => 'Status:',
        ];

        foreach ($data_str as $key => $val) {
            $faststr = \strpos($val, ' (FAST-');
            if ($faststr) {
                $data_str[$key] = \substr($val, 0, $faststr);
            }
        }

        $r = static::easyParser($data_str, $items, 'dmy', [], false, true);

        if (isset($r['domain']['sponsor']) && \is_array($r['domain']['sponsor'])) {
            $r['domain']['sponsor'] = $r['domain']['sponsor'][0];
        }

        if (isset($r['domain']['nserver'])) {
            foreach ($r['domain']['nserver'] as $key => $val) {
                if ('=-=-=-=' === $val) {
                    unset($r['domain']['nserver'][$key]);
                }
            }
        }

        return $r;
    }
}
