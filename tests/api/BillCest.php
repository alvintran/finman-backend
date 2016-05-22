<?php

class BillCest
{
    protected $endpoint = '/bills';
    protected $token = '';

    public function _before(ApiTester $I)
    {
        $I->haveRecord('users', ['email' => 'tester@gmail.com', 'password' => bcrypt('testing'), 'name' => 'Tester']);
        $I->sendPOST('/login', ['email' => 'tester@gmail.com', 'password' => 'testing']);
        $I->seeResponseCodeIs(200);
        $token = $I->grabDataFromResponseByJsonPath('$.token');
        $this->token = !isset($token[0]) ? : $token[0];
    }

    public function getAllBills(ApiTester $I)
    {
        $id = (string) $this->haveBill($I, ['payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);
        $id2 = (string) $this->haveBill($I, ['payer_id' => '2', 'amount' => 90000, 'note' => 'Ăn tối']);

        $I->amBearerAuthenticated($this->token);
        $I->sendGET($this->endpoint);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->expect('both items are in response');
        $I->seeResponseContainsJson(['id' => $id, 'payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);
        $I->seeResponseContainsJson(['id' => $id2, 'payer_id' => '2', 'amount' => 90000, 'note' => 'Ăn tối']);
        $I->expect('both items are in root array');
        $I->seeResponseContainsJson([['id' => $id], ['id' => $id2]]);
    }

    public function getSingleBill(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);

        // Get a not exist Bill
        $I->sendGET($this->endpoint."/99999999");
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Bill not found');

        $id = (string) $this->haveBill($I, ['payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);
        $I->sendGET($this->endpoint."/$id");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['id' => $id, 'amount' => 70000, 'note' => 'Ăn sáng']);
        $I->expect('there is no root array in response');
        $I->dontSeeResponseContainsJson([['id' => $id]]);
    }

    public function createBill(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);

        // Validation fail
        $I->sendPOST($this->endpoint, ['payer_id' => 0, 'board_id' => 1, 'amount' => 0, 'note' => 'Ăn sáng', 'payees' => [1,2,3]]);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContains('The amount must be at least 500.');
        $I->seeResponseContains('The payer id must be at least 1.');

        $I->sendPOST($this->endpoint, ['payer_id' => 1, 'board_id' => 1, 'amount' => 67000, 'note' => 'Ăn sáng', 'payees' => []]);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContains('The payees must be at least 1 person.');

        // Validation success
        $I->sendPOST($this->endpoint, ['payer_id' => 1, 'board_id' => 1, 'amount' => 70000, 'note' => 'Ăn sáng', 'payees' => [1,2,3]]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);
        $id = $I->grabDataFromResponseByJsonPath('$..id')[0];
        $I->seeRecord('bills', ['id' => $id, 'payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);
        $I->sendGET($this->endpoint."/$id");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);
    }

    public function updateBill(ApiTester $I)
    {
        $id = (string) $this->haveBill($I, ['payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);

        $I->amBearerAuthenticated($this->token);
        $I->sendPUT($this->endpoint."/$id", ['payer_id' => '1', 'board_id' => '1', 'amount' => 75000, 'note' => 'Ăn sáng', 'payees' => [1,2,3]]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['payer_id' => '1', 'amount' => 75000, 'note' => 'Ăn sáng']);
        $I->seeRecord('bills', ['payer_id' => '1', 'amount' => 75000, 'note' => 'Ăn sáng']);
        $I->dontSeeRecord('bills', ['payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);
    }

    public function deleteBill(ApiTester $I)
    {
        $id = (string) $this->haveBill($I, ['payer_id' => '1', 'amount' => 70000, 'note' => 'Ăn sáng']);

        $I->amBearerAuthenticated($this->token);
        $I->sendDELETE($this->endpoint."/$id");
        $I->seeResponseCodeIs(204);
        $I->dontSeeRecord('bills', ['id' => $id]);
    }

    private function haveBill(ApiTester $I, $data = [])
    {
       $data = array_merge([
           'payer_id'    => '1',
           'board_id'    => '1',
           'category_id' => '1',
           'amount'      => '500',
           'note'        => 'Đi chợ mua đồ'
       ], $data);
       return $I->haveRecord('bills', $data);
    }
}
