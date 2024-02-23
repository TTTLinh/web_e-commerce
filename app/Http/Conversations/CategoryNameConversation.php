<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class CategoryNameConversation extends Conversation
{
    public function run()
    {
        $this->askCategory();
    }

    public function askCategory()
    {
        $question = Question::create('Bạn muốn tìm kiếm sản phẩm theo loại nào?');
        $this->ask($question, function (Answer $answer) {
            // Xử lý câu trả lời từ người dùng
            $categoryName = $answer->getText();

            // Truy vấn cơ sở dữ liệu để tìm kiếm loại sản phẩm theo tên
            $category = CategoryModel::where('category_name', $categoryName)->first();

            if ($category) {
                // Hiển thị thông tin sản phẩm của loại sản phẩm
                $this->showProductByCategory($category);
                $this->askSearchAgain();
            } else {
                $this->bot->reply('Tiếp tục cuộc trò chuyện khác!');
            }
        });
    }

    public function showProductByCategory($category)
    {
        $products = $category->product; // Sử dụng phương thức product()

        if ($products->count() > 0) {
            // Hiển thị thông tin của tất cả các sản phẩm trong loại sản phẩm
            foreach ($products as $product) {
                $this->showProductInformation($product);
            }
        } else {
            $this->bot->reply('Không tìm thấy sản phẩm trong loại sản phẩm này.');
            $this->askSearchAgain();
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
            "Không gian: " . $brandName . "<br>" .
            "Giảm giá: " . $product->product_sale . " %" . "<br>" .
            "Giá bán: " . $formattedPriceSell . " VNĐ" . "<br>" .
            "<img src='" . $imagePath . "' alt='Hình ảnh sản phẩm' style='width: 200px;'>"
        );
    }

    public function askSearchAgain()
    {
        $question = Question::create('Bạn có muốn tìm kiếm sản phẩm theo danh mục khác không?(Có/Không)');
        $this->ask($question, function (Answer $answer) {
            $response = $answer->getText();

            if ($response === 'có' || $response === 'yes' || $response === 'Có' || $response === 'Yes') {
                // Nếu người dùng trả lời "có" hoặc "yes", chạy lại quá trình tìm kiếm
                $this->askCategory();
            } else {
                $this->bot->reply('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
            }
        });
    }

}