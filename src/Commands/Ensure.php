<?php

namespace Zilore\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Yaml\Yaml;
use Zilore\Api\Domains;
use Zilore\Api\Records;
use Zilore\Api\GeoRecords;
use Zilore\Api\FailoverRecords;
use Zilore\Utils\Domain;
use Zilore\Utils\FailoverRecord;
use Zilore\Utils\GeoRecord;
use Zilore\Utils\Record;


class Ensure extends Command
{
    private string $apiKey;
    private string $file;

    private array $specification;

    protected Domains $domains;
    protected Records $records;
    protected GeoRecords $geoRecords;
    protected FailoverRecords $failoverRecords;

    protected function configure()
    {
        $this->setName("dns:ensure")
            ->setDescription("Ensures that the domain exists as defined in the passed YAML file")
            ->addArgument('key', InputArgument::REQUIRED, 'Your Zilore API Key')
            ->addArgument('file', InputArgument::REQUIRED, 'The file which contains the DNS records you want in Zilore');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->apiKey = $input->getArgument('key');
            $this->file = $input->getArgument('file');
            $this->specification = Yaml::parseFile($this->file);
            $this->setUpZilore();

            $output->writeln('Checking if the domain ' . $this->specification['domain'] . ' exists in Zilore...');
            Domain::ensure($this->specification, $this->domains, $output);
            $output->writeln('<comment>Checking if the records in ' . $this->file . ' exist in Zilore...</comment>');
            Record::ensure($this->specification, $this->records, $output);
            $output->writeln('<comment>Checking if the georecords in ' . $this->file . ' exist in Zilore...</comment>');
            GeoRecord::ensure($this->specification, $this->geoRecords, $output);
            $output->writeln('<comment>Checking if the failover in ' . $this->file . ' exist in Zilore...</comment>');
            FailoverRecord::ensure($this->specification, $this->failoverRecords, $output);
            $output->writeln('<info>Checking for any extra DNS records in Zilore that do not exist in your YAML...</info>');
            Record::deleteRecordsNotInYaml($this->specification, $this->records, $output);
            $output->writeln('<info>And that\'s a wrap. Thanks for using the Zilore DNS cli from Mamluk.</info>');

            return 0;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return 1;
        }

    }

    private function setUpZilore(): void
    {
        $this->domains = new Domains($this->apiKey);
        $this->records = new Records($this->apiKey);
        $this->geoRecords = new GeoRecords($this->apiKey);
        $this->failoverRecords = new FailoverRecords($this->apiKey);
    }
}
