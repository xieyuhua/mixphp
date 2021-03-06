<?php

namespace apps\console\commands;

use mix\console\ExitCode;
use mix\facades\Input;
use mix\task\CenterWorker;
use mix\task\LeftWorker;
use mix\task\ProcessPoolTaskExecutor;

/**
 * 推送模式范例
 * @author 刘健 <coder.liu@qq.com>
 */
class PushCommand extends BaseCommand
{

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize(); // TODO: Change the autogenerated stub
        // 获取程序名称
        $this->programName = Input::getCommandName();
    }

    /**
     * 获取服务
     * @return ProcessPoolTaskExecutor
     */
    public function getTaskService()
    {
        return create_object(
            [
                // 类路径
                'class'         => 'mix\task\ProcessPoolTaskExecutor',
                // 服务名称
                'name'          => "mix-daemon: {$this->programName}",
                // 执行模式
                'mode'          => ProcessPoolTaskExecutor::MODE_PUSH,
                // 左进程数
                'leftProcess'   => 1,
                // 中进程数
                'centerProcess' => 5,
                // 最大执行次数
                'maxExecutions' => 16000,
                // 队列名称
                'queueName'     => __FILE__,
            ]
        );
    }

    // 执行任务
    public function actionExec()
    {
        // 预处理
        parent::actionExec();
        // 启动服务
        $service = $this->getTaskService();
        $service->on('LeftStart', [$this, 'onLeftStart']);
        $service->on('CenterStart', [$this, 'onCenterStart']);
        $service->on('CenterMessage', [$this, 'onCenterMessage']);
        $service->start();
        // 返回退出码
        return ExitCode::OK;
    }

    // 左进程启动事件
    public function onLeftStart(LeftWorker $worker)
    {
        // 模型内使用长连接版本的数据库组件，这样组件会自动帮你维护连接不断线
        $tableModel = new \apps\common\models\TableModel();
        // 取出全量数据一行一行推送给中进程去处理
        foreach ($tableModel->getAll() as $item) {
            // 将消息发送给中进程去处理，消息有长度限制 (https://wiki.swoole.com/wiki/page/290.html)
            $worker->send($item);
        }
    }

    // 中进程启动事件
    public function onCenterStart(CenterWorker $worker)
    {
        // 可以在这里实例化一些对象，供 onCenterMessage 中使用，这样就不需要重复实例化。
    }

    // 中进程消息事件
    public function onCenterMessage(CenterWorker $worker, $data)
    {
        // 处理消息，比如：发送短信、发送邮件、微信推送
        // ...
    }

}
