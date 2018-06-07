<?php

namespace apps\crontab\commands;

use mix\console\ExitCode;
use mix\facades\Input;
use mix\facades\Output;

/**
 * Clear 命令
 * @author 刘健 <coder.liu@qq.com>
 */
class ClearCommand extends BaseCommand
{

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize(); // TODO: Change the autogenerated stub
        // 获取程序名称
        $this->programName = Input::getCommandName();
    }

    // 执行任务
    public function actionExec()
    {
        // 预处理
        parent::actionExec();

        // 模型内使用短连接版本的数据库组件，计划任务都是一次性执行
        $tableModel = new \apps\common\models\TableModel();
        // 执行业务代码
        // ...

        // 响应
        Output::writeln('SUCCESS');
        // 返回退出码
        return ExitCode::OK;
    }

}
