<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UseVoucherTest extends TestCase
{
    /**
     * @test
     */
    function it_should_respond_winner_status_for_a_specific_phone_number()
    {
        $data = [
            'phoneNumber' => '09015262679',
            'code'        => '12345'
        ];

        $response = $this->post($this->getActionUrl('/use'), $data);

        $response->assertStatus(200);
        $response->assertExactJson([
            'isWinner' => true
        ]);

    }

    private function getActionUrl(string $action)
    {
        $action = ltrim($action, '/');

        return "api/campaign/voucher/{$action}";
    }
}
