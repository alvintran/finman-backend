<?php

class BoardCest
{
    protected $endpoint = '/boards';
    protected $token = '';

    public function _before(ApiTester $I)
    {
        $I->haveRecord('users', ['email' => 'tester@gmail.com', 'password' => bcrypt('testing'), 'name' => 'Tester']);
        $I->sendPOST('/login', ['email' => 'tester@gmail.com', 'password' => 'testing']);
        $I->seeResponseCodeIs(200);
        $token = $I->grabDataFromResponseByJsonPath('$.token');
        $this->token = !isset($token[0]) ? : $token[0];
    }

    public function getAllBoards(ApiTester $I)
    {
        $id = (string) $this->haveBoard($I, ['name' => 'Game of Thrones']);
        $id2 = (string) $this->haveBoard($I, ['name' => 'Lord of the Rings']);

        $I->amBearerAuthenticated($this->token);
        $I->sendGET($this->endpoint);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->expect('both items are in response');
        $I->seeResponseContainsJson(['id' => $id, 'name' => 'Game of Thrones', 'description' => 'description']);
        $I->seeResponseContainsJson(['id' => $id2, 'name' => 'Lord of the Rings', 'description' => 'description']);
        $I->expect('both items are in root array');
        $I->seeResponseContainsJson([['id' => $id], ['id' => $id2]]);
    }

    public function getSingleBoard(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);

        // Get a not exist Board
        $I->sendGET($this->endpoint."/99999999");
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Board not found');

        $id = (string) $this->haveBoard($I, ['name' => 'Starwars']);
        $I->sendGET($this->endpoint."/$id");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['id' => $id, 'name' => 'Starwars']);
        $I->expect('there is no root array in response');
        $I->dontSeeResponseContainsJson([['id' => $id]]);
    }

    public function createBoard(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);

        // Validation fail
        $I->sendPOST($this->endpoint, ['name' => '', 'description' => 'By Alvin']);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContains('The name field is required.');

        // Validation success
        $I->sendPOST($this->endpoint, ['name' => 'Game of Rings', 'description' => 'By George Tolkien']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Game of Rings']);
        $id = $I->grabDataFromResponseByJsonPath('$..id')[0];
        $I->seeRecord('boards', ['id' => $id, 'name' => 'Game of Rings']);
        $I->sendGET($this->endpoint."/$id");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Game of Rings']);
    }

    public function updateBoard(ApiTester $I)
    {
        $id = (string) $this->haveBoard($I, ['name' => 'Game of Thrones']);

        $I->amBearerAuthenticated($this->token);
        $I->sendPUT($this->endpoint."/$id", ['name' => 'Lord of Thrones']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['name' => 'Lord of Thrones']);
        $I->seeRecord('boards', ['name' => 'Lord of Thrones']);
        $I->dontSeeRecord('boards', ['name' => 'Game of Thrones']);
    }

    public function deleteBoard(ApiTester $I)
    {
        $id = (string) $this->haveBoard($I, ['name' => 'Game of Thrones']);

        $I->amBearerAuthenticated($this->token);
        $I->sendDELETE($this->endpoint."/$id");
        $I->seeResponseCodeIs(204);
        $I->dontSeeRecord('boards', ['id' => $id]);
    }

    private function haveBoard(ApiTester $I, $data = [])
    {
       $data = array_merge([
           'name' => 'Game of Thrones',
           'description' => 'description'
       ], $data);
       return $I->haveRecord('boards', $data);
    }
}
