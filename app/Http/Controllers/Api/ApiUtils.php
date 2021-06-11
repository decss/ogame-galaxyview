<?php


namespace App\Http\Controllers\Api;


use App\Models\Activity;
use App\Models\EventPlayer;
use App\Models\EventSystem;
use App\Models\SystemApi;
use App\Models\SystemDate;
use App\Models\SystemItem;
use Illuminate\Support\Facades\DB;

class ApiUtils
{
    public static function parseGalaxy($text)
    {
        preg_match('~data-galaxy="([0-9]+)"~i', $text, $matches);
        $galaxy = $matches[1];
        preg_match('~data-system="([0-9]+)"~i', $text, $matches);
        $system = $matches[1];

        $table = $text;
        $table = substr_replace($table, null, 0, stripos($table, '<table id="galaxytable'));
        $table = substr_replace($table, null, 0, stripos($table, '<tbody>') + 7);
        $table = substr_replace($table, null, stripos($table, '</tbody>'), strlen($table));


        $parse = self::parseGalaxyItems($table);
        $result = [
            'galaxy' => $galaxy,
            'system' => $system,
            'items' => $parse['items'],
            'ignored' => $parse['self'],
        ];

        return $result;
    }

    public static function parseGalaxyItems($table)
    {
        $items = [];
        $self = [];
        $rows = explode('</tr>', $table);
        foreach ($rows as $row) {
            $pos = self::parsePos($row);

            if (!$pos) {
                continue;
            }

            if (self::checkPos($row)) {
                $items[$pos] = [
                    'planet' => self::parsePlanet($row),
                    'moon' => self::parseMoon($row),
                    'debris' => self::parseDebris($row),
                    'player' => self::parsePlayer($row),
                ];
            } elseif (self::checkSelf($row)) {
                $self[] = $pos;
            }

        }

        return ['items' => $items, 'self' => $self];
    }

    public static function updateSystem($array)
    {
        $result['count'] = [
            'ally' => 0,
            'players' => 0,
            'activity' => 0,
            'planets' => 0,
        ];

        $i = 0;
        $allyIds = [];
        $playerIds = [];

        $allyQuery = null;
        $allyParams = [];
        $playerQuery = null;
        $playerParams = [];
        $activityQuery = null;
        $activityParams = [];
        $planetsQuery = null;
        $planetsParams = [];

        foreach ($array['items'] as $pos => $item) {
            $playerId = (int)$item['player']['id'];
            $allyId = isset($item['player']['alliance']['id']) ? $item['player']['alliance']['id'] : 0;

            // Alliances
            if ($allyId && !in_array($allyId, $allyIds)) {
                $allyParams[":id_{$i}"] = $item['player']['alliance']['id'];
                $allyParams[":tag_{$i}"] = $item['player']['alliance']['tag'];
                $allyParams[":name_{$i}"] = $item['player']['alliance']['name'];
                $allyQuery .= ($allyQuery ? "," : "") . "(:id_{$i}, :tag_{$i}, :name_{$i})";
                $allyIds[] = $allyId;
                $result['count']['ally']++;
            }

            // Players
            if ($playerId && !in_array($playerId, $playerIds)) {
                if (in_array('i', $item['player']['states'])) {
                    $iFlag = 1;
                } elseif (in_array('I', $item['player']['states'])) {
                    $iFlag = 2;
                } else {
                    $iFlag = 0;
                }

                $playerParams[":id_{$i}"] = $item['player']['id'];
                $playerParams[":name_{$i}"] = $item['player']['name'];
                $playerParams[":ally_id_{$i}"] = (int)$allyId;
                $playerParams[":rank_{$i}"] = (int)$item['player']['rank'];
                $playerParams[":a_{$i}"] = (in_array('A', $item['player']['states']) ? 1 : 0);
                $playerParams[":o_{$i}"] = (in_array('o', $item['player']['states']) ? 1 : 0);
                $playerParams[":v_{$i}"] = (in_array('v', $item['player']['states']) ? 1 : 0);
                $playerParams[":b_{$i}"] = (in_array('b', $item['player']['states']) ? 1 : 0);
                $playerParams[":i_{$i}"] = $iFlag;
                $playerParams[":hp_{$i}"] = (in_array('hp', $item['player']['states']) ? 1 : 0);
                $playerQuery .= ($playerQuery ? "," : "") . "(:id_{$i}, :name_{$i}, :ally_id_{$i}, :rank_{$i}, "
                    . ":a_{$i}, :o_{$i}, :v_{$i}, :b_{$i}, :i_{$i}, :hp_{$i})";
                $playerIds[] = $playerId;
                $result['count']['players']++;
            }

            // Activity (planet, moon)
            // check $item['planet']['activity'] and $item['moon']['activity']
            foreach ([1 => 'planet', 2 => 'moon'] as $activityType => $object) {
                // Skip if inactive
                if (in_array('i', $item['player']['states']) || in_array('I', $item['player']['states'])) {
                    continue;
                }
                $k = $i . '_' . $activityType;
                if ($item[$object] && $item[$object]['activity']) {
                    $activity = self::getActivityTime($item[$object]['activity']);
                    $activityParams[":player_id_{$k}"] = (int)$item['player']['id'];
                    $activityParams[":coords_{$k}"] = $array['galaxy'] . ':' . $array['system'] . ':' . $pos;
                    $activityParams[":type_{$k}"] = $activityType;
                    $activityParams[":date_{$k}"] = $activity['date'];
                    $activityParams[":time_{$k}"] = $activity['time'];
                    $activityParams[":value_{$k}"] = $item[$object]['activity'];
                    $activityQuery .= ($activityQuery ? ", " : "")
                        . "(:player_id_{$k}, :coords_{$k}, :type_{$k}, :date_{$k}, :time_{$k}, :value_{$k})";
                    $result['count']['activity']++;
                }
            }

            // Planets
            $planetsParams[":gal_{$pos}"] = (int)$array['galaxy'];
            $planetsParams[":sys_{$pos}"] = (int)$array['system'];
            $planetsParams[":pos_{$pos}"] = (int)$pos;
            $planetsParams[":player_id_{$pos}"] = (int)$item['player']['id'];
            $planetsParams[":planet_id_{$pos}"] = (int)$item['planet']['id'];
            $planetsParams[":planet_name_{$pos}"] = (string)$item['planet']['name'];
            $planetsParams[":moon_name_{$pos}"] = $item['moon'] ? $item['moon']['name'] : '';
            $planetsParams[":moon_size_{$pos}"] = $item['moon'] ? (int)$item['moon']['size'] : 0;
            $planetsParams[":field_me_{$pos}"] = $item['debris'] ? (int)$item['debris']['metal'] : 0;
            $planetsParams[":field_cry_{$pos}"] = $item['debris'] ? (int)$item['debris']['crystal'] : 0;
            $planetsQuery .= ($planetsQuery ? ", " : "")
                . "(:gal_{$pos}, :sys_{$pos}, :pos_{$pos}, :player_id_{$pos}, :planet_id_{$pos}, "
                . ":planet_name_{$pos}, :moon_name_{$pos}, :moon_size_{$pos}, :field_me_{$pos}, :field_cry_{$pos})";
            $result['count']['planets']++;

            $i++;
        }

        // Alliances
        if ($allyQuery) {
            $allyQuery = "REPLACE INTO ogv_alliances (id, tag, name) VALUES {$allyQuery}";
            DB::insert($allyQuery, $allyParams);
        }

        // Players
        if ($playerQuery) {
            $playerQuery = "REPLACE INTO ogv_players (id, name, ally_id, rank, a, o, v, b, i, hp) VALUES {$playerQuery}";
            DB::insert($playerQuery, $playerParams);
        }

        // Activity
        if ($activityQuery) {
            $activityQuery = "INSERT IGNORE INTO ovg_activity (player_id, coords, type, date, time, value) VALUES {$activityQuery}";
            DB::insert($activityQuery, $activityParams);
        }

        // Planets/Systems
        if ($planetsQuery) {
            // Clear system
            $delParams = [
                ':gal' => $array['galaxy'],
                ':sys' => $array['system'],
            ];
            $delQuery = "DELETE FROM ovg_systems WHERE gal = :gal AND sys = :sys"
                . ($array['ignored'] ? " AND pos NOT IN (" . implode(',', $array['ignored']) . ")" : "");
            DB::delete($delQuery, $delParams);

            // Write new system entities
            $planetsQuery = "INSERT INTO ovg_systems
                  (gal, sys, pos, player_id, planet_id, planet_name, moon_name, moon_size, field_me, field_cry)
                  VALUES {$planetsQuery}";
            DB::insert($planetsQuery, $planetsParams);

        }
        // System update date
        $dateParams = [
            ':gal' => $array['galaxy'],
            ':sys' => $array['system'],
            ':updated' => date('Y-m-d H:i:s'),
        ];
        $dateQuery = "REPLACE INTO ovg_system_dates (gal, sys, updated) VALUES (:gal, :sys, :updated)";
        DB::insert($dateQuery, $dateParams);

        return $result;
    }

    public static function updateEvents($array)
    {
        $result = [
            'player' => [],
            'system' => [],
        ];

        // If system was not scanned before
        $dates = SystemDate::where(['gal' => $array['galaxy'], 'sys' => $array['system']])->first();
        if (!isset($dates->updated)) {
            return $result;
        }

        $models = [];
        $playerEvents = self::getPlayerEvents($array);
        foreach ($playerEvents as $playerId => $events) {
            foreach ($events as $type => $event) {
                $models[] = [
                    'player_id' => (int)$playerId,
                    'type' => (int)$type,
                    'json' => json_encode($event),
                ];

                if (!isset($result['player'][$type])) {
                    $result['player'][$type] = 1;
                } else {
                    $result['player'][$type]++;
                }
            }
        }
        if ($models) {
            EventPlayer::insert($models);
        }

        $models = [];
        $systemEvents = self::getSystemEvents($array);
        foreach ($systemEvents as $pos => $events) {
            foreach ($events as $type => $event) {
                $playerId = isset($array['items'][$pos]['player']['id'])
                    ? $array['items'][$pos]['player']['id']
                    : $event['player_id'];

                $threshold = 0;
                if (in_array($type, [20, 21])) {
                    $threshold = $event['size'];
                } elseif (in_array($type, [30, 31, 32, 33])) {
                    $threshold = $event['field'];
                }

                $models[] = [
                    'gal' => (int)$array['galaxy'],
                    'sys' => (int)$array['system'],
                    'pos' => (int)$pos,
                    'player_id' => (int)$playerId,
                    'type' => (int)$type,
                    'json' => json_encode($event),
                    'threshold' => $threshold,
                ];
                if (!isset($result['system'][$type])) {
                    $result['system'][$type] = 1;
                } else {
                    $result['system'][$type]++;
                }
            }
        }
        if ($models) {
            EventSystem::insert($models);
        }

        return $result;
    }

    public static function updateEspEvents($events)
    {
        $count = 0;
        $models = [];
        foreach ($events as $event) {
            $eventTime = strtotime($event['date']) + 3600 * 2; // Timezine fix
            $activity = self::getActivityTime(0, $eventTime);
            $models[] = [
                'player_id' => (int)$event['playerId'],
                'coords' => (string)$event['coords'],
                'type' => 3,
                'date' => $activity['date'],
                'time' => $activity['time'],
                'value' => $event['date'],
            ];
            $count++;
        }

        if ($models) {
            Activity::insertOrIgnore($models);
        }

        return $count;
    }

    public static function updateEspReports($reports)
    {
        $count = 0;
        $models = [];

        // Load systems to get system_id by coords
        $systemItems = SystemItem::where(function ($query) use ($reports) {
            foreach ($reports as $report) {
                list($gal, $sys, $pos) = explode(':', $report['coords']);
                $query->orWhere([
                    ['gal', $gal],
                    ['sys', $sys],
                    ['pos', $pos],
                ]);
            }
        });
        $systemItems->get();

        foreach ($reports as $report) {
            list($gal, $sys, $pos) = explode(':', $report['coords']);
            $item = $systemItems->get()->where('gal', $gal)->where('sys', $sys)->where('pos', $pos)->first();
            if (!$item) {
                continue;
            }

            $models[] = [
                'system_id' => $item->id,
                'coords' => (string)$report['coords'],
                'type' => (int)$report['type'],
                'api' => (string)$report['api'],
                'level' => (int)$report['level'],
                'res' => $report['res'],
                'fleet' => $report['fleet'],
                'def' => $report['def'],
                'date' => $report['dateFmt'],
            ];
            $count++;
        }

        if ($models) {
            SystemApi::insertOrIgnore($models);
        }

        return $count;
    }

    private static function getPlayerEvents(array $array)
    {
        $ids = [];
        foreach ($array['items'] as $item) {
            $ids[] = $item['player']['id'];
        }
        if ($ids) {
            $dbPlayers = DB::select("SELECT * FROM ogv_players WHERE id IN (" . implode(',', $ids) . ")");
        }

        $changes = [];
        foreach ($array['items'] as $item) {
            $player = $item['player'];
            if (isset($dbPlayers)) {
                $dbPlayer = self::getDbItemById($dbPlayers, $player['id']);
            }
            if (!isset($dbPlayer) || !$dbPlayer) {
                continue;
            }

            $id = $player['id'];
            $allyId = $player['alliance'] ? $player['alliance']['id'] : 0;

            // Name
            if ($dbPlayer->name != $player['name']) {
                $changes[$id][50] = [
                    'old' => $dbPlayer->name,
                    'new' => $player['name']
                ];
            }

            // Rank
            // 5% threshold
            $threshold = 0;
            if (isset($dbPlayer->rank) && $player['rank']) {
                $threshold = abs($dbPlayer->rank- $player['rank']) / $player['rank'];
            }
            if ($dbPlayer->rank != $player['rank'] && $threshold > 0.05) {
                $changes[$id][60] = [
                    'old' => $dbPlayer->rank,
                    'new' => $player['rank'],
                ];
            }

            // Alliance
            if ($dbPlayer->ally_id != $allyId) {
                $changes[$id][70] = [
                    'old' => intval($dbPlayer->ally_id),
                    'new' => $allyId
                ];
            }

            // Status
            $states = [];
            $stateTypes = ["o" => 41, "v" => 42, "b" => 43, "i" => 44, "hp" => 45];
            foreach (["o", "v", "b", "i", "I", "hp"] as $state) {
                $value = in_array($state, $player['states']) ? 1 : 0;
                $states[$state] = $value;
            }
            // Fix: I => i
            if ($states['I'] == 1) {
                $states['i'] = 2;
            }
            unset($states['I']);

            foreach ($states as $s => $val) {
                $sLc = strtolower($s);
                if ($dbPlayer->{$sLc} != $val) {
                    $type = $stateTypes[$sLc];
                    $changes[$id][$type] = [
                        'old' => $dbPlayer->{$sLc},
                        'new' => $states[$s],
                    ];
                }
            }
        }

        return $changes;
    }

    private static function getSystemEvents(array $array)
    {
        $ids = [];
        $changes = [];
        $dbPlanets = [];
        $dfThreshold = 100000;
        foreach ($array['items'] as $pos => $item) {
            $ids[] = $pos;
        }

        if ($ids) {
            $dbRows = DB::select("SELECT * FROM ovg_systems WHERE gal = :gal AND sys = :sys AND pos IN (" . implode(',', $ids) . ")", [
                ':gal' => $array['galaxy'],
                ':sys' => $array['system'],
            ]);
            foreach ($dbRows as $row) {
                $dbPlanets[$row->pos] = $row;
            }
        }

        // New events
        foreach ($array['items'] as $pos => $item) {
            // 10 new planet
            if (!isset($dbPlanets[$pos])) {
                $changes[$pos][10] = [
                    'name' => $item['planet']['name'],
                    'player_id' => $item['player']['id'],
                ];
            }
            // 20 new moon
            if ($item['moon'] && $item['moon']['size'] && !isset($dbPlanets[$pos]->moon_size)) {
                $changes[$pos][20] = [
                    'name' => $item['moon']['name'],
                    'size' => $item['moon']['size'],
                    'player_id' => $item['player']['id'],
                ];
            }

            // threshold: $arrField > 10 k
            $arrField = $item['debris'] ? ($item['debris']['metal'] + $item['debris']['crystal']) : 0;
            $dbField = isset($dbPlanets[$pos]) ? intval($dbPlanets[$pos]->field_me + $dbPlanets[$pos]->field_cry) : 0;
            // 30 new field
            if ($arrField && !$dbField && $arrField >= $dfThreshold) {
                $changes[$pos][30] = [
                    'field' => $arrField,
                    'field_me' => $item['debris']['metal'],
                    'field_cry' => $item['debris']['crystal'],
                ];
                // 32 increased field
                // 33 decreased field
            } elseif (
                (($arrField > $dbField && $dbField > 0) || ($arrField > 0 && $arrField < $dbField))
                && ($arrField >= $dfThreshold || $dbField >= $dfThreshold)
            ) {
                $act = ($arrField > $dbField) ? 32 : 33;
                $changes[$pos][$act] = [
                    'field' => $arrField,
                    'field_me' => $item['debris']['metal'],
                    'field_cry' => $item['debris']['crystal'],
                    'oldfield' => $dbField,
                    'oldfield_me' => $dbPlanets[$pos]->field_me,
                    'oldfield_cry' => $dbPlanets[$pos]->field_cry,
                ];
            }
        }

        // Destroy events
        foreach ($dbPlanets as $pos => $dbPlanet) {
            $item = $array['items'][$pos];
            // 11 destroyed planet
            if (!$item['planet']) {
                $changes[$pos][11] = [
                    'name' => $dbPlanet->planet_name,
                    'player_id' => $dbPlanet->player_id,
                ];
            }
            // 21 destroyed moon
            if ($dbPlanets[$pos]->moon_size && !$item['moon']) {
                $changes[$pos][21] = [
                    'name' => $dbPlanets[$pos]->moon_name,
                    'size' => $dbPlanets[$pos]->moon_size,
                    'player_id' => $dbPlanet->player_id,
                ];
            }
            // 31 removed field
            $dbField = isset($dbPlanets[$pos]) ? intval($dbPlanets[$pos]->field_me + $dbPlanets[$pos]->field_cry) : 0;
            if ($dbField && !$item['debris'] && $dbField >= $dfThreshold) {
                $changes[$pos][31] = [
                    'field' => ($dbPlanets[$pos]->field_me + $dbPlanets[$pos]->field_cry),
                    'field_me' => $dbPlanets[$pos]->field_me,
                    'field_cry' => $dbPlanets[$pos]->field_cry,
                ];
            }
        }

        return $changes;
    }

    private static function getDbItemById($items, $id)
    {
        foreach ($items as $item) {
            if ($id && $item && $item->id == $id) {
                return $item;
            }
        }
        return [];
    }


    /** Utility
     */
    public static function checkPos($row)
    {
        $cols = explode("</td>", $row);

        if (stristr($cols[0], 'empty_filter') || !stristr($cols[5], 'data-playerid=')) {
            return false;
        }

        return true;
    }

    public static function checkSelf($row)
    {
        $cols = explode("</td>", $row);

        if (!stristr($cols[0], 'empty_filter')
            && stristr($cols[5], 'status_abbr_active')
            && !stristr($cols[5], 'data-playerid=')) {
            return true;
        }
        return false;
    }

    public static function parseVal($pattern, $text, $index = 1)
    {
        preg_match("~{$pattern}~i", $text, $matches);
        if ($matches && ($matches[$index] || strlen($matches[$index]))) {
            return $matches[$index];
        }

        return null;
    }

    public static function parsePos($row)
    {
        $cols = explode("</td>", $row);
        $pos = trim(strip_tags($cols[0]));

        if (!$pos || $pos > 15) {
            return null;
        }

        return $pos;
    }

    public static function getActivityTime($activity, $now = null)
    {
        $now = $now ? $now : time();

        if ($activity === '*') {
            $ts = $now - 5 * 60;
        } else {
            $ts = $now - intval($activity) * 60;
        }

        $minute = date('i', $ts);
        if ($minute < 10) {
            $minute = '00';
        } else {
            $minute = $minute[0] . '0';
        }

        return [
            'date' => date("Y-m-d", $ts),
            'time' => date("H:{$minute}:00", $ts),
        ];
    }


    /** Planet parse
     */
    public static function parsePlanet($row)
    {
        $cols = explode("</td>", $row);

        if (self::checkPos($row)) {
            return [
                'id' => (int)self::parsePlanetId($cols[1]),
                'name' => self::parsePlanetName($cols[1]),
                'activity' => self::parseActivity($cols[1]),
            ];
        }

        return [];
    }

    public static function parsePlanetId($col)
    {
        return self::parseVal('data-planet-id="([0-9]+)"', $col);
    }

    public static function parsePlanetName($col)
    {
        return self::parseVal('<h1>Planet: <span class="textNormal">([\w\d\s_-]+)</span></h1>', $col);
    }

    public static function parseActivity($col)
    {
        if (stristr($col, '<div class="activity')) {
            $div = $col;
            $div = substr_replace($div, null, 0, stripos($div, '<div class="activity'));
            $div = substr_replace($div, null, stripos($div, '</div>'));

            if (stristr($div, 'minute15')) {
                return '*';
            } elseif (stristr($div, 'showMinutes')) {
                return intval(trim(strip_tags($div)));
            }
        }

        return false;
    }


    /** Moon parse
     */
    public static function parseMoon($row)
    {
        $cols = explode("</td>", $row);

        // If no moon
        if (!stristr($cols[3], 'data-moon-id')) {
            return [];
        }

        return [
            'id' => (int)self::parseMoonId($cols[3]),
            'name' => self::parseMoonName($cols[3]),
            'size' => self::parseMoonSize($cols[3]),
            'activity' => self::parseActivity($cols[3]),
        ];
    }

    public static function parseMoonId($col)
    {
        return self::parseVal('data-moon-id="([0-9]+)"', $col);
    }

    public static function parseMoonName($col)
    {
        return trim(self::parseVal('<span class="textNormal">([\w\d\s]+)</span>', $col));
    }

    public static function parseMoonSize($col)
    {
        return self::parseVal('<li><span id="moonsize" title="Diameter of moon in km">([0-9]+) km</span></li>', $col);
    }


    /** Debris parse
     */
    public static function parseDebris($row)
    {
        $cols = explode("</td>", $row);

        // If no moon
        if (!stristr($cols[4], 'debris-content')) {
            return [];
        }

        $metal = self::parseVal('<li class="debris-content">Metal: ([0-9\.\s]+)</li>', $cols[4]);
        $metal = intval(preg_replace("~[^0-9]~", null, $metal));
        $crystal = self::parseVal('<li class="debris-content">Crystal: ([0-9\.\s]+)</li>', $cols[4]);
        $crystal = intval(preg_replace("~[^0-9]~", null, $crystal));

        return [
            'metal' => $metal,
            'crystal' => $crystal,
        ];
    }


    /** Player parse
     */
    public static function parsePlayer($row)
    {
        if (self::checkPos($row)) {
            $cols = explode("</td>", $row);
            return [
                'id' => (int)self::parseVal('data-playerid="([0-9]+)"', $cols[5]),
                'name' => self::parseVal('<h1>Player: <span>([0-9\w\s_-]+)</span></h1>', $cols[5]),
                'rank' => self::parsePlayerRank($cols[5]),
                'states' => self::parsePlayerStates($cols[5]),
                'alliance' => self::parsePlayerAlliance($cols[6]),
            ];
        }

        return [];
    }

    public static function parsePlayerRank($col)
    {
        $rank = 0;
        if (stristr($col, 'Ranking:')) {
            $rank = $col;
            $rank = substr_replace($rank, null, 0, stripos($rank, 'Ranking:') + 8);
            $rank = substr_replace($rank, null, stripos($rank, '</li>'));
            $rank = trim(strip_tags($rank));
        }

        return $rank;
    }

    public static function parsePlayerStates($col)
    {
        $str = $col;
        $str = substr_replace($str, null, 0, stripos($str, '<span class="status">'));
        $str = substr_replace($str, null, stripos($str, '<div') - 4);

        $states = [];
        if (stristr($str, 'status_abbr_admin')) {
            $states[] = 'A';
        }
        if (stristr($str, 'status_abbr_outlaw')) {
            $states[] = 'o';
        }
        if (stristr($str, 'status_abbr_vacation')) {
            $states[] = 'v';
        }
        if (stristr($str, 'status_abbr_banned')) {
            $states[] = 'b';
        }
        if (stristr($str, 'status_abbr_inactive')) {
            $states[] = 'i';
        }
        if (stristr($str, 'status_abbr_longinactive')) {
            $states[] = 'I';
        }
        if (stristr($str, 'status_abbr_honorableTarget')) {
            $states[] = 'hp';
        }

        return $states;
    }

    public static function parsePlayerAlliance($col)
    {
        if (!stristr($col, '<span class="allytagwrapper')) {
            return null;
        }
        $tag = $col;
        $tag = substr_replace($tag, null, 0, strpos($tag, '<span class="allytagwrapper'));
        $tag = substr_replace($tag, null, strpos($tag, '<div'));

        return [
            'id' => (int)self::parseVal('rel="alliance([0-9]+)"', $col),
            'tag' => trim(strip_tags($tag)),
            'name' => self::parseVal('<h1>([\w\s\d\.]+)</h1>', $col)
        ];
    }



    /** Messages */
    public static function parseMessages($text)
    {
        $text = substr_replace($text, null, 0, stripos($text, '</ul>') + 5);
        $text = substr_replace($text, null, stripos($text, '<ul '));
        $text = trim($text);

        $parse = self::parseMessagesItems($text);

        return $parse;

    }

    private static function parseMessagesItems(string $text)
    {
        $result = [
            'esp-action' => [],
            'esp-report' => [],
        ];
        $array = explode('</li>', $text);

        foreach ($array as $i => $row) {
            if (!trim($row)) {
                continue;
            }

            $type = self::getMessageType($row);
            // Espionage actions
            if ($type == 'esp-action') {
                $result[$type][] = self::getMessageEspAction($row);;
            } elseif ($type == 'esp-report') {
                $result[$type][] = self::getMessageEspReport($row);;
            }
        }

        return $result;
    }

    private static function getMessageType(string $row)
    {
        if (stristr($row, 'Espionage action on')) {
            return 'esp-action';
        } elseif (stristr($row, 'Espionage report from')) {
            return 'esp-report';
        }

        return null;
    }

    private static function getMessageEspAction(string $row)
    {
        $col1 = substr_replace($row, null, 0, strpos($row, 'A foreign fleet from'));
        $col1 = substr_replace($col1, null, strpos($col1, '</a>'));

        $coords = self::parseVal('\[([0-9:]+)\]', $col1);
        $playerName = substr_replace($row, null, 0, strpos($row, 'Player:') + 7);
        $playerName = substr_replace($playerName, null, strpos($playerName, '|'));

        $date = self::parseVal('msg_date fright">([0-9\s\.:]+)</span>', $row);
        // $dateFmt = date("Y-m-d H:i:s", strtotime($date));

        $result = [
            // 'msgId' => self::parseVal('data-msg-id="([0-9]+)"', $row),
            'coords' => $coords,
            'player' => trim($playerName),
            'playerId' => self::parseVal('data-playerId=&quot;([0-9]+)&quot;', $row),
            'date' => $date,
            // 'datetimeFmt' => $dateFmt,
        ];

        return $result;
    }

    private static function getMessageEspReport(string $row)
    {
        $col1 = substr_replace($row, null, 0, strpos($row, 'Espionage report from'));
        $col1 = substr_replace($col1, null, strpos($col1, '</a>'));

        $coords = self::parseVal('\[([0-9:]+)\]', $col1);
        $type = stristr($col1, 'planetIcon moon') ? 2 : 1;
        $apiStr = self::parseVal(" value='(sr-[a-z]+-[0-9]+-[A-z0-9]{20,})' ", $row);

        $level = 0;
        $res = null;
        $def = null;
        $fleet = null;
        if (stristr($row, 'Resources: ')) {
            $level = 1;
            $res = self::parseVal('Resources: ([0-9\.]+(Mn)?)<\/span>', $row);
        }
        if (stristr($row, 'Fleets: ')) {
            $level = 2;
            $fleet = self::parseVal('Fleets: ([0-9\.]+(Mn)?)<\/span>', $row);
        }
        if (stristr($row, 'Defence: ')) {
            $level = 3;
            $def = self::parseVal('Defence: ([0-9\.]+(Mn)?)<\/span>', $row);
        }

        $date = self::parseVal('msg_date fright">([0-9\s\.:]+)</span>', $row);
        $dateFmt = date("Y-m-d H:i:s", strtotime($date));

        $result = [
            // 'msgId' => self::parseVal('data-msg-id="([0-9]+)"', $row),
            'coords' => $coords,
            'type' => intval($type),
            'api' => $apiStr,
            'level' => $level,
            'res' => $res,
            'fleet' => $fleet,
            'def' => $def,
            // 'date' => $date,
            'dateFmt' => $dateFmt,
        ];

        return $result;
    }
}
