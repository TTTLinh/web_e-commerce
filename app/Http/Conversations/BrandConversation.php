<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Models\BrandModel;

class BrandConversation extends Conversation
{
    public function run()
    {
        $this->showBrand();
    }

    public function showBrand()
    {
        $brands = BrandModel::pluck('brand_name')->toArray();

        if (!empty($brands)) {
            $message = "Các không gian của cửa hàng:" . "<br>" ;
            $message .= implode("<br>", $brands);
            $this->say($message);
        } else {
            $this->say("Không có không gian nào được tìm thấy trong cơ sở dữ liệu.");
        }
    }
}