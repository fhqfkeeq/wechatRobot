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
        $userInfo = $user->init();
        if($userInfo === false){
            $io->error('* 发生未知错误，请重新登录');
            exit;
        }

        $io->text('* 获取联系人');

        $contactList = $user->getContact();

        $io->text('* 共获取到'.$contactList['MemberCount'].'位联系人');

        $message = new Message($BaseRequest, $data['pass_ticket'], $userInfo['SyncKey']);

        /* 发送消息 */

        while (true){
            $username = $io->ask('请输入要搜索的用户名', -1);

            if($username == -1){
                exit;
            }

            $contact = $user->searchByNickName($username);
            $showVlaue = [];
            foreach ($contact as $key => $item){
                $showVlaue[] = $key . '. ' . $item['nickname'];
            }

            $io->listing($showVlaue);

            $to = $io->ask('发送消息给？');

//            $message->sendMessage($userInfo['User']['UserName'], $contact[$to]['username']);
            $message->sendEmojiMessage($userInfo['User']['UserName'], $contact[$to]['username']);
        }


        /* 获取消息 */
        /*$io->text('* 开始监听消息');
        while (true){
            $isMessageSync = $message->syncCheck();

            if($isMessageSync === true){
                $newMessage = $message->messageSync();

                if($newMessage === false){
                    $io->error('* 发生未知错误，请重新登录');
                    exit;
                }

                if($newMessage['count'] > 0){
                    $io->text('* 监听到'.$newMessage['count'].'条新消息');
                    $fromInfo = $user->searchByUserName($item['FromUserName']);
                    foreach ($newMessage['list'] as $item){
                        $showData[] = 'Type：'.$item['MsgType'];
                        $showData[] = 'From: '. $fromInfo['NickName'];
                        $showData[] = $item['Content'];
                        $showData[] = '';
                    }
                    $io->listing($showData);
                }
            }
        }*/
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