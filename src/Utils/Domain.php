<?php

namespace Zilore\Utils;

use Symfony\Component\Console\Output\OutputInterface;
use Zilore\Api\Domains;

class Domain
{
    public static function ensure(array $specification, Domains $domains, OutputInterface $output): void
    {
        if (self::exists($specification['domain'], $domains)) {
            $output->writeln('<info>Domain exists in Zilore.</info>');
        } else {
            $output->writeln('<error>Domain does not exist in Zilore.</error>');
            $output->writeln('<comment>Creating domain ' . $specification['domain'] . ' in Zilore...</comment>');
            $domains->add($specification['domain']);
            $output->writeln('<info>Domain ' . $specification['domain'] . ' created in Zilore.</info>');
        }
    }
    public static function exists(string $domain, Domains $domains): bool
    {
        $domains = $domains->list();
        foreach ($domains as $d) {
            if ($d['domain_name'] == $domain) {
                return true;
            }
        }

        return false;
    }

}