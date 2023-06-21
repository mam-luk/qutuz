<?php

namespace Zilore\Utils;

use Symfony\Component\Console\Output\OutputInterface;
use Zilore\Api\GeoRecords;
use Zilore\Api\Records;

class GeoRecord
{
    public static function ensure(array $specification, GeoRecords $records, OutputInterface $output): void
    {
        $apiRecords = $records->list($specification['domain']);
        foreach ($specification['records'] as $r) {
            if (GeoRecord::exists($r, $apiRecords)) {
                $output->writeln('<info>Georecord ' . implode('::', [$r['type'], $r['name'], $r['value'], implode('|', $r['regions'])]) . ' exists in Zilore.</info>');
            } else {
                $output->writeln('<error>Georecord ' . $r['name'] . ' does not exist in Zilore.</error>');
                $output->writeln('<comment>Creating georecord ' . $r['name'] . ' in Zilore...</comment>');
                foreach ($r['regions'] as $region) {
                    $records->add($specification['domain'], $r['type'], $r['name'], $r['value'], $region);
                    $output->writeln('<info>Georecord ' . $r['name'] . '::' . $r['value'] . ' for  ' . $region . ' created in Zilore.</info>');
                }
            }
        }
    }

    public static function exists(array $record, array $recordsFromApi): bool
    {
        $res = false;
        foreach ($recordsFromApi as $r) {
            if ($r['record_type'] == $record['type'] &&
                $r['record_name'] == $record['name'] &&
                $r['record_value'] == $record['value'] &&
                $r['record_ttl'] == $record['ttl'] &&
                in_array($r['geo_region'], $record['regions'])
            ) {
                $res = true;
            }
        }

        return $res;
    }

}