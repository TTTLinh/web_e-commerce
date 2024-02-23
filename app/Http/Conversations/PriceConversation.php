<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\DB;
use App\Models\ProductModel;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;


class PriceConversation extends Conversation
{
    public function run()
    {
        $this->askPriceRange();
    }

    public function askPriceRange()
    {
        $this->ask('Xin vui lòng nhập khoảng giá bạn quan tâm (Ví dụ: 100000-500000):', function ($answer) {
            // Lấy câu trả lời từ người dùng
            $priceRange = $answer->getText();

            // Xử lý khoảng giá và tìm kiếm sản phẩm trong khoảng giá
            $prices = explode('-', $priceRange);
            $minPrice = intval($prices[0]);
            $maxPrice = intval($prices[1]);

            $products = ProductModel::whereBetween('product_price_sell', [$minPrice, $maxPrice])
            ->get();

            if ($products->count() > 0) {
                // Hiển thị thông tin của tất cả các sản phẩm
                foreach ($products as $product) {
                    $this->showSearchResults($products);
                }
                $this->askSearchAgain();
            } else {
                $this->bot->reply('Không tìm thấy sản phẩm với khoảng tiền đó.');
                $this->askSearchAgain();
            }
            // Hiển thị kết quả cho người dùng
        });
    }

    public function showSearchResults($results)
    {
        foreach ($results as $product) {
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
    }

    public function askSearchAgain()
    {
        $question = Question::create('Bạn có muốn tìm kiếm sản phẩm theo khoảng giá khác không?(Có/Không)');
        $this->ask($question, function (Answer $answer) {
            $response = $answer->getText();

            if ($response === 'có' || $response === 'yes' || $response === 'Có' || $response === 'Yes') {
                // Nếu người dùng trả lời "có" hoặc "yes", chạy lại quá trình tìm kiếm
                $this->askPriceRange();
            } else {
                $this->bot->reply('Tiếp tục cuộc trò chuyện khác!');
            }
        });
    }
}