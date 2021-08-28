<?php

namespace Bitrock\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Symfony\Component\Console\Style\SymfonyStyle;

Loader::includeModule("highloadblock");

class HighloadModelCommand extends Command
{
    public CONST HIGHLOAD_NAME = 'hlname';

    protected static $defaultName = 'app:create-highload-model';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        global $USER_FIELD_MANAGER;
//        while(ob_get_length()){ob_end_clean();}echo("<pre>");print_r($USER_FIELD_MANAGER);echo("</pre>");die();
        $hlname = $input->getArgument(static::HIGHLOAD_NAME);
        $hlblock = HL::getList(['filter' => ['NAME' => $hlname]])->fetch();

        $io = new SymfonyStyle($input, $output);
        if (empty($hlblock)) {
            $io->error('Highload with name ' . $hlname . ' is not found');
            return Command::FAILURE;
        }
        $highloadUserType = HL::compileEntityId($hlblock['ID']);
        while(ob_get_length()){ob_end_clean();}echo("<pre>");print_r($hlblock);die();
        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this->setDescription('creates a new HighloadModel')
            ->addArgument(
                static::HIGHLOAD_NAME,
                InputArgument::REQUIRED,
                'highload table name'
            );
    }
}