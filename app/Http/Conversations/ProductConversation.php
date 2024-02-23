<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Models\ProductModel;

class ProductConversation extends Conversation
{
    public function run()
    {
        $this->askProduct();
    }

    public function askProduct()
    {
        $question = Question::create('Bạn muốn tìm kiếm sản phẩm gì?');
        $this->ask($question, function (Answer $answer) {
            // Xử lý câu trả lời từ người dùng
            $product = $answer->getText();

            // Truy vấn cơ sở dữ liệu để lấy danh sách sản phẩm
            $foundProducts = ProductModel::where('product_name', 'like', '%' . $product . '%')->get();

            if ($foundProducts->count() > 0) {
                // Hiển thị thông tin của tất cả các sản phẩm
                foreach ($foundProducts as $foundProduct) {
                    $this->showProductInformation($foundProduct);
                }
                $this->askSearchAgain();
            } else {
                $this->bot->reply('Không tìm thấy sản phẩm.');
                $this->askSearchAgain();
            }
        });
    }

    public function showProductInformation($product)
    {
        // Truy cập thông tin thương hiệu từ quan hệ "brand()"
        $brandName = $product->brand->brand_name;
        $categoryName = $product->category->category_name;
        // Hiển thị hình ảnh sản phẩm
        $imagePath = asset($product->product_image);
        $formattedPriceSell = number_format($product->product_price_sell, 0, ',', '.');

        // Hiển thị thông tin sản phẩm cho người dùng
        $this->bot->reply(
            "Tên: " . $product->product_name . "<br>" .
            "Không gian: " . $brandName . "<br>" .
            "Danh mục: " . $categoryName . "<br>" .
            "Giảm giá: " . $product->product_sale . " %" . "<br>" .
            "Giá bán: " . $formattedPriceSell . " VNĐ" . "<br>" .
            "<img src='" . $imagePath . "' alt='Hình ảnh sản phẩm' style='width: 200px;'>");
    }

    public function askSearchAgain()
    {
        $question = Question::create('Bạn có muốn tìm kiếm sản phẩm theo tên khác không?(Có/Không)');
        $this->ask($question, function (Answer $answer) {
            $response = $answer->getText();

            if ($response === 'có' || $response === 'yes' || $response === 'Có' || $response === 'Yes') {
                // Nếu người dùng trả lời "có" hoặc "yes", chạy lại quá trình tìm kiếm
                $this->askProduct();
            } else {
                $this->bot->reply('Tiếp tục cuộc trò chuyện khác!');
            }
        });
    }
}