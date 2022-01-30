<?php 
namespace Armincms\EasyLicense\Mails;

use Armincms\Arminpay\Order;
use Illuminate\Bus\Queueable; 
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Credit extends Mailable/* implements ShouldQueue*/
{
    use Queueable, SerializesModels;

    /**
     * The credits instances.
     *
     * @var Order
     */
    public $credits;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($credits)
    {
        $this->credits = $credits;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {    
        return $this  
                ->subject(__("License Purchase Detail"))
                ->markdown('easy-license::emails.credit', ['credits' => $this->credits]);
    }
}