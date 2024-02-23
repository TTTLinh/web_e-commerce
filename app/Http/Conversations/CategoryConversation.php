<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Models\CategoryModel;

class CategoryConversation extends Conversation
{
    public function run()
    {
        $this->showCategory();
    }

    public function showCategory()
    {
        $category = CategoryModel::pluck('category_name')->toArray();

        if (!empty($category)) {
            $message = "Các loại sản phẩm của cửa hàng:" . "<br>" ;
            $message .= implode("<br>", $category);
            $this->say($message);
        } else {
            $this->say("Không có loại sản phẩm nào được tìm thấy trong cơ sở dữ liệu.");
        }
    }
}