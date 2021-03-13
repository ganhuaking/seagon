<?php

namespace App\Http\Controllers\Line;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\Laravel\Facade\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

/**
 * @see https://github.com/line/line-bot-sdk-php
 */
class Messaging
{
    public function __invoke(Request $request)
    {
        Log::debug('Request content: ' . json_encode($request->all()));

        $text = trim($request->input('events.0.message.text'));

        if (preg_match('/師公！/', $text)) {
            $textMessageBuilder = new TextMessageBuilder("想聽師公講什麼嗎？請輸入下面關鍵字讓師公來講講幹話\n\n1. 師公語錄\n2. 師公第一人提出");

            $response = LINEBot::replyMessage($request->input('events.0.replyToken'), $textMessageBuilder);

            if ($response->isSucceeded()) {
                return 'Succeeded!';
            }

            Log::error('LINE return error: ' . $response->getRawBody());
        }

        if ('師公語錄' === $text) {
            $textMessageBuilder = new TextMessageBuilder($this->no());

            $response = LINEBot::replyMessage($request->input('events.0.replyToken'), $textMessageBuilder);

            if ($response->isSucceeded()) {
                return 'Succeeded!';
            }

            Log::error('LINE return error: ' . $response->getRawBody());
        }

        if ('師公第一人提出' === $text) {
            $textMessageBuilder = new TextMessageBuilder($this->randomGanhua());

            $response = LINEBot::replyMessage($request->input('events.0.replyToken'), $textMessageBuilder);

            if ($response->isSucceeded()) {
                return 'Succeeded!';
            }

            Log::error('LINE return error: ' . $response->getRawBody());
        }
    }

    private function randomGanhua(): string
    {
        $part1 = collect([
            '精益',
            '飛輪',
            '斜槓',
            '敏捷',
            '長尾',
            '變種',
            '稀缺',
            '創新',
            '循環',
            '多元',
        ])->random();

        $part2 = collect([
            '價值',
            '人脈',
            '中心',
            '結合',
            '軟體',
            '知識',
            '流量',
            '數據',
            '老闆',
            '複利',
            '技能',
            '副業',
            'TDD',
            'BDD',
            'DDD',
            'github',
            'gitlab',
            'gitflow',
            '自動化',
            'DNA',
        ])->random();

        $part3 = collect([
            '變現',
            '素養',
            '管理',
            '理論',
            '品牌',
            '創業',
            '思維',
            '思想',
            '產品',
            '看板',
            '效應',
            '功力',
            '武功',
            '轉型',
            '進化',
            '整合',
            '投資',
            '工程師',
            '企業家',
            '創業家',
            '顧問',
            '一人公司',
        ])->random();

        return "{$part1}{$part2}{$part3}";
    }

    private function no(): string
    {
        return collect([
            '待產業待久了，累積domain knowledge，撐得下去，就不知道陣亡多少個了...能否把domain knowledge轉換成內化，甚至變成帶者走的DNA或便成自己的變現力和創造力，可就難了...
一般開發跟需求或業務單位，以為開發完熟悉整個就是自己的東西了...事實上這個倒是有很大問題在...
真正在做銷售或是行銷模組的時候，應該多跟業務學習，它們到底是怎麼做銷售甚至是銷售心理學，能夠把業務的銷售DNA和成交的DNA慢慢融入到自己的應用系統上，當學到Flow這一塊，如果能把其他部門的DNA和精隨融入進去，那才是帶者走的東西...',
            '必須要結合各派不同所長的結合起來，若結合自己本身的個性，威力就會差很多了...必須要結合各派不同所長的結合起來，若結合自己本身的個性，威力就會差很多了...就像是天龍八部鳩摩智聰慧過人，但為人高傲自負，癡迷武學，狂熱追求至高武功。後來，他因錯學少林72絕技，走火入魔，導致內息大亂膨脹...',
            '以敏捷角度我會從運動的角度來看，scrum就是從橄欖隊思維誕生，如果有打過球隊甚至是比賽，可以把運動的DNA融入在敏捷當中，舉個例子scrum master可以把教練式領導，用在scrum master身上，因為球隊求勝利跟打好比賽是不一樣的!
所以scrum master是在跑scrum來說是相當重要的，當然是scrum它只是一個框架，還必須搭配很多工程技術，其他方面的來強化! 它是對我來說這兩個是有很大落差，一個是互動合作比例為重、另一個則是追求價值、精益求精，一個是可以把用在團隊上發揮最大的威力，另一個則是在個人甚至團體上可以發揮威力，所以這兩個是可以交互也可以合併融合...我的見解是這樣',
            "我印象中有共事的主管，聽我在講未來技術上的戰略和局勢發展，保持謙虛為懷的，還在我面前抄筆記的主管願意學習的很稀少...我印象中有一個主管在跟我聊的時候，我提到策略變現  持續性收入 經濟訂閱的戰略  光願意寫筆記的主管很不容易了...通常聽到這樣的戰略走向，都會直覺說不可能、甚至抗拒的...所以還是要看有沒有慧根...有慧根的自然聽得懂、沒慧根就透過觀察連提也不用提了!\n其實也是在暗示它，這些策略不但要用在公司，同時也要用在自己身上!",
            "精益軟體開發、精益創業、精益需求、精益創新，以精益為核心，這核心思想跟飛輪效應很類似也可以結合起來...
精益商業思維：回歸常識、保持聚焦
這不就是以一拳全力鍛鍊且聚焦起來，再延伸出不同的新變化，以簡單的招數打出高殺傷力的威力!\n\n其實價值投資也是可以套用這個圈圈裏面，簡簡單單一招打出巨大複利威力",
        ])->random();
    }

}
