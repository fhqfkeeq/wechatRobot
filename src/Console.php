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

        foreach ($qrcode as $item) {
            $output->writeln($item);
        }

        $io->newLine();

        $scan = $login->listenScan();
        if ($scan === false) {
            $io->error('扫码失败，请重试');
            exit;
        }

        $io->text('* 扫码成功，请在手机点击登录');
        $redirect_uri = $login->listenClick();

        if ($redirect_uri === false) {
            $io->error('登录失败，请重试');
            exit;
        }

        $io->text('* 登录成功，开始获取用户信息');

        $data = $login->getLoginInfo($redirect_uri);

        $BaseRequest = [
            'Uin' => $data['wxuin'],
            'Sid' => $data['wxsid'],
            'Skey' => $data['skey'],
            'DeviceID' => 'e' . substr(md5(uniqid()), 2, 15)
        ];

        $user = new User($BaseRequest, $data['pass_ticket']);
        $user->init();

        switch ($this->menu($io)) {
            case '1':
                $info = $user->getContact();
                echo '获取成功';
                break;
            default:
                echo 'aaa';
                break;
        }

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
            '1. 获取联系人列表',
            '2. 注册',
            '3. 退出'
        ]);
        return $io->ask('请选择', 1);
    }

    private function createCliQrCode($qrcode)
    {
        $data = [];
        $len = strlen($qrcode[0]);

        $padding = str_repeat('<white>  </white>', $len * 2);

        for ($i = 0; $i < 5; $i++) {
            $data[] = $padding;
        }

        foreach ($qrcode as $item) {
            $data[] = str_replace([1, 0], ['<black>  </black>', '<white>  </white>'], $item);
        }

        for ($i = 0; $i < 5; $i++) {
            $data[] = $padding;
        }

        return $data;
    }
}