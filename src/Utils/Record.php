<?php

namespace Zilore\Utils;

use Symfony\Component\Console\Output\OutputInterface;
use Zilore\Api\Records;

class Record
{
    public static function ensure(array $specification, Records $records, OutputInterface $output): void
    {
        $apiRecords = $records->list($specification['domain']);
        foreach ($specification['records'] as $r) {
            if ($apiRecordId = Record::exists($r, $apiRecords)) {
                $output->writeln('<info>Record ' . implode('::', [$r['type'], $r['name'], $r['value'] ]) . ' exists in Zilore.</info>');
                if (!self::ttlMatches($r, $apiRecordId, $apiRecords)) {
                    $output->writeln('<error>Record ' . implode('::', [$r['type'], $r['name'], $r['value'] ]) . ' has a different TTL. Ensuring it matches...</error>');
                    $records->update($specification['domain'], $apiRecordId, $r['type'], $r['name'], $r['value'], $r['ttl']);
                    $output->writeln('<info>Record ' . implode('::', [$r['type'], $r['name'], $r['value'] ]) . ' TTL updated.</info>');
                }
            } else {
                $output->writeln('<error>Record ' . $r['name'] . ' does not exist in Zilore.</error>');
                $output->writeln('<comment>Creating record ' . $r['name'] . ' in Zilore...</comment>');
                $records->add($specification['domain'], $r['type'], $r['name'], $r['value'], $r['ttl']);
                $output->writeln('<info>Record ' . $r['name'] . ' created in Zilore.</info>');
            }
        }
    }
    public static function exists(array $record, array $recordsFromApi): ?int
    {
        foreach ($recordsFromApi as $r) {
            if ($r['record_type'] == $record['type'] &&
                $r['record_name'] == $record['name'] &&
                $r['record_value'] == $record['value']
            //    $r['record_ttl'] == $record['ttl']
            ) {
                return $r['record_id'];
            }
        }

        return null;
    }

    public static function ttlMatches(array $specificationRecord, int $apiRecordId, array $recordsFromApi): bool
    {
        foreach ($recordsFromApi as $r) {
            if ($r['record_id'] == $apiRecordId) {
                return $r['record_ttl'] == $specificationRecord['ttl'];
            }
        }

        return false;
    }

    public static function deleteRecordsNotInYaml(array $specification, Records $records, OutputInterface $output): void
    {
        // TODO: Optimise this. It's bad.
        $toKeep = [];
        $apiRecords = $records->list($specification['domain']);
        foreach ($apiRecords as $ar) {
            foreach ($specification['records'] as $sr) {;
                if (
                    $ar['record_type'] == $sr['type'] &&
                    $ar['record_name'] == $sr['name'] &&
                    $ar['record_value'] == $sr['value']
                ) {
                    $toKeep[] = $ar['record_id'];
                }
            }
        }

        foreach ($apiRecords as $xr) {
            if (!in_array($xr['record_type'], ['SOA', 'NS'])  && !in_array($xr['record_id'], $toKeep)) {
                $output->writeln('<error>Record ' . implode('::', [$xr['record_type'], $xr['record_name'], $xr['record_value']]) . ' exists in Zilore but not in YAML. Deleting...</error>');
                $records->delete($specification['domain'], $xr['record_id']);
                $output->writeln('<info>Record ' . implode('::', [$xr['record_type'], $xr['record_name'], $xr['record_value'] ]) . ' deleted.</info>');
            }
        }
    }

}