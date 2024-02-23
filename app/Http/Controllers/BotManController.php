<?php
namespace App\Http\Controllers;
   
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Conversations\StartConversation;
use App\Http\Conversations\ProductConversation;
use App\Http\Conversations\BrandConversation;
use App\Http\Conversations\BrandNameConversation;
use App\Http\Conversations\CategoryConversation;
use App\Http\Conversations\CategoryNameConversation;
use App\Http\Conversations\PriceConversation;


class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle(Request $request)
    {

        $botman = app('botman');

        // Thêm middleware Dialogflow
        $dialogflow = \BotMan\Middleware\DialogFlow\V2\DialogFlow::create('en');
        $botman->middleware->received($dialogflow);

        $botman->hears('smalltalk.(.*)', function ($bot) {
            $extras = $bot->getMessage()->getExtras();
            // $bot->reply($extras['apiReply']);
            if ($extras['apiReply'] == 'tìm kiếm sản phẩm' )
            {

                $bot->startConversation(new ProductConversation());  

            }elseif($extras['apiReply'] == 'không gian gồm' )
            {
                
                $bot->startConversation(new BrandConversation());

            }elseif($extras['apiReply'] == 'sản phẩm không gian gồm' )
            {
                
                $bot ->startConversation(new BrandNameConversation());

            }elseif($extras['apiReply'] == 'loại sản phẩm gồm' )
            {
                
                $bot ->startConversation(new CategoryConversation());

            }elseif($extras['apiReply'] == 'sản phẩm theo loại sản phẩm' )
            {
                
                $bot ->startConversation(new CategoryNameConversation());

            }elseif($extras['apiReply'] == 'sản phẩm trong khoảng tiền' )
            {
                
                $bot ->startConversation(new PriceConversation());

            }else {
                //get reply from dialogflow
                $bot->reply($extras['apiReply']);
            }           
        })->middleware($dialogflow);;

        $botman->listen();
    }

}