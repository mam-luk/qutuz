<?php

namespace Zilore\Utils;

use Symfony\Component\Console\Output\OutputInterface;
use Zilore\Api\FailoverRecords;
use Zilore\Api\GeoRecords;
use Zilore\Api\Records;

class FailoverRecord
{
    public static function ensure(array $specification, FailoverRecords $records, OutputInterface $output): void
    {
        $apiRecords = $records->list($specification['domain']);
        $output->writeln('<info>Found ' . count($apiRecords) . ' failover records in Zilore. Let\'s delete them all and recreate the ones in your YAML.</info>');
        $frids = [];
        foreach ($apiRecords as $record) {
            $output->writeln('<comment>Deleting failover record ' . implode('::', [$record['record_name'], $record['record_id']]) . ' in Zilore...</comment>');
            $frids[] = $record['record_id'];
        }

        if (!empty($frids)) {
            $records->delete($specification['domain'], implode(',', $frids));
        }

        $output->writeln('<info>Checking if we can add any failover records in Zilore...</info>');
        $qualifyingRecords = $records->all($specification['domain'])['failover_records'];
        $qrCount = count($qualifyingRecords);
        $output->writeln('<info>' . $qrCount . ' records found..</info>');

        foreach($qualifyingRecords as $qr) {
            // Find matching record in specification
            foreach ($specification['records'] as $sr) {
                $fta = self::getFailoverToAdd($sr, $qr);
                //$output->writeln('<error>' . implode('::', $qr) . '</error>');
                if (!empty($fta)) {
                    $output->writeln('<comment>Adding Failover Record for ' . implode('::', $qr). '</comment>');
                    $records->add(
                        $specification['domain'],
                        $qr['record_id'],
                        $fta['check']['type'],
                        $fta['check']['interval'],
                        $fta['check']['path'],
                        $fta['values'],
                        implode(',', $fta['notification']['emails']),
                        implode(',', $fta['notification']['sms'])
                    );
                    $output->writeln('<info>Added!</info>');
                }
            }
        }
    }

    private static function getFailoverToAdd(array $sr, array $fr): array
    {

        if (
            $sr['name'] == $fr['record_name']
            && $sr['type'] == $fr['record_type']
            && $sr['value'] == $fr['record_value']
            && $sr['ttl'] == $fr['record_ttl']
            && in_array($fr['geo_region'], $sr['regions'])
        ) {
            return $sr['failover'];
        }
        return [];
    }

}