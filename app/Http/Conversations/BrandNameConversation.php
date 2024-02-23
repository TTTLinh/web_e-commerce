<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Models\BrandModel;
use App\Models\ProductModel;

class BrandNameConversation extends Conversation
{
    public function run()
    {
        $this->askBrand();
    }

    public function askBrand()
    {
        $question = Question::create('Bạn muốn tìm kiếm sản phẩm theo không gian nào?');
        $this->ask($question, function (Answer $answer) {
            // Xử lý câu trả lời từ người dùng
            $brandName = $answer->getText();

            // Truy vấn cơ sở dữ liệu để tìm kiếm không gian theo tên
            $brand = BrandModel::where('brand_name', $brandName)->first();

            if ($brand) {
                // Hiển thị thông tin sản phẩm của không gian
                $this->showProductByBrand($brand);
                $this->askSearchAgain();
            } else {
                $this->bot->reply('Không tìm thấy không gian.');
                $this->askSearchAgain();
            }
        });
    }

    public function showProductByBrand($brand)
    {
        $products = $brand->product; // Sử dụng phương thức product()

        if ($products->count() > 0) {
            // Hiển thị thông tin của tất cả các sản phẩm trong không gian
            foreach ($products as $product) {
                $this->showProductInformation($product);
            }
        } else {
            $this->bot->reply('Không tìm thấy sản phẩm trong không gian này.');
        }
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
            "Danh mục: " . $categoryName . "<br>" .
            "Giảm giá: " . $product->product_sale . " %" . "<br>" .
            "Giá bán: " . $formattedPriceSell . " VNĐ" . "<br>" .
            "<img src='" . $imagePath . "' alt='Hình ảnh sản phẩm' style='width: 200px;'>"
        );
    }
    public function askSearchAgain()
    {
        $question = Question::create('Bạn có muốn tìm kiếm sản phẩm theo không gian khác không?(Có/Không)');
        $this->ask($question, function (Answer $answer) {
            $response = $answer->getText();

            if ($response === 'có' || $response === 'yes' || $response === 'Có' || $response === 'Yes') {
                // Nếu người dùng trả lời "có" hoặc "yes", chạy lại quá trình tìm kiếm
                $this->askBrand();
            } else {
                $this->bot->reply('Tiếp tục cuộc trò chuyện khác!');
            }
        });
    }
}