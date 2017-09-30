<?php
/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/9/30
 * Time: 16:04
 */

namespace WechatRobot;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Console extends Command
{
    protected function configure()
    {
        $this->setName('init');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initStyle($output);

        $io = new SymfonyStyle($input, $output);

        $io->text('* 准备登录');

        $io->text('* 获取UUID');

        $login = new Login();
        $login->getUUID();

        $io->text('* 获取登录二维码');
        $qrcode = $login->getQrCodeByCli();
        $qrcode = $this->createCliQrCode($qrcode);

        foreach ($qrcode as $item){
            $output->writeln($item);
        }

        $io->newLine();

//        $io->text('* 扫码成功，请在手机点击登录');

    }

    private function initStyle($output)
    {
        $black = new OutputFormatterStyle('black', 'black');
        $output->getFormatter()->setStyle('black', $black);
        $white = new OutputFormatterStyle('white', 'white');
        $output->getFormatter()->setStyle('white', $white);
    }

    private function menu($io)
    {
        $io->listing([
            '1. 登录',
            '2. 注册',
            '3. 退出'
        ]);
        $io->ask('请选择', 1);
    }

    private function createCliQrCode($qrcode)
    {
        $data = [];
        foreach ($qrcode as $item) {
            $data[] = str_replace([0,1], ['<black>  </black>', '<white>  </white>'], $item);
        }

        return $data;
    }
}